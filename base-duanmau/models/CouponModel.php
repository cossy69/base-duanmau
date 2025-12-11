<?php
class CouponModel
{
    public static function getActiveCoupons($pdo)
    {
        $sql = "SELECT * FROM coupons 
                WHERE is_active = 1 
                AND start_date <= NOW() 
                AND end_date >= NOW()
                ORDER BY discount_value DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
