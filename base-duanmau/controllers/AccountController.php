<?php
include_once './config/db_connection.php';
include_once './models/AccountModel.php';
include_once './models/ProductModel.php';
include_once './models/CartModel.php';
include_once './models/ReviewModel.php';

class AccountController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?class=login&act=login');
            exit;
        }
    }

    public function account()
    {
        global $pdo;
        $userId = $_SESSION['user_id'];

        $user = AccountModel::getUserById($pdo, $userId);
        $orders = AccountModel::getOrderHistory($pdo, $userId);
        $userReviews = ReviewModel::getUserReviews($pdo, $userId);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/account.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function update_profile()
    {
        global $pdo;
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullName = $_POST['full_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            if (AccountModel::updateProfile($pdo, $userId, $fullName, $phone, $address)) {
                $_SESSION['account_success'] = "Cập nhật thông tin thành công!";
                $_SESSION['full_name'] = $fullName;
            } else {
                $_SESSION['account_error'] = "Có lỗi xảy ra.";
            }
        }
        header('Location: index.php?class=account&act=account');
    }

    public function change_password()
    {
        global $pdo;
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $oldPass = $_POST['oldPass'];
            $newPass = $_POST['newPass'];
            $confirmPass = $_POST['confirmPass'];

            $user = AccountModel::getUserById($pdo, $userId);

            if (!password_verify($oldPass, $user['password_hash'])) {
                $_SESSION['account_error'] = "Mật khẩu cũ không đúng.";
            } elseif ($newPass !== $confirmPass) {
                $_SESSION['account_error'] = "Mật khẩu xác nhận không khớp.";
            } elseif (strlen($newPass) < 6) {
                $_SESSION['account_error'] = "Mật khẩu phải từ 6 ký tự.";
            } else {
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                AccountModel::changePassword($pdo, $userId, $newHash);
                $_SESSION['account_success'] = "Đổi mật khẩu thành công!";
            }
        }
        header('Location: index.php?class=account&act=account');
    }
    public function get_order_detail_html()
    {
        global $pdo;

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            echo "Vui lòng đăng nhập.";
            exit;
        }

        $orderId = $_GET['id'] ?? 0;
        $userId = $_SESSION['user_id'];

        // 2. Lấy thông tin đơn hàng (để check quyền xem)
        $stmt = $pdo->prepare("SELECT * FROM `order` WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();

        if (!$order) {
            echo "Không tìm thấy đơn hàng.";
            exit;
        }

        // 3. Lấy chi tiết sản phẩm
        // (Giả sử anh có bảng order_detail nối với products)
        $stmtItems = $pdo->prepare("
            SELECT od.*, p.name, p.main_image_url 
            FROM order_detail od
            JOIN products p ON od.product_id = p.product_id
            WHERE od.order_id = ?
        ");
        $stmtItems->execute([$orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // 4. Xuất HTML (Table)
        echo '<table class="table table-bordered align-middle">';
        echo '<thead class="table-light"><tr><th>Sản phẩm</th><th class="text-center">SL</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead>';
        echo '<tbody>';

        $totalCheck = 0;
        foreach ($items as $item) {
            $img = htmlspecialchars($item['main_image_url']);
            $name = htmlspecialchars($item['name']);
            $price = number_format($item['unit_price']);
            $qty = $item['quantity'];
            $subtotal = number_format($item['unit_price'] * $qty);
            $totalCheck += $item['unit_price'] * $qty;

            echo "<tr>
                    <td>
                        <div class='d-flex align-items-center gap-2'>
                            <img src='$img' style='width: 40px; height: 40px; object-fit: cover;' class='rounded border'>
                            <span>$name</span>
                        </div>
                    </td>
                    <td class='text-center'>$qty</td>
                    <td class='text-end'>{$price} đ</td>
                    <td class='text-end fw-bold'>{$subtotal} đ</td>
                  </tr>";
        }

        // Hiển thị các phí khác (Vận chuyển, giảm giá) lấy từ bảng order
        $ship = number_format($order['shipping_fee']);
        $discount = number_format($order['discount_amount']);
        $finalTotal = number_format($order['total_amount']);

        echo "</tbody>";
        echo "<tfoot>
                <tr><td colspan='3' class='text-end'>Tạm tính:</td><td class='text-end'>" . number_format($totalCheck) . " đ</td></tr>
                <tr><td colspan='3' class='text-end'>Phí vận chuyển:</td><td class='text-end'>{$ship} đ</td></tr>
                <tr><td colspan='3' class='text-end text-success'>Giảm giá:</td><td class='text-end text-success'>-{$discount} đ</td></tr>
                <tr class='bg-light'><td colspan='3' class='text-end fw-bold'>TỔNG CỘNG:</td><td class='text-end fw-bold text-danger fs-5'>{$finalTotal} đ</td></tr>
              </tfoot>";
        echo '</table>';

        // Nút in hóa đơn hoặc đánh giá (nếu cần)
        if ($order['order_status'] == 'COMPLETED') {
            echo "<div class='text-end mt-3'>
                    <a href='index.php?class=order&act=review_order&id=$orderId' class='btn btn-warning btn-sm'><i class='bx bx-star'></i> Đánh giá sản phẩm</a>
                  </div>";
        }

        exit;
    }

    // Xác nhận đã nhận hàng - chuyển trạng thái từ DELIVERED sang COMPLETED
    public function confirm_receipt()
    {
        global $pdo;
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['account_error'] = "Vui lòng đăng nhập để xác nhận nhận hàng.";
            header('Location: index.php?class=login&act=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $orderId = $_POST['order_id'] ?? $_GET['order_id'] ?? 0;

        if ($orderId <= 0) {
            $_SESSION['account_error'] = "Mã đơn hàng không hợp lệ.";
            header('Location: index.php?class=account&act=account');
            exit;
        }

        // Kiểm tra đơn hàng thuộc về user này
        $stmt = $pdo->prepare("SELECT order_id, order_status FROM `order` WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $_SESSION['account_error'] = "Không tìm thấy đơn hàng hoặc bạn không có quyền xác nhận đơn hàng này.";
            header('Location: index.php?class=account&act=account');
            exit;
        }

        if ($order['order_status'] !== 'DELIVERED') {
            $_SESSION['account_error'] = "Chỉ có thể xác nhận nhận hàng khi đơn hàng ở trạng thái 'Đã giao'.";
            header('Location: index.php?class=account&act=account');
            exit;
        }

        // Cập nhật trạng thái thành COMPLETED
        if (AccountModel::confirmReceipt($pdo, $orderId, $userId)) {
            $_SESSION['account_success'] = "Đã xác nhận nhận hàng thành công! Cảm ơn bạn đã mua sắm.";
        } else {
            $_SESSION['account_error'] = "Có lỗi xảy ra khi xác nhận nhận hàng. Vui lòng thử lại.";
        }

        header('Location: index.php?class=account&act=account');
        exit;
    }
}
