<?php
class AdminModel
{
    public static function getDashboardStats($pdo)
    {
        $stmt = $pdo->query("SELECT SUM(total_amount) FROM `order` WHERE order_status = 'COMPLETED'");
        $revenue = $stmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("SELECT COUNT(*) FROM `order`");
        $totalOrders = $stmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("SELECT COUNT(*) FROM user WHERE is_admin = 0");
        $totalUsers = $stmt->fetchColumn() ?: 0;

        return [
            'revenue' => $revenue,
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers
        ];
    }

    public static function getRevenueStats($pdo, $timeframe = 'month')
    {
        $sql = "";
        $baseWhere = "WHERE order_status = 'COMPLETED'";

        switch ($timeframe) {
            case 'day':
                $sql = "SELECT DATE_FORMAT(created_at, '%d/%m') as label, SUM(total_amount) as total 
                        FROM `order` 
                        $baseWhere AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY DATE(created_at) ORDER BY created_at ASC";
                break;

            case 'week':
                $sql = "SELECT CONCAT('Tuần ', WEEK(created_at)) as label, SUM(total_amount) as total 
                        FROM `order` 
                        $baseWhere AND created_at >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
                        GROUP BY WEEK(created_at) ORDER BY created_at ASC";
                break;

            case 'year':
                $sql = "SELECT YEAR(created_at) as label, SUM(total_amount) as total 
                        FROM `order` 
                        $baseWhere AND created_at >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
                        GROUP BY YEAR(created_at) ORDER BY created_at ASC";
                break;

            case 'month':
            default:
                $sql = "SELECT CONCAT('Tháng ', MONTH(created_at)) as label, SUM(total_amount) as total 
                        FROM `order` 
                        $baseWhere AND YEAR(created_at) = YEAR(CURRENT_DATE())
                        GROUP BY MONTH(created_at) ORDER BY created_at ASC";
                break;
        }

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function getTopProducts($pdo)
    {
        $sql = "SELECT p.name, SUM(od.quantity) as sold
                FROM order_detail od
                JOIN products p ON od.product_id = p.product_id
                JOIN `order` o ON od.order_id = o.order_id
                WHERE o.order_status = 'COMPLETED'
                GROUP BY p.product_id
                ORDER BY sold DESC
                LIMIT 5";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllProducts($pdo)
    {
        // Hiển thị tất cả sản phẩm trong admin (cả active và inactive)
        // Thêm trạng thái is_active để admin biết sản phẩm nào đang ẩn
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                       MIN(pv.current_variant_price) as current_price,
                       CASE WHEN p.is_active = 1 THEN 'Đang hiển thị' ELSE 'Đã ẩn' END as status_text
                FROM products p
                LEFT JOIN category c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN product_variants pv ON p.product_id = pv.product_id AND pv.is_active = 1
                GROUP BY p.product_id
                ORDER BY p.is_active DESC, p.product_id DESC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addProduct($pdo, $data)
    {
        // SỬA: Bỏ cột price trong câu lệnh INSERT
        $sql = "INSERT INTO products (category_id, brand_id, name, short_description, detail_description, main_image_url) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['category_id'],
            $data['brand_id'],
            $data['name'],
            // Bỏ $data['price'],
            $data['short_description'],
            $data['detail_description'],
            $data['image']
        ]);
        return $pdo->lastInsertId();
    }

    public static function deleteProduct($pdo, $id)
    {
        // Sử dụng soft delete thay vì hard delete để tránh lỗi foreign key constraint
        // Chỉ ẩn sản phẩm khỏi frontend, không xóa khỏi database
        $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?");
        return $stmt->execute([$id]);
    }

    public static function restoreProduct($pdo, $id)
    {
        // Khôi phục sản phẩm đã ẩn
        $stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE product_id = ?");
        return $stmt->execute([$id]);
    }

    public static function getOrders($pdo, $status = 'all', $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        // SQL: Lấy thêm Payment Method và Tóm tắt sản phẩm
        $sql = "SELECT o.*, u.full_name as user_name, u.email as user_email,
                       pm.payment_method, pm.payment_status,
                       GROUP_CONCAT(CONCAT(p.name, ' (x', od.quantity, ')') SEPARATOR '<br>') as product_summary
                FROM `order` o 
                LEFT JOIN user u ON o.user_id = u.user_id
                LEFT JOIN payment pm ON o.order_id = pm.order_id
                LEFT JOIN order_detail od ON o.order_id = od.order_id
                LEFT JOIN products p ON od.product_id = p.product_id";

        if ($status !== 'all') {
            $sql .= " WHERE o.order_status = ?";
            // Group By để gộp sản phẩm lại theo đơn hàng
            $sql .= " GROUP BY o.order_id ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $sql .= " GROUP BY o.order_id ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countOrders($pdo, $status = 'all')
    {
        $sql = "SELECT COUNT(*) FROM `order`";
        if ($status !== 'all') {
            $sql .= " WHERE order_status = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchColumn();
    }

    public static function updateOrderStatus($pdo, $orderId, $status)
    {
        // Lấy trạng thái hiện tại của đơn hàng
        $stmt = $pdo->prepare("SELECT order_status FROM `order` WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $currentStatus = $stmt->fetchColumn();
        
        // Cập nhật trạng thái đơn hàng
        $stmt = $pdo->prepare("UPDATE `order` SET order_status = ? WHERE order_id = ?");
        $result = $stmt->execute([$status, $orderId]);
        
        // Nếu đơn hàng được hủy và trước đó không phải là CANCELLED, khôi phục tồn kho
        if ($result && $status === 'CANCELLED' && $currentStatus !== 'CANCELLED') {
            include_once __DIR__ . '/OrderModel.php';
            
            // Lấy danh sách sản phẩm trong đơn hàng
            $stmt = $pdo->prepare("SELECT product_id, variant_id, quantity FROM order_detail WHERE order_id = ?");
            $stmt->execute([$orderId]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Khôi phục tồn kho cho từng sản phẩm
            foreach ($orderItems as $item) {
                OrderModel::restoreStock($pdo, $item['product_id'], $item['variant_id'], $item['quantity']);
            }
        }
        
        return $result;
    }

    public static function getAllUsers($pdo)
    {
        return $pdo->query("SELECT * FROM user ORDER BY user_id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function toggleUserStatus($pdo, $userId, $status)
    {
        $stmt = $pdo->prepare("UPDATE user SET is_disabled = ? WHERE user_id = ?");
        return $stmt->execute([$status, $userId]);
    }
    public static function getOrderDetails($pdo, $orderId)
    {
        $sql = "SELECT od.*, p.name, p.main_image_url 
                FROM order_detail od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getOrderWithCoupon($pdo, $orderId)
    {
        $sql = "SELECT o.*, c.code as coupon_code 
                FROM `order` o
                LEFT JOIN coupons c ON o.coupon_id = c.coupon_id
                WHERE o.order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function updateUserRole($pdo, $userId, $newRole)
    {
        // Chuyển đổi role number sang string
        $roleValue = $newRole == 1 ? 'admin' : 'user';
        
        // Cập nhật cả role và is_admin
        $stmt = $pdo->prepare("UPDATE user SET role = ?, is_admin = ? WHERE user_id = ?");
        return $stmt->execute([$roleValue, $newRole, $userId]);
    }
    public static function getUserById($pdo, $userId)
    {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getProductById($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateProduct($pdo, $id, $data)
    {
        // SỬA: Bỏ price = ? trong câu lệnh UPDATE
        $sql = "UPDATE products SET 
                category_id = ?, brand_id = ?, name = ?, 
                short_description = ?, detail_description = ?, main_image_url = ?, is_active = ? 
                WHERE product_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['category_id'],
            $data['brand_id'],
            $data['name'],
            // Bỏ $data['price'],
            $data['short_description'],
            $data['detail_description'],
            $data['image'],
            $data['is_active'],
            $id
        ]);
    }
    public static function getAllAttributes($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM attributes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addAttribute($pdo, $name)
    {
        $stmt = $pdo->prepare("INSERT INTO attributes (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public static function getAttributeValues($pdo, $attributeId)
    {
        $stmt = $pdo->prepare("SELECT * FROM attribute_values WHERE attribute_id = ?");
        $stmt->execute([$attributeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addAttributeValue($pdo, $attributeId, $value)
    {
        $stmt = $pdo->prepare("INSERT INTO attribute_values (attribute_id, value) VALUES (?, ?)");
        return $stmt->execute([$attributeId, $value]);
    }

    public static function deleteAttributeValue($pdo, $valueId)
    {
        // Kiểm tra xem giá trị này có đang được sử dụng trong biến thể nào không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM variant_attribute_map WHERE value_id = ?");
        $stmt->execute([$valueId]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            return false; // Không thể xóa vì đang được sử dụng
        }
        
        // Nếu không được sử dụng, có thể xóa an toàn
        $stmt = $pdo->prepare("DELETE FROM attribute_values WHERE value_id = ?");
        return $stmt->execute([$valueId]);
    }

    public static function getProductVariants($pdo, $productId)
    {
        $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($variants as &$v) {
            $sql = "SELECT a.name, av.value 
                    FROM variant_attribute_map vam
                    JOIN attribute_values av ON vam.value_id = av.value_id
                    JOIN attributes a ON av.attribute_id = a.attribute_id
                    WHERE vam.variant_id = ?";
            $stmtMap = $pdo->prepare($sql);
            $stmtMap->execute([$v['variant_id']]);
            $v['attributes'] = $stmtMap->fetchAll(PDO::FETCH_ASSOC);
        }
        return $variants;
    }

    public static function addProductVariant($pdo, $productId, $price, $originalPrice, $qty, $image)
    {
        $sql = "INSERT INTO product_variants (product_id, current_variant_price, original_variant_price, quantity, main_image_url) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$productId, $price, $originalPrice, $qty, $image]);
        return $pdo->lastInsertId();
    }

    public static function mapVariantAttribute($pdo, $variantId, $valueId)
    {
        $stmt = $pdo->prepare("INSERT INTO variant_attribute_map (variant_id, value_id) VALUES (?, ?)");
        $stmt->execute([$variantId, $valueId]);
    }

    public static function deleteVariant($pdo, $variantId)
    {
        // Kiểm tra xem variant có trong đơn hàng nào không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_detail WHERE variant_id = ?");
        $stmt->execute([$variantId]);
        $orderCount = $stmt->fetchColumn();
        
        if ($orderCount > 0) {
            // Nếu variant đã có trong đơn hàng, không thể xóa
            return false;
        }
        
        // Nếu chưa có trong đơn hàng nào, có thể xóa an toàn
        $stmt = $pdo->prepare("DELETE FROM product_variants WHERE variant_id = ?");
        return $stmt->execute([$variantId]);
    }

    public static function getVariantById($pdo, $variantId)
    {
        $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE variant_id = ?");
        $stmt->execute([$variantId]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($variant) {
            // Lấy thêm thông tin thuộc tính
            $sql = "SELECT a.name, av.value, av.value_id
                    FROM variant_attribute_map vam
                    JOIN attribute_values av ON vam.value_id = av.value_id
                    JOIN attributes a ON av.attribute_id = a.attribute_id
                    WHERE vam.variant_id = ?";
            $stmtMap = $pdo->prepare($sql);
            $stmtMap->execute([$variantId]);
            $variant['attributes'] = $stmtMap->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $variant;
    }

    public static function updateVariant($pdo, $variantId, $price, $originalPrice, $quantity, $image = null)
    {
        // Kiểm tra xem variant có trong đơn hàng nào không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_detail WHERE variant_id = ?");
        $stmt->execute([$variantId]);
        $orderCount = $stmt->fetchColumn();
        
        if ($orderCount > 0) {
            // Nếu variant đã có trong đơn hàng, tạo variant mới thay vì sửa
            // Lấy thông tin variant cũ
            $oldVariant = self::getVariantById($pdo, $variantId);
            if (!$oldVariant) return false;
            
            // Tạo variant mới với thông tin cập nhật
            $newImage = $image ?: $oldVariant['main_image_url'];
            $newVariantId = self::addProductVariant($pdo, $oldVariant['product_id'], $price, $originalPrice, $quantity, $newImage);
            
            // Copy thuộc tính từ variant cũ sang variant mới
            foreach ($oldVariant['attributes'] as $attr) {
                self::mapVariantAttribute($pdo, $newVariantId, $attr['value_id']);
            }
            
            // Ẩn variant cũ (soft delete) thay vì xóa hoàn toàn
            $stmt = $pdo->prepare("UPDATE product_variants SET is_active = 0 WHERE variant_id = ?");
            $stmt->execute([$variantId]);
            
            return $newVariantId;
        } else {
            // Nếu chưa có trong đơn hàng nào, có thể sửa trực tiếp
            $sql = "UPDATE product_variants SET 
                    current_variant_price = ?, 
                    original_variant_price = ?, 
                    quantity = ?";
            $params = [$price, $originalPrice, $quantity];
            
            if ($image) {
                $sql .= ", main_image_url = ?";
                $params[] = $image;
            }
            
            $sql .= " WHERE variant_id = ?";
            $params[] = $variantId;
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        }
    }

    public static function getFeedbacks($pdo, $status = 'all')
    {
        $sql = "SELECT * FROM customer_feedback";

        if ($status !== 'all') {
            $sql .= " WHERE status = :status";
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($sql);

        if ($status !== 'all') {
            $stmt->bindParam(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateFeedbackStatus($pdo, $id, $status)
    {
        $sql = "UPDATE customer_feedback SET status = ? WHERE feedback_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public static function deleteFeedback($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM customer_feedback WHERE feedback_id = ?");
        return $stmt->execute([$id]);
    }
    // --- QUẢN LÝ ĐÁNH GIÁ (REVIEWS) ---

    public static function getAllReviews($pdo)
    {
        // Join 3 bảng: review, products, user
        $sql = "SELECT r.*, p.name as product_name, p.main_image_url, u.full_name 
                FROM review r
                JOIN products p ON r.product_id = p.product_id
                JOIN user u ON r.user_id = u.user_id
                ORDER BY r.review_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteReview($pdo, $id)
    {
        $sql = "DELETE FROM review WHERE review_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public static function toggleReviewStatus($pdo, $id, $status)
    {
        // status: 1 là duyệt, 0 là ẩn
        $sql = "UPDATE review SET is_approved = ? WHERE review_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
    public static function getAllCoupons($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM coupons ORDER BY coupon_id DESC"); // Hoặc order by coupon_id DESC nếu ko có created_at
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addCoupon($pdo, $data)
    {
        $sql = "INSERT INTO coupons (code, description, discount_type, discount_value, max_discount_value, min_order_amount, usage_limit, start_date, end_date, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['code'],
            $data['description'],
            $data['discount_type'],
            $data['discount_value'],
            $data['max_discount_value'],
            $data['min_order_amount'],
            $data['usage_limit'],
            $data['start_date'],
            $data['end_date'],
            $data['is_active']
        ]);
    }

    public static function getCouponById($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateCoupon($pdo, $id, $data)
    {
        try {
            $sql = "UPDATE coupons SET 
                    code = ?, description = ?, discount_type = ?, discount_value = ?, 
                    max_discount_value = ?, min_order_amount = ?, usage_limit = ?, 
                    start_date = ?, end_date = ?, is_active = ?
                    WHERE coupon_id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['code'],
                $data['description'],
                $data['discount_type'],
                $data['discount_value'],
                $data['max_discount_value'],
                $data['min_order_amount'],
                $data['usage_limit'],
                $data['start_date'],
                $data['end_date'],
                $data['is_active'],
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function deleteCoupon($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM coupons WHERE coupon_id = ?");
        return $stmt->execute([$id]);
    }
    public static function isAttributeInUse($pdo, $attributeId)
    {
        // Join từ bảng map -> value -> attribute để đếm
        $sql = "SELECT COUNT(*) 
                FROM variant_attribute_map vam
                JOIN attribute_values av ON vam.value_id = av.value_id
                WHERE av.attribute_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$attributeId]);
        return $stmt->fetchColumn() > 0;
    }

    // Xóa thuộc tính (và tự động xóa các giá trị con nhờ CASCADE trong DB)
    public static function deleteAttribute($pdo, $attributeId)
    {
        $stmt = $pdo->prepare("DELETE FROM attributes WHERE attribute_id = ?");
        return $stmt->execute([$attributeId]);
    }
}
