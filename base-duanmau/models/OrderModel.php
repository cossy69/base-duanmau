<?php
class OrderModel
{
    public static function createOrder($pdo, $userId, $couponId, $totalAmount, $shippingAddress, $shippingFee, $discountAmount)
    {
        $sql = "INSERT INTO `order` (user_id, coupon_id, total_amount, shipping_address, shipping_fee, discount_amount, order_status) 
                VALUES (?, ?, ?, ?, ?, ?, 'PENDING')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $couponId, $totalAmount, $shippingAddress, $shippingFee, $discountAmount]);
        return $pdo->lastInsertId();
    }

    public static function addOrderDetail($pdo, $orderId, $productId, $variantId, $quantity, $unitPrice)
    {
        $sql = "INSERT INTO order_detail (order_id, product_id, variant_id, quantity, unit_price) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        $dbVariantId = ($variantId > 0) ? $variantId : null;

        $stmt->execute([$orderId, $productId, $dbVariantId, $quantity, $unitPrice]);
    }

    public static function createPayment($pdo, $orderId, $method, $amount)
    {
        $sql = "INSERT INTO payment (order_id, payment_method, amount, payment_status) 
                VALUES (?, ?, ?, 'PENDING')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $method, $amount]);
    }

    public static function getCouponIdByCode($pdo, $code)
    {
        if (empty($code)) return null;
        $stmt = $pdo->prepare("SELECT coupon_id FROM coupons WHERE code = ? AND is_active = 1");
        $stmt->execute([$code]);
        return $stmt->fetchColumn() ?: null;
    }

    public static function updateUserPhone($pdo, $userId, $phone)
    {
        $stmt = $pdo->prepare("UPDATE user SET phone = ? WHERE user_id = ?");
        $stmt->execute([$phone, $userId]);
    }

    public static function reduceStock($pdo, $productId, $variantId, $quantity)
    {
        if ($variantId > 0) {
            $sql = "UPDATE product_variants SET quantity = quantity - ? WHERE variant_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$quantity, $variantId]);
        } else {
        }
    }

    public static function clearCart($pdo, $userId)
    {
        if ($userId) {
            $stmt = $pdo->prepare("DELETE FROM cart_item WHERE cart_id IN (SELECT cart_id FROM cart WHERE user_id = ?)");
            $stmt->execute([$userId]);
        }
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }
}
