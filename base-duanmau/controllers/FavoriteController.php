<?php
include_once __DIR__ . '/../config/db_connection.php';
// SỬA: Gọi các Model
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/FavoriteModel.php'; // (File Model mới)

class FavoriteController
{
    /**
     * Hàm chính: Hiển thị trang Yêu thích (act=favorite)
     */
    public function favorite()
    {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?class=login&act=login');
            exit;
        }
        $userId = $_SESSION['user_id'];

        // SỬA: Gọi từ Model
        $favoriteProducts = FavoriteModel::getFavoriteProducts($pdo, $userId);

        // Lấy dữ liệu cho header
        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        // SỬA: Gọi từ Model
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/favorite.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * [AJAX] Thêm hoặc Xóa một sản phẩm khỏi danh sách
     */
    public function toggleFavorite()
    {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse('error', 'Bạn cần đăng nhập để thực hiện việc này.');
            return;
        }

        $userId = $_SESSION['user_id'];
        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->jsonResponse('error', 'Sản phẩm không hợp lệ.');
            return;
        }

        try {
            // SỬA: Gọi logic từ Model
            $isFavorited = FavoriteModel::isFavorited($pdo, $userId, $productId);

            if ($isFavorited) {
                // Bỏ thích (DELETE)
                FavoriteModel::removeFavorite($pdo, $userId, $productId);
                $newCount = FavoriteModel::getFavoriteCount($pdo, $userId);
                $this->jsonResponse('success', 'Đã xóa khỏi yêu thích.', ['action' => 'removed', 'count' => $newCount]);
            } else {
                // Thêm (INSERT)
                FavoriteModel::addFavorite($pdo, $userId, $productId);
                $newCount = FavoriteModel::getFavoriteCount($pdo, $userId);
                $this->jsonResponse('success', 'Đã thêm vào yêu thích.', ['action' => 'added', 'count' => $newCount]);
            }
        } catch (PDOException $e) {
            $this->jsonResponse('error', 'Lỗi CSDL: ' . $e->getMessage());
        }
    }
    
    // SỬA: Xóa 3 hàm: getFavoriteProducts, getFavoriteCount, getFavoriteProductIds
    // (Đã chuyển sang Model)

    /**
     * Helper: Trả về JSON (Giữ lại)
     */
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
