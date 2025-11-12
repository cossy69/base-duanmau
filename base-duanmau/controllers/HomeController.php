<?php
// Đảm bảo đã gọi file kết nối CSDL
// Dùng __DIR__ để đảm bảo đường dẫn luôn đúng,
// bất kể file này được gọi từ đâu.
include_once __DIR__ . '/../config/db_connection.php';

class HomeController
{

    /**
     * Hàm chính để hiển thị trang chủ
     * Tên hàm (home) tương ứng với 'act=home' trên URL
     */
    public function home()
    {
        global $pdo; // Lấy biến kết nối PDO

        // 1. Lấy sản phẩm và thương hiệu
        $newProducts = $this->getProducts($pdo, "p.product_id DESC", 8);
        $bestDeals = $this->getProducts($pdo, "discount_percent DESC", 10);
        $brands = $this->getBrands($pdo);

        // 2. Lấy bài viết (MỚI)
        // Lấy 4 bài, bài đầu tiên làm bài chính, 3 bài sau làm bài phụ
        $allPosts = $this->getPosts($pdo, 4);

        $mainPost = null;  // Biến cho bài viết chính
        $sidePosts = []; // Mảng cho 3 bài viết phụ

        if (!empty($allPosts)) {
            $mainPost = array_shift($allPosts); // Lấy phần tử đầu tiên
            $sidePosts = $allPosts;             // Lấy các phần tử còn lại
        }

        // 3. Gọi file view 'home.php' và truyền dữ liệu vào
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/home.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * Hàm dùng chung để lấy thông tin sản phẩm
     * @param PDO $pdo - Biến kết nối PDO
     * @param string $orderBy - Điều kiện sắp xếp (VD: "p.product_id DESC")
     * @param int $limit - Số lượng sản phẩm cần lấy
     * @return array - Mảng chứa các sản phẩm
     */
    private function getProducts($pdo, $orderBy, $limit)
    {
        // ... (phần select giữ nguyên) ...
        $sql = "
            SELECT
                p.product_id,
                p.name,
                MIN(pv.original_variant_price) as original_price,
                MIN(pv.current_variant_price) as current_price,
                COALESCE( p.main_image_url) AS image_url,
                (MIN(pv.original_variant_price) - MIN(pv.current_variant_price)) AS discount_amount,
                
                CASE 
                    WHEN MIN(pv.original_variant_price) > 0 AND MIN(pv.original_variant_price) > MIN(pv.current_variant_price)
                    THEN ((MIN(pv.original_variant_price) - MIN(pv.current_variant_price)) / MIN(pv.original_variant_price)) * 100
                    ELSE 0 
                END AS discount_percent
                
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.name, p.main_image_url
            ORDER BY p.product_id DESC
            LIMIT ? 
        "; // SỬA 1: Đổi $limit thành ?

        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$limit]); // SỬA 2: Truyền $limit vào execute
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách các thương hiệu
     */
    private function getBrands($pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT brand_id, name FROM brands ORDER BY name ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Xử lý lỗi (nếu có)
            echo "Lỗi khi lấy thương hiệu: " . $e->getMessage();
            return [];
        }
    }

    // SỬA 3: Xóa đoạn code bị lặp lại (từ dòng 102 đến 111 trong file gốc)

    /**
     * Lấy các bài viết để hiển thị (HÀM MỚI)
     */
    private function getPosts($pdo, $limit)
    {
        try {
            $sql = "
                SELECT 
                    p.post_id, 
                    p.title, 
                    p.content, 
                    p.thumbnail_url,
                    u.full_name AS author_name, -- SỬA 4: Thêm dấu phẩy
                    p.created_at
                FROM post p
                JOIN user u ON p.user_id = u.user_id
                WHERE p.is_published = 1
                ORDER BY p.created_at DESC
                LIMIT ?
            ";

            $stmt = $pdo->prepare($sql);
            // PDO cần $limit là kiểu int
            $stmt->execute([(int)$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Lỗi khi lấy bài viết: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Hàm helper để rút gọn văn bản (HÀM MỚI)
     */
    private function truncate($text, $length = 200, $suffix = '...')
    {
        // ... (Hàm này đã đúng, giữ nguyên) ...
        if (mb_strlen($text, 'UTF-8') <= $length) {
            return $text;
        }
        $truncated = mb_substr($text, 0, $length, 'UTF-8');
        $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
        }
        return $truncated . $suffix;
    }
}
