<?php
// Đảm bảo đã gọi file kết nối CSDL
include_once __DIR__ . '/../config/db_connection.php';
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/FavoriteController.php';

class ProductController
{

    /**
     * Hàm chính để hiển thị trang sản phẩm (act=product)
     */
    public function product()
    {
        // ... (Code hàm này giữ nguyên như file của anh)
        global $pdo;
        $limit = 9;
        $maxPriceDefault = 50000000;
        $maxPrice = (int)($_GET['max_price'] ?? $maxPriceDefault);
        $currentPage = (int)($_GET['page'] ?? 1);
        $sortOrder = (string)($_GET['sort'] ?? 'newest');
        $filterCategories = (array)($_GET['category'] ?? []);
        $filterBrandId = (int)($_GET['brand'] ?? 0);
        $filterCategories = array_map('intval', $filterCategories);
        $filters = [
            'categories' => $filterCategories,
            'brand_id'   => $filterBrandId,
            'max_price'  => $maxPrice,
            'max_price_default' => $maxPriceDefault
        ];
        $result = $this->getProducts($pdo, $filters, $sortOrder, $currentPage, $limit);
        $products = $result['products'];
        $totalProducts = $result['total'];
        $categories = $this->getCategories($pdo);
        $brands = $this->getBrands($pdo);
        $cartItemCount = CartController::getCartItemCount();
        $totalPages = ceil($totalProducts / $limit);
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteController::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteController::getFavoriteProductIds($pdo, $userId);
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/product.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * ===================================================================
     * HÀM MỚI: HIỂN THỊ TRANG CHI TIẾT SẢN PHẨM (act=product_detail)
     * ===================================================================
     */
    public function product_detail()
    {
        global $pdo;
        $productId = (int)($_GET['id'] ?? 0);
        if ($productId <= 0) {
            echo "Sản phẩm không hợp lệ.";
            return;
        }

        // 1. Lấy thông tin cơ bản
        $product = $this->getProductDetails($pdo, $productId);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }

        // 2. Lấy tùy chọn
        $variantOptions = $this->getVariantOptions($pdo, $productId);

        // 3. Lấy thư viện ảnh
        $galleryImages = $this->getGalleryImages($pdo, $productId, $product['main_image_url']);

        // 4. Lấy đánh giá
        $reviewSummary = $this->getReviewSummary($pdo, $productId);
        $reviews = $this->getReviews($pdo, $productId);

        // 5. (MỚI) Lấy thông số kỹ thuật
        $productSpecs = $this->getProductSpecs($pdo, $productId);

        // 6. Lấy số lượng giỏ hàng
        $cartItemCount = CartController::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteController::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteController::getFavoriteProductIds($pdo, $userId);
        // 7. Gọi view
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        // Truyền thêm biến $productSpecs
        require_once './views/user/product_detail.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function getVariantDetails()
    {
        global $pdo;
        $productId = (int)($_POST['product_id'] ?? 0);
        $optionValueIds = (array)($_POST['options'] ?? []); // Mảng các value_id

        if ($productId <= 0 || empty($optionValueIds)) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }

        sort($optionValueIds);
        $numOptions = count($optionValueIds);
        $placeholders = str_repeat('?,', $numOptions - 1) . '?';

        // --- SỬA CÂU SQL ---
        // Thay vì dùng subquery "=(SELECT...)",
        // chúng ta dùng JOIN, GROUP BY và HAVING ở query chính
        $sql = "
            SELECT 
                pv.variant_id, 
                pv.current_variant_price, 
                pv.original_variant_price, 
                pv.quantity,
                COALESCE(pv.main_image_url, p.main_image_url) as image_url
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.product_id
            
            -- Join với bảng map
            JOIN variant_attribute_map vam ON pv.variant_id = vam.variant_id
            
            WHERE pv.product_id = ? 
              -- Lọc các biến thể chứa CÁC value_id đã chọn
              AND vam.value_id IN ($placeholders)
            
            -- Gom nhóm theo từng biến thể
            GROUP BY 
                pv.variant_id, 
                pv.current_variant_price, 
                pv.original_variant_price, 
                pv.quantity,
                p.main_image_url,
                pv.main_image_url
            
            -- Chỉ lấy biến thể nào có SỐ LƯỢNG thuộc tính khớp
            HAVING COUNT(DISTINCT vam.value_id) = ?
            
            LIMIT 1
        ";

        // Params giờ là: [product_id, ...value_ids, numOptions]
        $params = array_merge([$productId], $optionValueIds, [$numOptions]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $variant = $stmt->fetch();
        // --- KẾT THÚC SỬA ---

        if ($variant) {
            $this->jsonResponse('success', 'Tìm thấy biến thể.', $variant);
        } else {
            // Lỗi này xảy ra khi tổ hợp không tồn tại
            // (Ví dụ: MacBook Bạc + 256GB + 16GB RAM (nếu ko có))
            $this->jsonResponse('error', 'Không tìm thấy phiên bản phù hợp.');
        }
    }

    /**
     * Helper: Trả về JSON
     */
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }


    /**
     * ===================================================================
     * CÁC HÀM HELPER MỚI CHO product_detail()
     * ===================================================================
     */

