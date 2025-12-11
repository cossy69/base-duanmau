<?php
class AccountModel
{
    public static function getUserById($pdo, $userId)
    {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateProfile($pdo, $userId, $fullName, $phone, $address)
    {
        $stmt = $pdo->prepare("UPDATE user SET full_name = ?, phone = ?, address = ? WHERE user_id = ?");
        return $stmt->execute([$fullName, $phone, $address, $userId]);
    }

    public static function changePassword($pdo, $userId, $newHash)
    {
        $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE user_id = ?");
        return $stmt->execute([$newHash, $userId]);
    }

    public static function getOrderHistory($pdo, $userId)
    {
        $sql = "SELECT o.*, p.payment_method, p.payment_status 
                FROM `order` o 
                LEFT JOIN payment p ON o.order_id = p.order_id 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xác nhận đã nhận hàng - chuyển trạng thái từ DELIVERED sang COMPLETED
    public static function confirmReceipt($pdo, $orderId, $userId)
    {
        // Kiểm tra đơn hàng thuộc về user này và có trạng thái DELIVERED
        $stmt = $pdo->prepare("SELECT order_id FROM `order` WHERE order_id = ? AND user_id = ? AND order_status = 'DELIVERED'");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();
        
        if (!$order) {
            return false;
        }
        
        // Cập nhật trạng thái thành COMPLETED
        $stmt = $pdo->prepare("UPDATE `order` SET order_status = 'COMPLETED' WHERE order_id = ? AND user_id = ?");
        return $stmt->execute([$orderId, $userId]);
    }
}
