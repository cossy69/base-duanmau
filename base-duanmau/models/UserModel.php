<?php
class UserModel
{
    public static function findUserByEmail($pdo, $email)
    {
        try {
            $stmt = $pdo->prepare("
            SELECT user_id, full_name, email, password_hash, is_admin, is_verified 
            FROM user 
            WHERE email = ? AND is_disabled = 0
        ");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function checkIfEmailExists($pdo, $email)
    {
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function createUser($pdo, $fullName, $email, $passwordHash, $token)
    {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO user (full_name, email, password_hash, is_admin, is_disabled, is_verified, verification_token) 
                VALUES (?, ?, ?, 0, 0, 0, ?)
            ");
            return $stmt->execute([$fullName, $email, $passwordHash, $token]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function verifyUser($pdo, $token)
    {
        $stmt = $pdo->prepare("SELECT user_id FROM user WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $update = $pdo->prepare("UPDATE user SET is_verified = 1, verification_token = NULL WHERE user_id = ?");
            return $update->execute([$user['user_id']]);
        }
        return false;
    }

    public static function createPasswordReset($pdo, $userId, $token)
    {
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$userId]);

        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        return $stmt->execute([$userId, $token]);
    }

    public static function verifyResetToken($pdo, $token)
    {
        $stmt = $pdo->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updatePassword($pdo, $userId, $newPasswordHash)
    {
        $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$newPasswordHash, $userId]);

        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$userId]);
        return true;
    }
    public static function updateRememberToken($pdo, $userId, $token)
    {
        $stmt = $pdo->prepare("UPDATE user SET remember_token = ? WHERE user_id = ?");
        return $stmt->execute([$token, $userId]);
    }

    public static function findUserByRememberToken($pdo, $token)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM user WHERE remember_token = ? AND is_disabled = 0");
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
