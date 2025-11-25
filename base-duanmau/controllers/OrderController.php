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

        $cartData = CartModel::getCartContents($pdo);
        $cartItems = $cartData['items'];
        $subtotal = $cartData['subtotal'];

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

            $couponId = null;
            if (!empty($couponCode)) {
                $couponId = OrderModel::getCouponIdByCode($pdo, $couponCode);
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

            OrderModel::clearCart($pdo, $userId);

            $pdo->commit();

            if ($paymentMethod === 'VNPAY') {
                require_once("./config/config_vnpay.php");

                $vnp_TxnRef = $orderId;
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
            $pdo->rollBack();
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

        $orderId = $_GET['vnp_TxnRef'];

        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {

                $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'COMPLETED', payment_method = 'VNPAY' WHERE order_id = ?");
                $stmt->execute([$orderId]);


                header("Location: index.php?class=order&act=success&id=$orderId");
            } else {
                echo "<div style='text-align:center; margin-top:50px;'>
                        <h2 style='color:red;'>Thanh toán thất bại!</h2>
                        <p>Giao dịch bị hủy hoặc có lỗi xảy ra.</p>
                        <a href='index.php?class=cart&act=checkout'>Về trang thanh toán</a>
                      </div>";
            }
        } else {
            echo "Chu ký không hợp lệ (Invalid Signature)";
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

        // 1. Xử lý đăng nhập tự động qua Token (MỚI THÊM)
        $loginToken = $_GET['login_token'] ?? null;
        if ($loginToken) {
            // Tìm user có token này
            $stmt = $pdo->prepare("SELECT user_id, full_name, email FROM user WHERE one_time_token = ?");
            $stmt->execute([$loginToken]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (session_status() === PHP_SESSION_NONE) session_start();

                // Đăng nhập
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                // Hủy token ngay lập tức (Để link không dùng được lần 2)
                $stmtDel = $pdo->prepare("UPDATE user SET one_time_token = NULL WHERE user_id = ?");
                $stmtDel->execute([$user['user_id']]);

                // Cập nhật đơn hàng thành công
                $stmtOrder = $pdo->prepare("UPDATE `order` SET order_status = 'COMPLETED' WHERE order_id = ?");
                $stmtOrder->execute([$orderId]);

                // Chuyển hướng sang trang đánh giá
                header("Location: index.php?class=order&act=review_order&id=$orderId");
                exit;
            } else {
                // Token sai hoặc đã hết hạn
                echo "<script>alert('Link xác nhận đã hết hạn hoặc không hợp lệ. Vui lòng đăng nhập thủ công.'); window.location.href='index.php?class=login&act=login';</script>";
                exit;
            }
        }

        // 2. Logic cũ (Dành cho trường hợp không dùng token auto-login)
        $token = $_GET['token'] ?? '';
        $validToken = md5($orderId . 'TechHubSecretKey2025');
        if ($token !== $validToken) {
            die('Link xác nhận không hợp lệ hoặc đã hết hạn.');
        }
        $stmt = $pdo->prepare("UPDATE `order` SET order_status = 'COMPLETED' WHERE order_id = ?");
        $stmt->execute([$orderId]);
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
}
