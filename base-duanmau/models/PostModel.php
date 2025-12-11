<?php
class PostModel
{
    public static function getAllPosts($pdo)
    {
        $sql = "SELECT p.*, u.full_name as author_name, c.name as category_name 
                FROM post p
                LEFT JOIN user u ON p.user_id = u.user_id
                LEFT JOIN category c ON p.category_id = c.category_id
                ORDER BY p.created_at DESC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPostById($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM post WHERE post_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function addPost($pdo, $data)
    {
        $sql = "INSERT INTO post (user_id, category_id, title, content, thumbnail_url, is_published) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['user_id'],
            $data['category_id'],
            $data['title'],
            $data['content'],
            $data['thumbnail_url'],
            $data['is_published']
        ]);
    }

    public static function updatePost($pdo, $id, $data)
    {
        $sql = "UPDATE post SET 
                category_id = ?, title = ?, content = ?, thumbnail_url = ?, is_published = ? 
                WHERE post_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['content'],
            $data['thumbnail_url'],
            $data['is_published'],
            $id
        ]);
    }

    public static function deletePost($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM post WHERE post_id = ?");
        return $stmt->execute([$id]);
    }

    public static function filterPosts($pdo, $keyword = '', $limit = 10, $page = 1)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.*, u.full_name as author_name 
                FROM post p
                LEFT JOIN user u ON p.user_id = u.user_id
                WHERE p.is_published = 1";

        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
            $keywordParam = "%$keyword%";
            $params[] = $keywordParam;
            $params[] = $keywordParam;
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPublishedPosts($pdo, $limit = 10, $page = 1)
    {
        return self::filterPosts($pdo, '', $limit, $page);
    }
}
