<?php
class ContactModel
{
    public static function addFeedback($pdo, $fullName, $email, $subject, $message)
    {
        try {
            $sql = "INSERT INTO customer_feedback (full_name, email, title, content, status, created_at) 
                    VALUES (?, ?, ?, ?, 'NEW', NOW())";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$fullName, $email, $subject, $message]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
