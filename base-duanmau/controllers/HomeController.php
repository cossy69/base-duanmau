<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/ProductController.php'; // Vẫn cần để gọi getCategories
include_once __DIR__ . '/FavoriteController.php';

// SỬA: Gọi Model
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/PostModel.php';
include_once __DIR__ . '/../models/CartModel.php';

class HomeController
{
    public function home()
    {
        global $pdo;

        // SỬA: Gọi từ Model và đổi tên hàm
        $newProducts = ProductModel::getProductsSimple($pdo, "p.product_id DESC", 8);
        $bestDeals = ProductModel::getProductsSimple($pdo, "discount_percent DESC", 10);
        $brands = ProductModel::getBrands($pdo);

        // SỬA: Gọi từ Model
        $allPosts = PostModel::getPosts($pdo, 4);

        $mainPost = null;
        $sidePosts = [];
        if (!empty($allPosts)) {
            $mainPost = array_shift($allPosts);
            $sidePosts = $allPosts;
        }

        // SỬA: Lấy categories từ ProductModel
        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/home.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function filterProducts()
    {
        global $pdo;
        $brand_id = $_POST['brand_id'] ?? 'all';
        $brandId = ($brand_id == 'all' || $brand_id == 0) ? null : (int)$brand_id;

        // SỬA: Gọi từ Model và đổi tên hàm
        $products = ProductModel::getProductsSimple($pdo, "p.product_id DESC", 8, $brandId);

        ob_start();
        include __DIR__ . '/../views/user/partials/_product_card.php';
        $html = ob_get_clean();
        echo $html;
    }

    // SỬA: Xóa hàm getBrands() (đã chuyển sang Model)

    private function truncate($text, $length = 200, $suffix = '...')
    {
        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }
        $truncated = mb_substr($text, 0, $length, 'UTF-8');
        $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
        }
        return $truncated . $suffix;
    }
}
