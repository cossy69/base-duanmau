<?php
class ReviewModel
{
    public static function checkIfUserPurchasedProduct($pdo, $userId, $productId)
    {
        $sql = "
            SELECT COUNT(od.order_detail_id)
            FROM `order` o
            JOIN `order_detail` od ON o.order_id = od.order_id
            WHERE o.user_id = ? 
              AND od.product_id = ?
              -- Chỉ tính các đơn đã hoàn thành (hoặc đang giao)
              AND o.order_status IN ('COMPLETED', 'SHIPPED', 'DELIVERED') 
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $productId]);

        return $stmt->fetchColumn() > 0;
    }
    public static function insertReview($pdo, $productId, $userId, $rating, $comment)
    {
        $sql = "INSERT INTO review (product_id, user_id, rating, comment, is_approved) 
                VALUES (?, ?, ?, ?, 0)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$productId, $userId, $rating, $comment]);
    }

    public static function getReviewSummary($pdo, $productId)
    {
        $sql = "
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as '5_star',
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as '4_star',
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as '3_star',
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as '2_star',
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as '1_star'
            FROM review
            WHERE product_id = ? AND is_approved = 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        $summary = $stmt->fetch();

        $summary['avg_rating'] = $summary['total_reviews'] > 0 ? $summary['avg_rating'] : 0;
        $summary['percentages'] = [
            '5' => $summary['total_reviews'] > 0 ? ($summary['5_star'] / $summary['total_reviews']) * 100 : 0,
            '4' => $summary['total_reviews'] > 0 ? ($summary['4_star'] / $summary['total_reviews']) * 100 : 0,
            '3' => $summary['total_reviews'] > 0 ? ($summary['3_star'] / $summary['total_reviews']) * 100 : 0,
            '2' => $summary['total_reviews'] > 0 ? ($summary['2_star'] / $summary['total_reviews']) * 100 : 0,
            '1' => $summary['total_reviews'] > 0 ? ($summary['1_star'] / $summary['total_reviews']) * 100 : 0,
        ];
        return $summary;
    }

    public static function getReviews($pdo, $productId)
    {
        $sql = "
            SELECT r.*, u.full_name 
            FROM review r
            JOIN user u ON r.user_id = u.user_id
            WHERE r.product_id = ? AND r.is_approved = 1
            ORDER BY r.review_date DESC
            LIMIT 10
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
}
