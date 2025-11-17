<?php
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/ProductController.php';

class FavoriteController
{
    /**
     * Hàm chính: Hiển thị trang Yêu thích (act=favorite)
     */
    public function favorite()
    {
        global $pdo;

        // Bắt buộc phải đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?class=login&act=login');
            exit;
        }
        $userId = $_SESSION['user_id'];

        // Lấy dữ liệu cho trang
        $favoriteProducts = $this->getFavoriteProducts($pdo, $userId);

        // Lấy dữ liệu cho header
        $categories = ProductController::getCategories($pdo);
        $cartItemCount = CartController::getCartItemCount();
        $favoriteCount = self::getFavoriteCount($pdo, $userId); // Đếm số yêu thích

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/favorite.php'; // File này sẽ được sửa ở Bước 5
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
            // 1. Kiểm tra xem đã yêu thích chưa
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_products WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $isFavorited = $stmt->fetchColumn() > 0;

            if ($isFavorited) {
                // 2. Nếu đã thích -> Bỏ thích (DELETE)
                $stmt = $pdo->prepare("DELETE FROM favorite_products WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
                $newCount = self::getFavoriteCount($pdo, $userId);
                $this->jsonResponse('success', 'Đã xóa khỏi yêu thích.', ['action' => 'removed', 'count' => $newCount]);
            } else {
                // 3. Nếu chưa thích -> Thêm (INSERT)
                $stmt = $pdo->prepare("INSERT INTO favorite_products (user_id, product_id) VALUES (?, ?)");
                $stmt->execute([$userId, $productId]);
                $newCount = self::getFavoriteCount($pdo, $userId);
                $this->jsonResponse('success', 'Đã thêm vào yêu thích.', ['action' => 'added', 'count' => $newCount]);
            }
        } catch (PDOException $e) {
            $this->jsonResponse('error', 'Lỗi CSDL: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Lấy danh sách sản phẩm yêu thích (giống getProducts)
     */
    private function getFavoriteProducts($pdo, $userId)
    {
        // Câu SQL này tương tự getProducts, nhưng join với favorite_products
        $sql = "
            SELECT
                p.product_id,
                MIN(pv.variant_id) as default_variant_id,
                p.name,
                MIN(pv.original_variant_price) as original_price,
                MIN(pv.current_variant_price) as current_price,
                COALESCE( p.main_image_url) AS image_url,
                (MIN(pv.original_variant_price) - MIN(pv.current_variant_price)) AS discount_amount,
                CASE 
                    WHEN MIN(pv.original_variant_price) > 0 AND MIN(pv.original_variant_price) > MIN(pv.current_variant_price)
                    THEN ((MIN(pv.original_variant_price) - MIN(pv.current_variant_price)) / MIN(pv.original_variant_price)) * 100
                    ELSE 0 
                END AS discount_percent
            FROM favorite_products fp
            JOIN products p ON fp.product_id = p.product_id
            JOIN product_variants pv ON p.product_id = pv.product_id
            WHERE fp.user_id = ? AND p.is_active = 1
            GROUP BY p.product_id, p.name, p.main_image_url
            ORDER BY fp.created_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Helper (static): Đếm số lượng yêu thích cho header
     */
    public static function getFavoriteCount($pdo, $userId)
    {
        if ($userId <= 0) {
            return 0; // Khách thì không có
        }
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_products WHERE user_id = ?");
            $stmt->execute([$userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    public static function getFavoriteProductIds($pdo, $userId)
    {
        if ($userId <= 0) {
            return []; // Khách thì không có
        }
        try {
            $stmt = $pdo->prepare("SELECT product_id FROM favorite_products WHERE user_id = ?");
            $stmt->execute([$userId]);
            // Dùng FETCH_COLUMN để lấy 1 mảng ID (VD: [1, 5, 12])
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * Helper: Trả về JSON
     */
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
