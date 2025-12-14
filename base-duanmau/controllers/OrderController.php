<?php
include_once './config/db_connection.php';
include_once __DIR__ . '/../models/OrderModel.php';
include_once __DIR__ . '/../models/CartModel.php';

class OrderController
{
    public function process()
    {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $fullname = $_POST['fullname'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $address = $_POST['address'] ?? '';
        $note = $_POST['note'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? 'COD';

        $shippingFee = (int)($_POST['shipping_fee'] ?? 0);
        $discountAmount = (int)($_POST['discount_amount'] ?? 0);
        $couponCode = $_POST['coupon_code'] ?? '';

        $userId = $_SESSION['user_id'] ?? null;

        // Lấy danh sách sản phẩm được chọn từ form hoặc session (nếu có)
        $selectedItemsJson = $_POST['selected_items'] ?? $_SESSION['checkout_selected_items'] ?? '';
        $selectedItems = [];
        if (!empty($selectedItemsJson)) {
            $selectedItems = json_decode($selectedItemsJson, true) ?? [];
        }
        
        // Xóa selected_items khỏi session sau khi đã sử dụng
        unset($_SESSION['checkout_selected_items']);

        // Lấy toàn bộ giỏ hàng
        $cartData = CartModel::getCartContents($pdo);
        $allCartItems = $cartData['items'];
        
        // Nếu có sản phẩm được chọn, chỉ lấy các sản phẩm đó
        if (!empty($selectedItems)) {
            $cartItems = [];
            $subtotal = 0;
            
            foreach ($allCartItems as $item) {
                foreach ($selectedItems as $selected) {
                    if ($item['product_id'] == $selected['product_id'] && 
                        ($item['variant_id'] ?? 0) == ($selected['variant_id'] ?? 0)) {
                        // Cập nhật số lượng từ danh sách được chọn
                        $item['quantity'] = $selected['quantity'] ?? $item['quantity'];
                        $item['item_total'] = $item['price'] * $item['quantity'];
                        $cartItems[] = $item;
                        $subtotal += $item['item_total'];
                        break;
                    }
                }
            }
        } else {
            // Nếu không có sản phẩm nào được chọn, lấy tất cả
            $cartItems = $allCartItems;
            $subtotal = $cartData['subtotal'];
        }

        if (empty($cartItems)) {
            echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php?class=cart&act=cart';</script>";
            return;
        }

        $totalAmount = $subtotal + $shippingFee - $discountAmount;
        if ($totalAmount < 0) $totalAmount = 0;

        $fullShippingInfo = "Người nhận: $fullname | Email: $email | SĐT: $phone | Đ/c: $address";
        if (!empty($note)) {
            $fullShippingInfo .= " | Ghi chú: $note";
        }

        try {
            if ($pdo->inTransaction()) {
                // Nếu còn transaction cũ (không hợp lệ), rollback để bắt đầu phiên mới
                $pdo->rollBack();
            }
            $pdo->beginTransaction();

            if ($userId) {
                $stmt = $pdo->prepare("SELECT phone FROM user WHERE user_id = ?");
                $stmt->execute([$userId]);
                $currentPhone = $stmt->fetchColumn();

                if (empty($currentPhone) && !empty($phone)) {
                    OrderModel::updateUserPhone($pdo, $userId, $phone);
                }
                if (!empty($address)) {
                    CartModel::updateUserAddress($pdo, $userId, $address);
                }
            }

            // Validate coupon trước khi tạo đơn hàng
            $couponId = null;
            if (!empty($couponCode)) {
                $couponValidation = OrderModel::validateAndApplyCoupon($pdo, $couponCode, $subtotal);
                if (!$couponValidation['valid']) {
                    echo "<script>alert('" . $couponValidation['message'] . "'); window.history.back();</script>";
                    return;
                }
                $couponId = $couponValidation['coupon_id'];
                // Cập nhật lại discount amount từ validation
                $discountAmount = $couponValidation['discount'];
                $totalAmount = $subtotal + $shippingFee - $discountAmount;
            }

            $orderId = OrderModel::createOrder(
                $pdo,
                $userId,
                $couponId,
                $totalAmount,
                $fullShippingInfo,
                $shippingFee,
                $discountAmount
            );

            foreach ($cartItems as $item) {
                $variantId = $item['variant_id'] ?? 0;
                OrderModel::addOrderDetail($pdo, $orderId, $item['product_id'], $variantId, $item['quantity'], $item['price']);
                OrderModel::reduceStock($pdo, $item['product_id'], $variantId, $item['quantity']);
            }

            OrderModel::createPayment($pdo, $orderId, $paymentMethod, $totalAmount);

            // Với VNPay: KHÔNG xóa giỏ hàng ngay, chỉ xóa khi thanh toán thành công
            // Với COD: Xóa giỏ hàng ngay
            if ($paymentMethod !== 'VNPAY') {
                // Chỉ xóa các sản phẩm đã được chọn khỏi giỏ hàng
                if (!empty($selectedItems)) {
                    CartModel::removeSelectedItems($selectedItems, $userId);
                } else {
                    // Nếu không có danh sách chọn, xóa toàn bộ giỏ hàng
                    OrderModel::clearCart($pdo, $userId);
                }
            }

            $pdo->commit();

            if ($paymentMethod === 'VNPAY') {
                require_once("./config/config_vnpay.php");

                // Mỗi lần thanh toán tạo mã giao dịch duy nhất để tránh VNPAY báo trùng
                $vnp_TxnRef = $this->generateTxnRef($orderId);
                $vnp_OrderInfo = 'Thanh toan don hang #' . $orderId;
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = $totalAmount * 100;
                $vnp_Locale = 'vn';
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date("YmdHis"),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef
                );

                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }

                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }

                header('Location: ' . $vnp_Url);
                exit;
            }

            header("Location: index.php?class=order&act=success&id=$orderId");
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo "Có lỗi xảy ra: " . $e->getMessage();
        }
    }

    public function vnpay_return()
    {
        global $pdo;
        require_once("./config/config_vnpay.php");

        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        // vnp_TxnRef có dạng "{orderId}-{timestamp}" để tránh trùng giao dịch
        $orderRef = $_GET['vnp_TxnRef'];
        $orderId = intval(explode('-', $orderRef)[0]);

        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                // --- TRƯỜNG HỢP 1: THANH TOÁN THÀNH CÔNG ---

                // 1. Cập nhật bảng PAYMENT -> COMPLETED
                $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'COMPLETED', payment_method = 'VNPAY' WHERE order_id = ?");
                $stmt->execute([$orderId]);

                // 2. Cập nhật bảng ORDER -> PREPARING 
                // (Tự động xác nhận đơn hàng, bỏ qua bước PENDING)
                $stmtOrder = $pdo->prepare("UPDATE `order` SET order_status = 'PREPARING' WHERE order_id = ?");
                $stmtOrder->execute([$orderId]);

                // 3. Xóa khỏi giỏ hàng đúng các sản phẩm đã mua
                $this->removePurchasedItemsFromCart($orderId);

                // 4. Chuyển hướng về trang thành công
                header("Location: index.php?class=order&act=success&id=$orderId");
                exit;
            } else {
                // --- TRƯỜNG HỢP 2: THANH TOÁN THẤT BẠI / HỦY ---

                // 1. Cập nhật bảng PAYMENT -> FAILED (Để Admin biết khách đã thử thanh toán nhưng lỗi)
                $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'FAILED', payment_method = 'VNPAY' WHERE order_id = ?");
                $stmt->execute([$orderId]);

                // 2. Trạng thái đơn hàng (order_status) GIỮ NGUYÊN LÀ 'PENDING'
                // (Để khách có thể thử thanh toán lại hoặc chọn phương thức khác nếu code hỗ trợ, hoặc Admin tự hủy sau)

                echo "<div style='text-align:center; margin-top:50px; font-family: Arial, sans-serif;'>
                        <h2 style='color:#dc3545;'>Thanh toán thất bại!</h2>
                        <p>Giao dịch đã bị hủy hoặc xảy ra lỗi trong quá trình xử lý.</p>
                        <p>Mã đơn hàng: <b>#$orderId</b> vẫn đang ở trạng thái Chờ xử lý.</p>
                        <div style='margin-top: 20px;'>
                            <a href='index.php' style='text-decoration: none; padding: 10px 20px; background: #6c757d; color: white; border-radius: 5px; margin-right: 10px;'>Về trang chủ</a>
                            <a href='index.php?class=order&act=continue_payment&id=$orderId' style='text-decoration: none; padding: 10px 20px; background: #0d6efd; color: white; border-radius: 5px;'>Tiếp tục thanh toán</a>
                        </div>
                      </div>";
            }
        } else {
            echo "<div style='text-align:center; margin-top:50px; color:red;'>
                    <h3>Lỗi bảo mật: Chữ ký không hợp lệ (Invalid Signature)</h3>
                  </div>";
        }
    }

    public function success()
    {
        global $pdo;
        $orderId = $_GET['id'] ?? 0;
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/order_success.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function confirm_receipt()
    {
        global $pdo;
        $orderId = $_GET['id'] ?? 0;

        // 1. Nhận token từ link (Hỗ trợ cả tên tham số 'token' cũ và 'login_token' mới)
        $receivedToken = $_GET['login_token'] ?? $_GET['token'] ?? '';

        if (empty($receivedToken)) {
            die('Link không hợp lệ (Thiếu token).');
        }

        // 2. CÁCH 1: Tìm Token trong Database (Dành cho Thành viên - Token ngẫu nhiên)
        // Logic: Nếu tìm thấy token này trong bảng User -> Tự động đăng nhập
        $stmt = $pdo->prepare("SELECT user_id, full_name, email FROM user WHERE one_time_token = ?");
        $stmt->execute([$receivedToken]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // A. Đăng nhập tự động
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];

            // B. Hủy token ngay (để không dùng lại được link này)
            $stmtDel = $pdo->prepare("UPDATE user SET one_time_token = NULL WHERE user_id = ?");
            $stmtDel->execute([$user['user_id']]);

            // C. Hoàn thành đơn & Chuyển hướng
            $this->completeOrderAndRedirect($orderId);
            return;
        }

        // 3. CÁCH 2: Kiểm tra mã MD5 (Dành cho Khách vãng lai)
        // Logic: Nếu không tìm thấy trong DB, kiểm tra xem có phải là mã MD5 bí mật không
        $validMd5 = md5($orderId . 'TechHubSecretKey2025');

        if ($receivedToken === $validMd5) {
            // Chỉ xác nhận đơn, KHÔNG đăng nhập (vì là khách)
            $this->completeOrderAndRedirect($orderId);
            return;
        }

        // 4. Nếu cả 2 cách đều không khớp
        echo "<script>alert('Link xác nhận không hợp lệ hoặc đã hết hạn.'); window.location.href='index.php';</script>";
    }

    // Hàm phụ xử lý update và chuyển trang (tránh viết lặp lại)
    private function completeOrderAndRedirect($orderId)
    {
        global $pdo;
        // Cập nhật trạng thái đơn hàng
        $stmt = $pdo->prepare("UPDATE `order` SET order_status = 'COMPLETED' WHERE order_id = ?");
        $stmt->execute([$orderId]);

        // Chuyển hướng sang trang đánh giá
        header("Location: index.php?class=order&act=review_order&id=$orderId");
        exit;
    }

    public function review_order()
    {
        global $pdo;
        $orderId = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("
            SELECT od.*, p.name, p.main_image_url 
            FROM order_detail od
            JOIN products p ON od.product_id = p.product_id
            WHERE od.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/review_order.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    // Tiếp tục thanh toán VNPay cho đơn hàng đã tạo
    public function continue_payment()
    {
        global $pdo;
        $orderId = $_GET['id'] ?? 0;

        if ($orderId <= 0) {
            header('Location: index.php');
            exit;
        }

        // Kiểm tra đơn hàng có tồn tại và ở trạng thái PENDING không
        $stmt = $pdo->prepare("SELECT o.*, p.payment_method, p.payment_status FROM `order` o 
                                LEFT JOIN payment p ON o.order_id = p.order_id 
                                WHERE o.order_id = ? AND o.order_status = 'PENDING'");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order || $order['payment_method'] !== 'VNPAY') {
            echo "<script>alert('Đơn hàng không hợp lệ hoặc không thể tiếp tục thanh toán.'); window.location.href='index.php?class=account&act=account';</script>";
            exit;
        }

        // Reset trạng thái payment về PENDING cho lần thanh toán mới
        $stmtUpdate = $pdo->prepare("UPDATE payment SET payment_status = 'PENDING' WHERE order_id = ?");
        $stmtUpdate->execute([$orderId]);
        // Đảm bảo đơn vẫn ở PENDING trước khi đi thanh toán lại
        $stmtOrderReset = $pdo->prepare("UPDATE `order` SET order_status = 'PENDING' WHERE order_id = ?");
        $stmtOrderReset->execute([$orderId]);

        require_once("./config/config_vnpay.php");

        // Mỗi lần thanh toán tạo mã giao dịch mới để tránh VNPAY báo trùng
        $vnp_TxnRef = $this->generateTxnRef($orderId);
        $vnp_OrderInfo = 'Thanh toan don hang #' . $orderId;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order['total_amount'] * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date("YmdHis"),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        header('Location: ' . $vnp_Url);
        exit;
    }

    // Xóa đúng các sản phẩm đã mua khỏi giỏ hàng (user hoặc khách)
    private function removePurchasedItemsFromCart($orderId)
    {
        global $pdo;

        // Lấy danh sách sản phẩm trong đơn
        $stmt = $pdo->prepare("SELECT product_id, variant_id FROM order_detail WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($items)) {
            return;
        }

        // Lấy user_id của đơn (để biết là khách hay thành viên)
        $stmtOrder = $pdo->prepare("SELECT user_id FROM `order` WHERE order_id = ?");
        $stmtOrder->execute([$orderId]);
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
        $userId = $order['user_id'] ?? null;

        // Chuẩn hóa variant_id null nếu <=0
        $normalizedItems = array_map(function ($row) {
            return [
                'product_id' => (int)$row['product_id'],
                'variant_id' => ($row['variant_id'] ?? null) ? (int)$row['variant_id'] : null,
            ];
        }, $items);

        // Gọi CartModel để xóa đúng các item (DB hoặc session)
        CartModel::removeSelectedItems($normalizedItems, $userId);
    }

    // Hủy đơn hàng do người dùng chủ động; nếu đã thanh toán VNPAY thì chuyển sang trạng thái hoàn tiền
    public function cancel_order()
    {
        global $pdo;
        $orderId = (int)($_POST['order_id'] ?? 0);
        $userId = $_SESSION['user_id'] ?? 0;

        header('Content-Type: application/json');

        if ($orderId <= 0 || $userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ hoặc chưa đăng nhập.']);
            return;
        }

        $stmt = $pdo->prepare("SELECT o.user_id, o.order_status, p.payment_method, p.payment_status 
                               FROM `order` o 
                               LEFT JOIN payment p ON o.order_id = p.order_id
                               WHERE o.order_id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order || (int)$order['user_id'] !== (int)$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng hoặc không có quyền thao tác.']);
            return;
        }

        if (in_array($order['order_status'], ['CANCELLED', 'COMPLETED'])) {
            echo json_encode(['status' => 'error', 'message' => 'Đơn hàng đã hoàn tất hoặc đã hủy.']);
            return;
        }

        try {
            $pdo->beginTransaction();

            // Nếu đã thanh toán thành công qua VNPAY thì chuyển sang chờ hoàn tiền
            if ($order['payment_method'] === 'VNPAY' && $order['payment_status'] === 'COMPLETED') {
                $newPaymentStatus = 'REFUND_PENDING';
            } else {
                $newPaymentStatus = 'CANCELLED';
            }

            $stmtPay = $pdo->prepare("UPDATE payment SET payment_status = ? WHERE order_id = ?");
            $stmtPay->execute([$newPaymentStatus, $orderId]);

            $stmtOrder = $pdo->prepare("UPDATE `order` SET order_status = 'CANCELLED' WHERE order_id = ?");
            $stmtOrder->execute([$orderId]);

            // Khôi phục tồn kho
            $stmt = $pdo->prepare("SELECT product_id, variant_id, quantity FROM order_detail WHERE order_id = ?");
            $stmt->execute([$orderId]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($orderItems as $item) {
                OrderModel::restoreStock($pdo, $item['product_id'], $item['variant_id'], $item['quantity']);
            }

            $pdo->commit();

            $message = ($newPaymentStatus === 'REFUND_PENDING')
                ? 'Đã yêu cầu hủy và hoàn tiền. Vui lòng chờ xử lý.'
                : 'Đã hủy đơn hàng.';

            echo json_encode(['status' => 'success', 'message' => $message]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => 'Không thể hủy đơn hàng: ' . $e->getMessage()]);
        }
    }

    // Tạo mã giao dịch duy nhất cho mỗi lần thanh toán VNPay
    private function generateTxnRef($orderId)
    {
        return $orderId . '-' . time();
    }
}
