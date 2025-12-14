<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/FavoriteController.php';

include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/ReviewModel.php';
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/CompareModel.php';


class ProductController
{
    public function product()
    {
        global $pdo;
        $compareProductIds = CompareModel::getComparisonIds();
        $compareCount = count($compareProductIds);
        $limit = 9;
        $maxPriceDefault = 50000000;
        $maxPrice = (int)($_GET['max_price'] ?? $maxPriceDefault);
        $currentPage = (int)($_GET['page'] ?? 1);
        $sortOrder = (string)($_GET['sort'] ?? 'newest');
        $filterCategories = (array)($_GET['category'] ?? []);
        $filterBrandId = (int)($_GET['brand'] ?? 0);
        $filterCategories = array_map('intval', $filterCategories);
        $filters = [
            'categories' => $filterCategories,
            'brand_id'   => $filterBrandId,
            'max_price'  => $maxPrice,
            'max_price_default' => $maxPriceDefault
        ];

        $result = ProductModel::getProductsFiltered($pdo, $filters, $sortOrder, $currentPage, $limit);
        $products = $result['products'];
        $totalProducts = $result['total'];

        $categories = ProductModel::getCategories($pdo);
        $brands = ProductModel::getBrands($pdo);

        $cartItemCount = CartModel::getCartItemCount();
        $totalPages = ceil($totalProducts / $limit);

        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/product.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function product_detail()
    {
        global $pdo;
        $productId = (int)($_GET['id'] ?? 0);
        if ($productId <= 0) {
            echo "Sản phẩm không hợp lệ.";
            return;
        }

        $product = ProductModel::getProductDetails($pdo, $productId);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }
        $isFavorited = false;
        if (isset($_SESSION['user_id'])) {
            $isFavorited = ProductModel::checkFavorite($pdo, $_SESSION['user_id'], $productId);
        }

        $compareProductIds = CompareModel::getComparisonIds();
        $isCompared = in_array($productId, $compareProductIds);

        $compareCount = count($compareProductIds);
        $variantOptions = ProductModel::getVariantOptions($pdo, $productId);
        $galleryImages = ProductModel::getGalleryImages($pdo, $productId, $product['main_image_url']);
        $productSpecs = ProductModel::getProductSpecs($pdo, $productId);

        $reviewSummary = ReviewModel::getReviewSummary($pdo, $productId);
        $userId = $_SESSION['user_id'] ?? 0;
        $reviews = ReviewModel::getReviews($pdo, $productId, $userId);
        
        // Lấy đánh giá của người dùng hiện tại (nếu có)
        $userReview = null;
        if ($userId > 0) {
            $userReview = ReviewModel::getUserReview($pdo, $userId, $productId);
        }

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);
        
        // Lấy thông tin tồn kho
        $totalStock = ProductModel::getProductStock($pdo, $productId);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $isAllowedToReview = false;

        if (isset($_SESSION['user_id'])) {
            include_once './models/ReviewModel.php';

            $isAllowedToReview = ReviewModel::checkIfUserPurchasedProduct($pdo, $_SESSION['user_id'], $id);
        }

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/product_detail.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function getVariantDetails()
    {
        global $pdo;
        $productId = (int)($_POST['product_id'] ?? 0);
        $optionValueIds = (array)($_POST['options'] ?? []);

        if ($productId <= 0 || empty($optionValueIds)) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }

        $variant = ProductModel::fetchVariantDetailsByOptions($pdo, $productId, $optionValueIds);

        if ($variant) {
            // Thêm thông tin tồn kho vào response
            $variant['stock'] = ProductModel::getVariantStock($pdo, $variant['variant_id']);
            $this->jsonResponse('success', 'Tìm thấy biến thể.', $variant);
        } else {
            $this->jsonResponse('error', 'Không tìm thấy phiên bản phù hợp.');
        }
    }

    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
