<?php
class SearchModel
{
    public static function searchProducts($pdo, $keyword, $categoryIds = [], $brandId = 0, $maxPrice = 0, $limit = 12, $page = 1, $sort = 'newest')
    {
        $offset = ($page - 1) * $limit;
        $keywordParam = "%" . trim($keyword) . "%";
        $params = [':keyword' => $keywordParam];

        $sql = "SELECT 
                    p.*, 
                    v.current_variant_price as current_price, 
                    v.original_variant_price as original_price,
                    v.variant_id as default_variant_id,
                    v.main_image_url as image_url,
                    (v.original_variant_price - v.current_variant_price) as discount_amount,
                    CASE 
                        WHEN v.original_variant_price > 0 THEN ((v.original_variant_price - v.current_variant_price) / v.original_variant_price * 100)
                        ELSE 0 
                    END as discount_percent
                FROM products p
                JOIN (
                    SELECT product_id, MIN(current_variant_price) as min_price, current_variant_price, original_variant_price, variant_id, main_image_url
                    FROM product_variants 
                    GROUP BY product_id
                ) v ON p.product_id = v.product_id
                WHERE p.is_active = 1 AND p.name LIKE :keyword";


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
            $sql .= " AND v.current_variant_price <= :max_price";
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

    public static function countSearchResults($pdo, $keyword, $categoryIds = [], $brandId = 0, $maxPrice = 0)
    {
        $keywordParam = "%" . trim($keyword) . "%";
        $params = [':keyword' => $keywordParam];

        $sql = "SELECT COUNT(*) FROM products p 
                JOIN (SELECT product_id, current_variant_price FROM product_variants GROUP BY product_id) v 
                ON p.product_id = v.product_id
                WHERE p.is_active = 1 AND p.name LIKE :keyword";

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
            $sql .= " AND v.current_variant_price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
