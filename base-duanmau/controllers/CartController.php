<?php
include_once './config/db_connection.php';
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/FavoriteModel.php';
include_once __DIR__ . '/../models/ProductModel.php';

class CartController
{
    public function cart()
    {
        global $pdo;

        $cartData = CartModel::getCartContents($pdo);
        $cartItems = $cartData['items'];
        $subtotal = $cartData['subtotal'];
        $shippingMethods = CartModel::getShippingMethods($pdo);
        $coupons = CartModel::getAvailableCoupons($pdo);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);
        $userAddress = '';
        if (isset($_SESSION['user_id'])) {
            $userInfo = CartModel::getUserInfo($pdo, $_SESSION['user_id']);
            if (!empty($userInfo['address'])) {
                $userAddress = $userInfo['address'];
            }
        }
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/cart.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function addToCart()
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }

        $dbVariantId = ($variantId <= 0) ? null : $variantId;
        $userId = $_SESSION['user_id'] ?? null;


        $success = CartModel::addToCart($productId, $dbVariantId, $quantity, $userId);

        if ($success) {
            $totalQuantity = CartModel::getCartItemCount();
            $this->jsonResponse('success', 'Đã thêm vào giỏ hàng!', ['total_quantity' => $totalQuantity]);
        } else {
            $this->jsonResponse('error', 'Lỗi khi thêm vào giỏ hàng.');
        }
    }


    public function updateQuantity()
    {
        global $pdo;
        $productId = (int)($_POST['product_id'] ?? 0);
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($quantity <= 0 || ($productId <= 0 && $variantId <= 0)) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }
        $dbVariantId = ($variantId <= 0) ? null : $variantId;
        $userId = $_SESSION['user_id'] ?? null;

        $success = CartModel::updateQuantity($productId, $dbVariantId, $quantity, $userId);

        if ($success) {
            $cartData = CartModel::getCartContents($pdo);
            $cartData['total_quantity'] = CartModel::getCartItemCount();
            $this->jsonResponse('success', 'Cập nhật thành công!', $cartData);
        } else {
            $this->jsonResponse('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
        }
    }

    public function removeSelectedItems()
    {
        global $pdo;
        $itemsJson = $_POST['items_json'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            $this->jsonResponse('error', 'Không có sản phẩm nào được chọn.');
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;

        CartModel::removeSelectedItems($items, $userId);

        $cartData = CartModel::getCartContents($pdo);
        $cartData['total_quantity'] = CartModel::getCartItemCount();
        $this->jsonResponse('success', 'Đã xóa các sản phẩm đã chọn.', $cartData);
    }
    public function checkout()
    {
        global $pdo;
        $cartData = CartModel::getCartContents($pdo);
        $cartItems = $cartData['items'];
        $subtotal = $cartData['subtotal'];

        if (empty($cartItems)) {
            header('Location: index.php?class=cart&act=cart');
            exit;
        }

        $shippingFee = (int)($_POST['shipping_fee'] ?? 0);
        $shippingMethod = $_POST['shipping_method'] ?? 'Chưa tính';
        $discountAmount = (int)($_POST['discount_amount'] ?? 0);
        $couponCode = $_POST['coupon_code'] ?? '';
        $customerAddress = $_POST['customer_address'] ?? '';

        $totalAmount = $subtotal + $shippingFee - $discountAmount;

        $userPhone = '';
        $userName = '';
        $userEmail = '';

        if (isset($_SESSION['user_id'])) {
            $userInfo = CartModel::getUserInfo($pdo, $_SESSION['user_id']);
            if ($userInfo) {
                $userPhone = $userInfo['phone'];
                $userName = $userInfo['full_name'];
                $userEmail = $userInfo['email'];
            }
        }

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/checkout.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
