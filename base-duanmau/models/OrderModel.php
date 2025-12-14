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

    public static function validateAndApplyCoupon($pdo, $couponCode, $orderTotal)
    {
        if (empty($couponCode)) {
            return ['valid' => false, 'discount' => 0, 'message' => ''];
        }

        // Lấy thông tin coupon
        $stmt = $pdo->prepare("
            SELECT * FROM coupons 
            WHERE code = ? AND is_active = 1 
            AND start_date <= NOW() AND end_date >= NOW()
        ");
        $stmt->execute([$couponCode]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'];
        }

        // Kiểm tra đơn hàng tối thiểu
        if ($coupon['min_order_amount'] > 0 && $orderTotal < $coupon['min_order_amount']) {
            return [
                'valid' => false, 
                'discount' => 0, 
                'message' => 'Đơn hàng tối thiểu để sử dụng mã này là ' . number_format($coupon['min_order_amount']) . 'đ'
            ];
        }

        // Kiểm tra giới hạn sử dụng
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Mã giảm giá đã hết lượt sử dụng.'];
        }

        // Tính giảm giá
        $discount = 0;
        if ($coupon['discount_type'] === 'PERCENT') {
            $discount = $orderTotal * ($coupon['discount_value'] / 100);
            // Áp dụng giảm tối đa nếu có
            if ($coupon['max_discount_value'] > 0 && $discount > $coupon['max_discount_value']) {
                $discount = $coupon['max_discount_value'];
            }
        } else {
            $discount = $coupon['discount_value'];
        }

        // Không cho giảm quá tổng đơn hàng
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return [
            'valid' => true, 
            'discount' => $discount, 
            'coupon_id' => $coupon['coupon_id'],
            'message' => 'Áp dụng mã giảm giá thành công!'
        ];
    }

    public static function updateUserPhone($pdo, $userId, $phone)
    {
        $stmt = $pdo->prepare("UPDATE user SET phone = ? WHERE user_id = ?");
        $stmt->execute([$phone, $userId]);
    }

    public static function reduceStock($pdo, $productId, $variantId, $quantity)
    {
        if ($variantId > 0) {
            // Trừ tồn kho từ biến thể cụ thể
            $sql = "UPDATE product_variants SET quantity = quantity - ? WHERE variant_id = ? AND is_active = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$quantity, $variantId]);
        } else {
            // Nếu không có variantId, trừ từ biến thể đầu tiên có đủ tồn kho
            $sql = "UPDATE product_variants SET quantity = quantity - ? 
                    WHERE product_id = ? AND is_active = 1 AND quantity >= ? 
                    ORDER BY variant_id ASC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$quantity, $productId, $quantity]);
        }
    }

    public static function restoreStock($pdo, $productId, $variantId, $quantity)
    {
        if ($variantId > 0) {
            // Cộng lại tồn kho cho biến thể cụ thể
            $sql = "UPDATE product_variants SET quantity = quantity + ? WHERE variant_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$quantity, $variantId]);
        } else {
            // Nếu không có variantId, cộng vào biến thể đầu tiên
            $sql = "UPDATE product_variants SET quantity = quantity + ? 
                    WHERE product_id = ? AND is_active = 1 
                    ORDER BY variant_id ASC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$quantity, $productId]);
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
