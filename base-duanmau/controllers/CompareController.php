<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/ProductController.php';
include_once __DIR__ . '/FavoriteController.php';
include_once __DIR__ . '/../models/CompareModel.php';

class CompareController
{
    public function compare()
    {
        global $pdo;

        $productIds = CompareModel::getComparisonIds();

        $comparisonData = CompareModel::getComparisonData($pdo, $productIds);

        $products = $comparisonData['products'] ?? [];
        $specs = $comparisonData['specs'] ?? [];

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/compare.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function toggleCompare()
    {
        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->jsonResponse('error', 'Sản phẩm không hợp lệ.');
            return;
        }

        $result = CompareModel::toggleComparisonItem($productId);

        if ($result['status'] === 'limit_reached') {
            $this->jsonResponse('error', 'Chỉ được so sánh tối đa ' . CompareModel::MAX_ITEMS . ' sản phẩm.');
            return;
        }

        $this->jsonResponse('success', 'Đã ' . ($result['status'] === 'added' ? 'thêm' : 'xóa') . ' sản phẩm.', $result);
    }

    public function removeProduct()
    {
        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->jsonResponse('error', 'Sản phẩm không hợp lệ.');
            return;
        }

        $result = CompareModel::toggleComparisonItem($productId);

        $this->jsonResponse('success', 'Đã xóa sản phẩm khỏi bảng.', $result);
    }


    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
