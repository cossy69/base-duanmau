<?php
include_once './config/db_connection.php';
include_once './models/PostModel.php';
include_once './models/ProductModel.php';
include_once './models/CartModel.php';

class NewsController
{
    public function news()
    {
        global $pdo;

        $posts = PostModel::filterPosts($pdo);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/news.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
    public function filter()
    {
        global $pdo;
        $keyword = $_GET['keyword'] ?? '';

        $posts = PostModel::filterPosts($pdo, $keyword);

        if (empty($posts)) {
            echo '<div class="alert alert-warning text-center">Không tìm thấy bài viết nào.</div>';
        } else {
            foreach ($posts as $post) {
                include './views/user/partials/_news_item.php';
            }
        }
    }
    public function new_detail()
    {
        global $pdo;
        $id = $_GET['id'] ?? 0;

        $post = PostModel::getPostById($pdo, $id);

        $relatedPosts = PostModel::getPublishedPosts($pdo, 5, 1);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;

        if (!$post) {
            echo "Bài viết không tồn tại.";
            return;
        }

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/new_detail.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
