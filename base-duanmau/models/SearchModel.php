<?php
class SearchModel
{
    // 1. Hàm tìm kiếm chính
    public static function searchProducts($pdo, $keyword, $categoryIds = [], $brandId = 0, $maxPrice = 0, $limit = 12, $page = 1, $sort = 'newest')
    {
        $offset = ($page - 1) * $limit;
        $keywordParam = "%" . trim($keyword) . "%";

        // FIX: Đặt 3 tham số riêng biệt để tránh lỗi PDO ở một số server
        $params = [
            ':kw1' => $keywordParam,
            ':kw2' => $keywordParam,
            ':kw3' => $keywordParam
        ];

        // SQL: Đã sửa lại GROUP BY để tránh lỗi "only_full_group_by"
        $sql = "SELECT 
                    p.*, 
                    v.current_price, 
                    v.original_price,
                    v.default_variant_id,
                    v.image_url,
                    (v.original_price - v.current_price) as discount_amount,
                    CASE 
                        WHEN v.original_price > 0 THEN ((v.original_price - v.current_price) / v.original_price * 100)
                        ELSE 0 
                    END as discount_percent
                FROM products p
                JOIN (
                    SELECT 
                        product_id, 
                        MIN(current_variant_price) as current_price, 
                        MAX(original_variant_price) as original_price,
                        MAX(variant_id) as default_variant_id,
                        MAX(main_image_url) as image_url
                    FROM product_variants 
                    GROUP BY product_id
                ) v ON p.product_id = v.product_id
                WHERE p.is_active = 1 
                AND (p.name LIKE :kw1 OR p.short_description LIKE :kw2 OR p.detail_description LIKE :kw3)";

        if (!empty($categoryIds)) {
            $inQuery = "";
            foreach ($categoryIds as $i => $id) {
                $key = ":cat" . $i;
                $inQuery .= ($inQuery ? "," : "") . $key;
                $params[$key] = $id;
            }
            $sql .= " AND p.category_id IN ($inQuery)";
        }

        if ($brandId > 0) {
            $sql .= " AND p.brand_id = :brand_id";
            $params[':brand_id'] = $brandId;
        }

        if ($maxPrice > 0) {
            $sql .= " AND v.current_price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }

        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY current_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY current_price DESC";
                break;
            default:
                $sql .= " ORDER BY p.product_id DESC";
                break;
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Hàm đếm số lượng (Cũng fix tham số :kw1, :kw2, :kw3)
    public static function countSearchResults($pdo, $keyword, $categoryIds = [], $brandId = 0, $maxPrice = 0)
    {
        $keywordParam = "%" . trim($keyword) . "%";
        $params = [
            ':kw1' => $keywordParam,
            ':kw2' => $keywordParam,
            ':kw3' => $keywordParam
        ];

        $sql = "SELECT COUNT(*) FROM products p 
                JOIN (
                    SELECT product_id, MIN(current_variant_price) as min_price 
                    FROM product_variants 
                    GROUP BY product_id
                ) v ON p.product_id = v.product_id
                WHERE p.is_active = 1 
                AND (p.name LIKE :kw1 OR p.short_description LIKE :kw2 OR p.detail_description LIKE :kw3)";

        if (!empty($categoryIds)) {
            $inQuery = "";
            foreach ($categoryIds as $i => $id) {
                $key = ":cat" . $i;
                $inQuery .= ($inQuery ? "," : "") . $key;
                $params[$key] = $id;
            }
            $sql .= " AND p.category_id IN ($inQuery)";
        }

        if ($brandId > 0) {
            $sql .= " AND p.brand_id = :brand_id";
            $params[':brand_id'] = $brandId;
        }

        if ($maxPrice > 0) {
            $sql .= " AND v.min_price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // 3. Hàm gợi ý (Cũng fix tham số)
    public static function getSearchSuggestions($pdo, $keyword, $limit = 5)
    {
        // % ở đầu và cuối để tìm tương đối
        $keywordParam = "%" . trim($keyword) . "%";

        // Mẹo: Nếu DB là utf8_general_ci, nó tự hiểu 'a' == 'á'. 
        // Nếu muốn chắc chắn hơn, ta có thể dùng OR với keyword không dấu (nếu PHP xử lý keyword)
        // Nhưng ở đây ta dùng query đơn giản và hiệu quả nhất.

        $sql = "SELECT 
                    p.product_id, 
                    p.name, 
                    v.price,
                    v.image
                FROM products p
                JOIN (
                    SELECT 
                        product_id, 
                        MIN(current_variant_price) as price, 
                        MAX(main_image_url) as image
                    FROM product_variants 
                    GROUP BY product_id
                ) v ON p.product_id = v.product_id
                WHERE p.is_active = 1 
                AND (p.name LIKE :kw1 OR p.short_description LIKE :kw2)
                ORDER BY p.name ASC
                LIMIT :limit";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':kw1', $keywordParam);
        $stmt->bindValue(':kw2', $keywordParam);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
