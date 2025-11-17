<?php
include_once './config/db_connection.php';
// Gọi Model
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/FavoriteModel.php';
include_once __DIR__ . '/../models/ProductModel.php'; // (Sửa: dùng ProductModel)

class CartController
{
    /**
     * Action: Hiển thị trang giỏ hàng
     */
    public function cart()
    {
        global $pdo;

        // SỬA: Gọi từ Model
        $cartData = CartModel::getCartContents($pdo);
        $cartItems = $cartData['items'];
        $subtotal = $cartData['subtotal'];

        // Lấy dữ liệu cho header
        $categories = ProductModel::getCategories($pdo); // Sửa: Gọi từ ProductModel
        $cartItemCount = CartModel::getCartItemCount(); // Sửa: Gọi từ CartModel
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);

        // Gọi View
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/cart.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * Action: [AJAX] Thêm vào giỏ hàng
     */
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

        // SỬA: Gọi từ Model
        $success = CartModel::addToCart($productId, $dbVariantId, $quantity, $userId);

        if ($success) {
            // SỬA: Gọi từ Model
            $totalQuantity = CartModel::getCartItemCount();
            $this->jsonResponse('success', 'Đã thêm vào giỏ hàng!', ['total_quantity' => $totalQuantity]);
        } else {
            $this->jsonResponse('error', 'Lỗi khi thêm vào giỏ hàng.');
        }
    }

    /**
     * Action: [AJAX] Cập nhật số lượng
     */
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

        // SỬA: Gọi từ Model
        $success = CartModel::updateQuantity($productId, $dbVariantId, $quantity, $userId);

        if ($success) {
            // SỬA: Lấy lại dữ liệu mới sau khi cập nhật
            $cartData = CartModel::getCartContents($pdo);
            $cartData['total_quantity'] = CartModel::getCartItemCount();
            $this->jsonResponse('success', 'Cập nhật thành công!', $cartData);
        } else {
            $this->jsonResponse('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
        }
    }

    /**
     * Action: [AJAX] Xóa sản phẩm đã chọn
     */
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

        // SỬA: Gọi từ Model
        CartModel::removeSelectedItems($items, $userId);

        // Lấy lại giỏ hàng mới
        $cartData = CartModel::getCartContents($pdo);
        $cartData['total_quantity'] = CartModel::getCartItemCount();
        $this->jsonResponse('success', 'Đã xóa các sản phẩm đã chọn.', $cartData);
    }

    /**
     * Helper: Trả về JSON (Giữ lại)
     */
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
