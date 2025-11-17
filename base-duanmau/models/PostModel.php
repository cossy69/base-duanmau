<?php
class PostModel
{
    /**
     * Lấy các bài viết để hiển thị
     * (Đã chuyển từ HomeController)
     */
    public static function getPosts($pdo, $limit)
    {
        try {
            $sql = "
                SELECT 
                    p.post_id, 
                    p.title, 
                    p.content, 
                    p.thumbnail_url,
                    u.full_name AS author_name,
                    p.created_at
                FROM post p
                JOIN user u ON p.user_id = u.user_id
                WHERE p.is_published = 1
                ORDER BY p.created_at DESC
                LIMIT ?
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([(int)$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Lỗi khi lấy bài viết: " . $e->getMessage();
            return [];
        }
    }
}
