<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/FavoriteModel.php';

class FavoriteController
{
    public function favorite()
    {
        global $pdo;

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?class=login&act=login');
            exit;
        }
        $userId = $_SESSION['user_id'];

        $favoriteProducts = FavoriteModel::getFavoriteProducts($pdo, $userId);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/favorite.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

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
            $isFavorited = FavoriteModel::isFavorited($pdo, $userId, $productId);

            if ($isFavorited) {
                FavoriteModel::removeFavorite($pdo, $userId, $productId);
                $newCount = FavoriteModel::getFavoriteCount($pdo, $userId);
                $this->jsonResponse('success', 'Đã xóa khỏi yêu thích.', ['action' => 'removed', 'count' => $newCount]);
            } else {
                FavoriteModel::addFavorite($pdo, $userId, $productId);
                $newCount = FavoriteModel::getFavoriteCount($pdo, $userId);
                $this->jsonResponse('success', 'Đã thêm vào yêu thích.', ['action' => 'added', 'count' => $newCount]);
            }
        } catch (PDOException $e) {
            $this->jsonResponse('error', 'Lỗi CSDL: ' . $e->getMessage());
        }
    }


    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
