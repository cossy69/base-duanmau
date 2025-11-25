<?php
include_once './config/db_connection.php';
include_once './models/SearchModel.php';
include_once './models/ProductModel.php';
include_once './models/CartModel.php';

class SearchController
{
    public function search()
    {
        global $pdo;

        $keyword = $_GET['keyword'] ?? '';
        $filterCategories = $_GET['category'] ?? [];
        $filterBrandId = $_GET['brand'] ?? 0;
        $maxPriceDefault = 50000000;
        $maxPrice = $_GET['max_price'] ?? $maxPriceDefault;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sortOrder = $_GET['sort'] ?? 'newest';
        $limit = 12;

        $products = SearchModel::searchProducts($pdo, $keyword, $filterCategories, $filterBrandId, $maxPrice, $limit, $page, $sortOrder);

        $totalProducts = SearchModel::countSearchResults($pdo, $keyword, $filterCategories, $filterBrandId, $maxPrice);
        $totalPages = ceil($totalProducts / $limit);
        $currentPage = $page;

        $categories = ProductModel::getCategories($pdo);
        $brands = ProductModel::getBrands($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;

        include './views/user/header_link.php';
        include_once './views/user/header.php';

        echo '<div class="container mt-3"><h4 class="text-muted">Kết quả tìm kiếm cho: "' . htmlspecialchars($keyword) . '"</h4></div>';

        require_once './views/user/product.php';

        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
