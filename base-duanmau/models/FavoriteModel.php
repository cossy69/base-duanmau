<?php
class FavoriteModel
{
    /**
     * Lấy danh sách sản phẩm yêu thích (cho trang favorite)
     * (Chuyển từ FavoriteController)
     */
    public static function getFavoriteProducts($pdo, $userId)
    {
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
     * Đếm số lượng yêu thích (cho header)
     * (Chuyển từ FavoriteController)
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

    /**
     * Lấy MẢNG ID các sản phẩm đã yêu thích
     * (Chuyển từ FavoriteController)
     */
    public static function getFavoriteProductIds($pdo, $userId)
    {
        if ($userId <= 0) {
            return []; // Khách thì không có
        }
        try {
            $stmt = $pdo->prepare("SELECT product_id FROM favorite_products WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * (MỚI) Kiểm tra xem đã yêu thích chưa
     * (Tách ra từ toggleFavorite)
     */
    public static function isFavorited($pdo, $userId, $productId)
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_products WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * (MỚI) Thêm vào yêu thích
     * (Tách ra từ toggleFavorite)
     */
    public static function addFavorite($pdo, $userId, $productId)
    {
        $stmt = $pdo->prepare("INSERT INTO favorite_products (user_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $productId]);
    }

    /**
     * (MỚI) Xóa khỏi yêu thích
     * (Tách ra từ toggleFavorite)
     */
    public static function removeFavorite($pdo, $userId, $productId)
    {
        $stmt = $pdo->prepare("DELETE FROM favorite_products WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }
}
