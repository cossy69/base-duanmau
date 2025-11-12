<?php
// Giả sử anh đã có file kết nối database và khởi tạo session ở một nơi nào đó trong dự án
include_once './config/db_connection.php'; // File kết nối PDO của anh

class CartController
{
    public function cart()
    {
        // Lấy dữ liệu giỏ hàng
        $cartData = $this->getCartContents();
        // Truyền dữ liệu này ra view scope
        $cartItems = $cartData['items'];
        $subtotal = $cartData['subtotal'];

        // Lấy số lượng cho header
        $cartItemCount = self::getCartItemCount();
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/cart.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function addToCart()
    {
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        // Chỉ cần ProductID và Quantity là bắt buộc
        if ($productId <= 0 || $quantity <= 0) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ (thiếu product_id hoặc quantity).');
            return;
        }

        // Chuyển 0 thành NULL để lưu vào DB/Session
        $dbVariantId = ($variantId <= 0) ? null : $variantId;

        if (isset($_SESSION['user_id'])) {
            $this->handleLoggedInUser($productId, $dbVariantId, $quantity, $_SESSION['user_id']);
        } else {
            $this->handleGuestUser($productId, $dbVariantId, $quantity);
        }
    }

    // --- Xử lý cho người dùng đã đăng nhập ---
    private function handleLoggedInUser($productId, $variantId, $quantity, $userId)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cart = $stmt->fetch();
            $cartId = $cart ? $cart['cart_id'] : null;