    /**
     * Lấy thông tin chi tiết (cơ bản) của sản phẩm
     */
    private function getProductDetails($pdo, $productId)
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
     * Lấy các nhóm tùy chọn (VD: [Màu sắc => [Xanh, Đỏ], Dung lượng => [128GB, 256GB]])
     */
    private function getVariantOptions($pdo, $productId)
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
            // Gom nhóm theo tên thuộc tính
            $options[$row['attribute_name']][] = [
                'value' => $row['attribute_value'],
                'value_id' => $row['value_id']
            ];
        }
        return $options; // Kết quả: ['Màu sắc' => [...], 'Dung lượng' => [...]]
    }

    /**
     * Lấy thư viện ảnh (ảnh chính + ảnh của các biến thể)
     */
    private function getGalleryImages($pdo, $productId, $mainImageUrl)
    {
        $sql = "
        (SELECT 
            '$mainImageUrl' as image_url, 
            0 as sort_order
        )
        
        UNION DISTINCT  -- SỬA 1: Dùng UNION DISTINCT để loại bỏ ảnh trùng lặp
        
        (SELECT 
            -- Lấy ảnh gallery (vi.image_url) hoặc ảnh thumbnail (pv.main_image_url)
            COALESCE(vi.image_url, pv.main_image_url) as image_url, 
            
            -- SỬA 2: Ưu tiên sort_order của ảnh gallery, 
            -- nếu là ảnh thumbnail (không có) thì mặc định là 1 (sau ảnh chính)
            COALESCE(vi.sort_order, 1) as sort_order
            
        FROM product_variants pv
        -- Dùng LEFT JOIN để lấy cả những variant có ảnh gallery và không có
        LEFT JOIN variant_images vi ON pv.variant_id = vi.variant_id
        WHERE pv.product_id = ? 
          -- Chỉ lấy những ảnh có đường dẫn (không bị NULL)
          AND COALESCE(vi.image_url, pv.main_image_url) IS NOT NULL
        )
        
        ORDER BY sort_order, image_url -- Sắp xếp theo thứ tự và cả tên ảnh
    ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);

        // Dùng FETCH_COLUMN để chỉ lấy cột image_url
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Lấy tóm tắt đánh giá (điểm TB, % các sao)
     */
    private function getReviewSummary($pdo, $productId)
    {
        $sql = "
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as '5_star',
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as '4_star',
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as '3_star',
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as '2_star',
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as '1_star'
            FROM review
            WHERE product_id = ? AND is_approved = 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        $summary = $stmt->fetch();

        // Xử lý chia cho 0 nếu chưa có đánh giá
        $summary['avg_rating'] = $summary['total_reviews'] > 0 ? $summary['avg_rating'] : 0;
        $summary['percentages'] = [
            '5' => $summary['total_reviews'] > 0 ? ($summary['5_star'] / $summary['total_reviews']) * 100 : 0,
            '4' => $summary['total_reviews'] > 0 ? ($summary['4_star'] / $summary['total_reviews']) * 100 : 0,
            '3' => $summary['total_reviews'] > 0 ? ($summary['3_star'] / $summary['total_reviews']) * 100 : 0,
            '2' => $summary['total_reviews'] > 0 ? ($summary['2_star'] / $summary['total_reviews']) * 100 : 0,
            '1' => $summary['total_reviews'] > 0 ? ($summary['1_star'] / $summary['total_reviews']) * 100 : 0,
        ];

        return $summary;
    }

    /**
     * Lấy danh sách các đánh giá
     */
    private function getReviews($pdo, $productId)
    {
        $sql = "
            SELECT r.*, u.full_name 
            FROM review r
            JOIN user u ON r.user_id = u.user_id
            WHERE r.product_id = ? AND r.is_approved = 1
            ORDER BY r.review_date DESC
            LIMIT 10 -- Lấy 10 đánh giá mới nhất
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    private function getProductSpecs($pdo, $productId)
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

            // Gom nhóm lại cho view
            $specs = [];
            foreach ($rows as $row) {
                // Key là tên nhóm (VD: 'Màn hình'), value là mảng các thông số
                $specs[$row['spec_group']][] = [
                    'name' => $row['spec_name'],
                    'value' => $row['spec_value']
                ];
            }
            return $specs; // ['Màn hình' => [...], 'Hiệu năng' => [...]]

        } catch (PDOException $e) {
            echo "Lỗi khi lấy thông số: " . $e->getMessage();
            return [];
        }
    }

    /**
     * ===================================================================
     * CÁC HÀM CŨ TỪ trang product (getProducts, getCategories, getBrands)
     * (Giữ nguyên, không thay đổi)
     * ===================================================================
     */
    private function getProducts($pdo, $filters, $sort, $page, $limit)
    {
        // ... (Code hàm này giữ nguyên như file của anh)
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
            SELECT COUNT(*) 
            FROM (
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
            $whereSql
            GROUP BY p.product_id, p.name, p.main_image_url
            $havingSql
            $orderBySql
            $limitSql
        ";
        $mainParams = array_merge($params, $havingParams, [(int)$limit, (int)$offset]);
        $stmt = $pdo->prepare($mainSql);
        $stmt->execute($mainParams);
        $products = $stmt->fetchAll();
        return ['products' => $products, 'total' => $total];
    }

    public static function getCategories($pdo)
    {
        // ... (Code hàm này giữ nguyên như file của anh)
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

    private function getBrands($pdo)
    {
        // ... (Code hàm này giữ nguyên như file của anh)
        try {
            $stmt = $pdo->prepare("SELECT brand_id, name FROM brands ORDER BY name ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Lỗi khi lấy thương hiệu: " . $e->getMessage();
            return [];
        }
    }
}
