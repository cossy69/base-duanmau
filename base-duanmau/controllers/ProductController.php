<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/FavoriteController.php';

// SỬA: Gọi Model
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/ReviewModel.php';
include_once __DIR__ . '/../models/CartModel.php';


class ProductController
{
    public function product()
    {
        global $pdo;
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

        // SỬA: Gọi từ Model và đổi tên hàm
        $result = ProductModel::getProductsFiltered($pdo, $filters, $sortOrder, $currentPage, $limit);
        $products = $result['products'];
        $totalProducts = $result['total'];

        // SỬA: Gọi từ Model
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

        // SỬA: Gọi từ Model
        $product = ProductModel::getProductDetails($pdo, $productId);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }

        // SỬA: Gọi từ Model
        $variantOptions = ProductModel::getVariantOptions($pdo, $productId);
        $galleryImages = ProductModel::getGalleryImages($pdo, $productId, $product['main_image_url']);
        $productSpecs = ProductModel::getProductSpecs($pdo, $productId);

        // SỬA: Gọi từ ReviewModel
        $reviewSummary = ReviewModel::getReviewSummary($pdo, $productId);
        $reviews = ReviewModel::getReviews($pdo, $productId);

        // Lấy data cho header
        $categories = ProductModel::getCategories($pdo); // Lấy category cho header
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);

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

        // SỬA: Gọi từ Model
        $variant = ProductModel::fetchVariantDetailsByOptions($pdo, $productId, $optionValueIds);

        if ($variant) {
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

    // SỬA: ĐÃ XÓA TẤT CẢ CÁC HÀM LOGIC DATABASE
}