            if (!$cartId) {
                $stmt = $pdo->prepare("INSERT INTO cart (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                $cartId = $pdo->lastInsertId();
            }

            // Dùng <=> (NULL-safe equals) để so sánh variant_id (kể cả khi nó là NULL)
            $stmt = $pdo->prepare("SELECT quantity FROM cart_item WHERE cart_id = ? AND variant_id <=> ? AND product_id = ?");
            $stmt->execute([$cartId, $variantId, $productId]);
            $existingItem = $stmt->fetch();

            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $quantity;
                // Dùng <=> (NULL-safe equals)
                $stmt = $pdo->prepare("UPDATE cart_item SET quantity = ? WHERE cart_id = ? AND variant_id <=> ? AND product_id = ?");
                $stmt->execute([$newQuantity, $cartId, $variantId, $productId]);
            } else {
                // INSERT thì NULL vẫn hoạt động bình thường
                $stmt = $pdo->prepare("INSERT INTO cart_item (cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)");
                $stmt->execute([$cartId, $productId, $variantId, $quantity]);
            }
            $totalQuantity = self::getCartItemCount();
            $this->jsonResponse('success', 'Đã thêm vào giỏ hàng!', ['total_quantity' => $totalQuantity]);
        } catch (PDOException $e) {
            $this->jsonResponse('error', 'Lỗi cơ sở dữ liệu: ' . $e->getMessage());
        }
    }

    // --- Xử lý cho khách (chưa đăng nhập) ---
    private function handleGuestUser($productId, $variantId, $quantity)
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Tạo key: v_1 (variant_id 1) hoặc p_11 (product_id 11)
        $sessionKey = ($variantId === null) ? 'p_' . $productId : 'v_' . $variantId;

        if (isset($_SESSION['cart'][$sessionKey])) {
            $_SESSION['cart'][$sessionKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$sessionKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId, // Sẽ là NULL nếu không có
                'quantity' => $quantity
            ];
        }
        $totalQuantity = self::getCartItemCount();
        $this->jsonResponse('success', 'Đã thêm vào giỏ hàng!', ['total_quantity' => $totalQuantity]);
    }

    public function getCartContents()
    {
        global $pdo;
        $cartItems = [];
        $subtotal = 0;

        if (isset($_SESSION['user_id'])) {
            // --- User đã đăng nhập (DB) ---
            $userId = $_SESSION['user_id'];
            $sql = "
                SELECT
                    p.product_id,
                    pv.variant_id,    
                    p.name,
                    -- Lấy giá biến thể, nếu không có thì lấy giá gốc của sản phẩm
                    COALESCE(pv.current_variant_price, p.price) AS price,
                    ci.quantity,
                    -- Tính tổng tiền dựa trên giá đã COALESCE
                    (COALESCE(pv.current_variant_price, p.price) * ci.quantity) AS item_total,
                    -- Lấy ảnh biến thể, nếu không có thì lấy ảnh gốc của sản phẩm
                    COALESCE(pv.main_image_url, p.main_image_url) AS image_url,
                    -- GROUP_CONCAT sẽ tự động trả NULL nếu không có join (sản phẩm không có biến thể)
                    GROUP_CONCAT(CONCAT(a.name, ': ', av.value) SEPARATOR ', ') AS variant_details
                FROM cart c
                JOIN cart_item ci ON c.cart_id = ci.cart_id
                -- Join product TRƯỚC
                JOIN products p ON ci.product_id = p.product_id
                -- LEFT JOIN với variants và attributes
                LEFT JOIN product_variants pv ON ci.variant_id = pv.variant_id
                LEFT JOIN variant_attribute_map vam ON pv.variant_id = vam.variant_id
                LEFT JOIN attribute_values av ON vam.value_id = av.value_id
                LEFT JOIN attributes a ON av.attribute_id = a.attribute_id
                WHERE c.user_id = ?
                -- Group by ID sản phẩm VÀ ID biến thể
                GROUP BY p.product_id, pv.variant_id, p.name, p.price, ci.quantity, image_url
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $cartItems = $stmt->fetchAll();
            foreach ($cartItems as $item) {
                $subtotal += $item['item_total'];
            }
        } else if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            // --- User là khách (Session) ---

            // 1. Tách ID biến thể và ID sản phẩm ra
            $variantIds = [];
            $productIds = [];
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['variant_id'] !== null) {
                    $variantIds[] = $item['variant_id'];
                } else {
                    $productIds[] = $item['product_id'];
                }
            }

            $productsInfo = [];

            // 2. Query lấy thông tin các BIẾN THỂ (nếu có)
            if (!empty($variantIds)) {
                $placeholders = implode(',', array_fill(0, count($variantIds), '?'));
                $sqlVariants = "
                    SELECT
                        pv.variant_id, 
                        p.name,
                        pv.current_variant_price AS price,
                        COALESCE(pv.main_image_url, p.main_image_url) AS image_url,
                        GROUP_CONCAT(CONCAT(a.name, ': ', av.value) SEPARATOR ', ') AS variant_details
                    FROM product_variants pv
                    JOIN products p ON pv.product_id = p.product_id
                    LEFT JOIN variant_attribute_map vam ON pv.variant_id = vam.variant_id
                    LEFT JOIN attribute_values av ON vam.value_id = av.value_id
                    LEFT JOIN attributes a ON av.attribute_id = a.attribute_id
                    WHERE pv.variant_id IN ($placeholders)
                    GROUP BY pv.variant_id, p.name, pv.current_variant_price, image_url
                ";
                $stmt = $pdo->prepare($sqlVariants);
                $stmt->execute($variantIds);
                // Dùng FETCH_UNIQUE để lấy variant_id làm key
                $productsInfo += $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            }

            // 3. Query lấy thông tin các SẢN PHẨM (nếu có)
            if (!empty($productIds)) {
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $sqlProducts = "
                    SELECT
                        product_id,
                        name,
                        price,
                        main_image_url AS image_url,
                        NULL AS variant_details -- Đặt là NULL để khớp cấu trúc
                    FROM products
                    WHERE product_id IN ($placeholders)
                ";
                $stmt = $pdo->prepare($sqlProducts);
                $stmt->execute($productIds);
                // Dùng FETCH_UNIQUE để lấy product_id làm key
                $productsInfo += $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            }

            // 4. Gộp dữ liệu từ session và DB
            foreach ($_SESSION['cart'] as $key => $item) {
                // Xác định key để tìm trong $productsInfo
                $lookupKey = ($item['variant_id'] !== null) ? $item['variant_id'] : $item['product_id'];

                if (isset($productsInfo[$lookupKey])) {
                    $product = $productsInfo[$lookupKey];
                    $quantity = $item['quantity'];
                    $itemTotal = $product['price'] * $quantity;

                    $cartItems[] = [
                        'name' => $product['name'],
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'], // Sẽ là NULL nếu không có
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'item_total' => $itemTotal,
                        'image_url' => $product['image_url'],
                        'variant_details' => $product['variant_details'] // Sẽ là NULL nếu không có
                    ];
                    $subtotal += $itemTotal;
                }
            }
        }

        return [
            'items' => $cartItems,
            'subtotal' => $subtotal
        ];
    }
    /**
     * Cập nhật số lượng của một sản phẩm trong giỏ hàng
     */
    public function updateQuantity()
    {
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        // Cần quantity, và (product_id hoặc variant_id)
        if ($quantity <= 0 || ($productId <= 0 && $variantId <= 0)) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }
        $dbVariantId = ($variantId <= 0) ? null : $variantId;

        if (isset($_SESSION['user_id'])) {
            global $pdo;
            $stmt = $pdo->prepare("
                UPDATE cart_item ci
                JOIN cart c ON ci.cart_id = c.cart_id
                SET ci.quantity = ?
                WHERE ci.variant_id <=> ? AND ci.product_id = ? AND c.user_id = ?
            ");
            $stmt->execute([$quantity, $dbVariantId, $productId, $_SESSION['user_id']]);
        } else {
            $sessionKey = ($dbVariantId === null) ? 'p_' . $productId : 'v_' . $dbVariantId;
            if (isset($_SESSION['cart'][$sessionKey])) {
                $_SESSION['cart'][$sessionKey]['quantity'] = $quantity;
            } else {
                $this->jsonResponse('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
                return;
            }
        }

        $cartData = $this->getCartContents();
        $cartData['total_quantity'] = self::getCartItemCount();
        $this->jsonResponse('success', 'Cập nhật thành công!', $cartData);
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng
     */
    public function removeItem()
    {
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;

        if ($productId <= 0 && $variantId <= 0) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }
        $dbVariantId = ($variantId <= 0) ? null : $variantId;

        if (isset($_SESSION['user_id'])) {
            global $pdo;
            $stmt = $pdo->prepare("
                DELETE ci FROM cart_item ci
                JOIN cart c ON ci.cart_id = c.cart_id
                WHERE ci.variant_id <=> ? AND ci.product_id = ? AND c.user_id = ?
            ");
            $stmt->execute([$dbVariantId, $productId, $_SESSION['user_id']]);
        } else {
            $sessionKey = ($dbVariantId === null) ? 'p_' . $productId : 'v_' . $dbVariantId;
            if (isset($_SESSION['cart'][$sessionKey])) {
                unset($_SESSION['cart'][$sessionKey]);
            } else {
                $this->jsonResponse('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
                return;
            }
        }

        $cartData = $this->getCartContents();
        $cartData['total_quantity'] = self::getCartItemCount();
        $this->jsonResponse('success', 'Đã xóa sản phẩm.', $cartData);
    }

    /**
     * Hàm helper để trả về JSON response cho gọn
     */
    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
    /**
     * Hàm NHANH để đếm số lượng sản phẩm trong giỏ hàng
     * Dùng "static" để có thể gọi mà không cần tạo đối tượng "new CartController()"
     */
    /**
     * Sửa: Hàm static siêu nhẹ để TÍNH TỔNG SỐ LƯỢNG cho header
     */
    public static function getCartItemCount()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            // Khách: Tính tổng quantity trong session
            $totalQuantity = 0;
            foreach ($_SESSION['cart'] ?? [] as $item) {
                // $item['quantity'] là số lượng của 1 loại sản phẩm
                $totalQuantity += (int)$item['quantity'];
            }
            return $totalQuantity;
        }

        // User đã đăng nhập: Dùng SUM(quantity) trong DB
        try {
            global $pdo;
            if (!$pdo) {
                include_once __DIR__ . '/../config/db_connection.php';
            }

            // Đổi từ COUNT(ci.cart_item_id) thành SUM(ci.quantity)
            $stmt = $pdo->prepare("
                SELECT SUM(ci.quantity) 
                FROM cart c
                JOIN cart_item ci ON c.cart_id = c.cart_id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            // fetchColumn() sẽ trả về tổng số lượng, hoặc 0 nếu không có gì
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
