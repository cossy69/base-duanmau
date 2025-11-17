<?php
class UserModel
{
    /**
     * Tìm user bằng email (dùng cho đăng nhập)
     */
    public static function findUserByEmail($pdo, $email)
    {
        try {
            $stmt = $pdo->prepare("
                SELECT user_id, full_name, email, password_hash, is_admin 
                FROM user 
                WHERE email = ? AND is_disabled = 0
            ");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            // (Nên log lỗi $e->getMessage())
            return false;
        }
    }

    /**
     * Kiểm tra xem email đã tồn tại chưa (dùng cho đăng ký)
     */
    public static function checkIfEmailExists($pdo, $email)
    {
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            // rowCount() > 0 nghĩa là đã tồn tại
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Tạo user mới (dùng cho đăng ký)
     */
    public static function createUser($pdo, $fullName, $email, $passwordHash)
    {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO user (full_name, email, password_hash, is_admin, is_disabled) 
                VALUES (?, ?, ?, 0, 0)
            ");
            // execute() trả về true nếu thành công
            return $stmt->execute([$fullName, $email, $passwordHash]);
        } catch (PDOException $e) {
            // (Nên log lỗi $e->getMessage())
            return false;
        }
    }
}
