<?php
class ProductModel
{
    /**
     * Lấy sản phẩm (đơn giản, cho trang chủ)
     * (Chuyển từ HomeController, đổi tên từ getProducts)
     */
    public static function getProductsSimple($pdo, $orderBy, $limit, $brandId = null)
    {
        $params = [];
        $whereClause = "WHERE p.is_active = 1";

        if ($brandId !== null) {
            $whereClause .= " AND p.brand_id = ?";
            $params[] = (int)$brandId;
        }

        $params[] = (int)$limit;

        $sql = "
            SELECT
                p.product_id,
                MIN(pv.variant_id) as default_variant_id,
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
            $whereClause
            GROUP BY p.product_id, p.name, p.main_image_url
            ORDER BY $orderBy
            LIMIT ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm (phức tạp, cho trang product, có filter)
     * (Chuyển từ ProductController, đổi tên từ getProducts)
     */
    public static function getProductsFiltered($pdo, $filters, $sort, $page, $limit)
    {
        $params = [];
        $whereClauses = ["p.is_active = 1"];
        $havingParams = [];
        $havingClauses = [];
        if (!empty($filters['categories'])) {
            $in = str_repeat('?,', count($filters['categories']) - 1) . '?';
            $whereClauses[] = "p.category_id IN ($in)";
            $params = array_merge($params, $filters['categories']);
        }
        if (!empty($filters['brand_id'])) {
            $whereClauses[] = "p.brand_id = ?";
            $params[] = (int)$filters['brand_id'];
        }
        $whereSql = "WHERE " . implode(" AND ", $whereClauses);
        $maxPriceDefault = $filters['max_price_default'] ?? 50000000;
        if (!empty($filters['max_price']) && (int)$filters['max_price'] < $maxPriceDefault) {
            $havingClauses[] = "MIN(pv.current_variant_price) <= ?";
            $havingParams[] = (int)$filters['max_price'];
        }
        $havingSql = !empty($havingClauses) ? "HAVING " . implode(" AND ", $havingClauses) : "";
        $orderBySql = "ORDER BY ";
        switch ($sort) {
            case 'price_asc':
                $orderBySql .= "current_price ASC";
                break;
            case 'price_desc':
                $orderBySql .= "current_price DESC";
                break;
            default:
                $orderBySql .= "p.product_id DESC";
        }
        $offset = ($page - 1) * $limit;
        $limitSql = "LIMIT ? OFFSET ?";
        $countSql = "
            SELECT COUNT(*) FROM (
                SELECT p.product_id
                FROM products p
                JOIN product_variants pv ON p.product_id = pv.product_id
                $whereSql
                GROUP BY p.product_id
                $havingSql
            ) as count_subquery
        ";
        $countParams = array_merge($params, $havingParams);
        $stmtCount = $pdo->prepare($countSql);
        $stmtCount->execute($countParams);
        $total = $stmtCount->fetchColumn();
        $mainSql = "
            SELECT
                p.product_id, MIN(pv.variant_id) as default_variant_id, p.name,
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
            $whereSql
            GROUP BY p.product_id, p.name, p.main_image_url
            $havingSql $orderBySql $limitSql
        ";
        $mainParams = array_merge($params, $havingParams, [(int)$limit, (int)$offset]);
        $stmt = $pdo->prepare($mainSql);
        $stmt->execute($mainParams);
        return ['products' => $stmt->fetchAll(), 'total' => $total];
    }

    /**
     * Lấy danh mục (Dùng chung)
     */
    public static function getCategories($pdo)
    {
        try {
            $sql = "
                SELECT c.category_id, c.name, COUNT(p.product_id) as product_count
                FROM category c
                LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1
                GROUP BY c.category_id, c.name
                ORDER BY c.name ASC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Lỗi khi lấy danh mục: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Lấy thương hiệu (Dùng chung)
     */
    public static function getBrands($pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT brand_id, name FROM brands ORDER BY name ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Lỗi khi lấy thương hiệu: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Lấy thông tin chi tiết (cơ bản) của sản phẩm
     */
    public static function getProductDetails($pdo, $productId)
    {
        $sql = "
            SELECT 
                p.*, 
                b.name as brand_name, 
                c.name as category_name,
                MIN(pv.current_variant_price) as default_price,
                MIN(pv.original_variant_price) as default_original_price,
                SUM(pv.quantity) as total_stock
            FROM products p
            JOIN brands b ON p.brand_id = b.brand_id
            JOIN category c ON p.category_id = c.category_id
            LEFT JOIN product_variants pv ON p.product_id = pv.product_id
            WHERE p.product_id = ? AND p.is_active = 1
            GROUP BY p.product_id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /**
     * Lấy các nhóm tùy chọn
     */
    public static function getVariantOptions($pdo, $productId)
    {
        $sql = "
            SELECT 
                a.name as attribute_name,
                av.value as attribute_value,
                av.value_id
            FROM attributes a
            JOIN attribute_values av ON a.attribute_id = av.attribute_id
            JOIN variant_attribute_map vam ON av.value_id = vam.value_id
            JOIN product_variants pv ON vam.variant_id = pv.variant_id
            WHERE pv.product_id = ?
            GROUP BY a.attribute_id, a.name, av.value_id, av.value
            ORDER BY a.attribute_id, av.value_id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        $options = [];
        while ($row = $stmt->fetch()) {
            $options[$row['attribute_name']][] = [
                'value' => $row['attribute_value'],
                'value_id' => $row['value_id']
            ];
        }
        return $options;
    }

    /**
     * Lấy thư viện ảnh
     */
    public static function getGalleryImages($pdo, $productId, $mainImageUrl)
    {
        $sql = "
            (SELECT '$mainImageUrl' as image_url, 0 as sort_order)
            UNION DISTINCT
            (SELECT 
                COALESCE(vi.image_url, pv.main_image_url) as image_url, 
                COALESCE(vi.sort_order, 1) as sort_order
            FROM product_variants pv
            LEFT JOIN variant_images vi ON pv.variant_id = vi.variant_id
            WHERE pv.product_id = ? 
              AND COALESCE(vi.image_url, pv.main_image_url) IS NOT NULL
            )
            ORDER BY sort_order, image_url
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Lấy thông số kỹ thuật
     */
    public static function getProductSpecs($pdo, $productId)
    {
        $sql = "
            SELECT spec_group, spec_name, spec_value 
            FROM product_specs 
            WHERE product_id = ? 
            ORDER BY spec_group, sort_order ASC
        ";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$productId]);
            $rows = $stmt->fetchAll();
            $specs = [];
            foreach ($rows as $row) {
                $specs[$row['spec_group']][] = [
                    'name' => $row['spec_name'],
                    'value' => $row['spec_value']
                ];
            }
            return $specs;
        } catch (PDOException $e) {
            echo "Lỗi khi lấy thông số: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Lấy chi tiết biến thể bằng các ID tùy chọn (cho AJAX)
     */
    public static function fetchVariantDetailsByOptions($pdo, $productId, $optionValueIds)
    {
        sort($optionValueIds);
        $numOptions = count($optionValueIds);
        $placeholders = str_repeat('?,', $numOptions - 1) . '?';

        $sql = "
            SELECT 
                pv.variant_id, 
                pv.current_variant_price, 
                pv.original_variant_price, 
                pv.quantity,
                COALESCE(pv.main_image_url, p.main_image_url) as image_url
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.product_id
            JOIN variant_attribute_map vam ON pv.variant_id = vam.variant_id
            WHERE pv.product_id = ? 
              AND vam.value_id IN ($placeholders)
            GROUP BY 
                pv.variant_id, 
                pv.current_variant_price, 
                pv.original_variant_price, 
                pv.quantity,
                p.main_image_url,
                pv.main_image_url
            HAVING COUNT(DISTINCT vam.value_id) = ?
            LIMIT 1
        ";

        $params = array_merge([$productId], $optionValueIds, [$numOptions]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
