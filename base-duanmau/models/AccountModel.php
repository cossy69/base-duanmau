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
        $sql = "SELECT * FROM `order` WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
