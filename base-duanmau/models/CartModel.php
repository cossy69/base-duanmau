<?php
class CartModel
{
    /**
     * Lấy toàn bộ nội dung giỏ hàng (cho cả user và khách)
     */
    public static function getCartContents($pdo)
    {
        $cartItems = [];
        $subtotal = 0;

        if (isset($_SESSION['user_id'])) {
            // --- User đã đăng nhập (DB) ---
            $userId = $_SESSION['user_id'];
            $sql = "
                SELECT
                    p.product_id, pv.variant_id, p.name,
                    COALESCE(pv.current_variant_price, p.price) AS price,
                    ci.quantity,
                    (COALESCE(pv.current_variant_price, p.price) * ci.quantity) AS item_total,
                    COALESCE(pv.main_image_url, p.main_image_url) AS image_url,
                    GROUP_CONCAT(CONCAT(a.name, ': ', av.value) SEPARATOR ', ') AS variant_details
                FROM cart c
                JOIN cart_item ci ON c.cart_id = ci.cart_id
                JOIN products p ON ci.product_id = p.product_id
                LEFT JOIN product_variants pv ON ci.variant_id = pv.variant_id
                LEFT JOIN variant_attribute_map vam ON pv.variant_id = vam.variant_id
                LEFT JOIN attribute_values av ON vam.value_id = av.value_id
                LEFT JOIN attributes a ON av.attribute_id = a.attribute_id
                WHERE c.user_id = ?
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
            if (!empty($variantIds)) {
                $placeholders = implode(',', array_fill(0, count($variantIds), '?'));
                $sqlVariants = "
                    SELECT
                        pv.variant_id, p.name, pv.current_variant_price AS price,
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
                $productsInfo += $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            }
            if (!empty($productIds)) {
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $sqlProducts = "
                    SELECT
                        product_id, name, price, main_image_url AS image_url,
                        NULL AS variant_details
                    FROM products
                    WHERE product_id IN ($placeholders)
                ";
                $stmt = $pdo->prepare($sqlProducts);
                $stmt->execute($productIds);
                $productsInfo += $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            }
            foreach ($_SESSION['cart'] as $key => $item) {
                $lookupKey = ($item['variant_id'] !== null) ? $item['variant_id'] : $item['product_id'];
                if (isset($productsInfo[$lookupKey])) {
                    $product = $productsInfo[$lookupKey];
                    $quantity = $item['quantity'];
                    $itemTotal = $product['price'] * $quantity;
                    $cartItems[] = [
                        'name' => $product['name'],
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'],
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'item_total' => $itemTotal,
                        'image_url' => $product['image_url'],
                        'variant_details' => $product['variant_details']
                    ];
                    $subtotal += $itemTotal;
                }
            }
        }
        return ['items' => $cartItems, 'subtotal' => $subtotal];
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (DB hoặc Session)
     */
    public static function addToCart($productId, $variantId, $quantity, $userId)
    {
        if ($userId) {
            return self::handleLoggedInUserAdd($productId, $variantId, $quantity, $userId);
        } else {
            return self::handleGuestUserAdd($productId, $variantId, $quantity);
        }
    }

    /**
     * Cập nhật số lượng (DB hoặc Session)
     */
    public static function updateQuantity($productId, $variantId, $quantity, $userId)
    {
        if ($userId) {
            global $pdo;
            $stmt = $pdo->prepare("
                UPDATE cart_item ci
                JOIN cart c ON ci.cart_id = c.cart_id
                SET ci.quantity = ?
                WHERE ci.variant_id <=> ? AND ci.product_id = ? AND c.user_id = ?
            ");
            return $stmt->execute([$quantity, $variantId, $productId, $userId]);
        } else {
            $sessionKey = ($variantId === null) ? 'p_' . $productId : 'v_' . $variantId;
            if (isset($_SESSION['cart'][$sessionKey])) {
                $_SESSION['cart'][$sessionKey]['quantity'] = $quantity;
                return true;
            }
            return false;
        }
    }

    /**
     * Xóa các sản phẩm đã chọn (DB hoặc Session)
     */
    public static function removeSelectedItems($items, $userId)
    {
        if ($userId) {
            return self::handleLoggedInUserRemoveMultiple($items, $userId);
        } else {
            return self::handleGuestUserRemoveMultiple($items);
        }
    }

    /**
     * Lấy tổng số lượng (cho icon header)
     */
    public static function getCartItemCount()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $totalQuantity = 0;
            foreach ($_SESSION['cart'] ?? [] as $item) {
                $totalQuantity += (int)$item['quantity'];
            }
            return $totalQuantity;
        }
        try {
            global $pdo;
            if (!$pdo) {
                include_once __DIR__ . '/../config/db_connection.php';
            }
            $stmt = $pdo->prepare("
                SELECT SUM(ci.quantity) 
                FROM cart c
                JOIN cart_item ci ON c.cart_id = c.cart_id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // --- CÁC HÀM HELPER (PRIVATE) CHO MODEL ---

    private static function handleLoggedInUserAdd($productId, $variantId, $quantity, $userId)
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

            $stmt = $pdo->prepare("SELECT quantity FROM cart_item WHERE cart_id = ? AND variant_id <=> ? AND product_id = ?");
            $stmt->execute([$cartId, $variantId, $productId]);
            $existingItem = $stmt->fetch();

            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $quantity;
                $stmt = $pdo->prepare("UPDATE cart_item SET quantity = ? WHERE cart_id = ? AND variant_id <=> ? AND product_id = ?");
                $stmt->execute([$newQuantity, $cartId, $variantId, $productId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart_item (cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)");
                $stmt->execute([$cartId, $productId, $variantId, $quantity]);
            }
            return true;
        } catch (PDOException $e) {
            // (Nên log lỗi $e->getMessage() ở đây)
            return false;
        }
    }

    private static function handleGuestUserAdd($productId, $variantId, $quantity)
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $sessionKey = ($variantId === null) ? 'p_' . $productId : 'v_' . $variantId;
        if (isset($_SESSION['cart'][$sessionKey])) {
            $_SESSION['cart'][$sessionKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$sessionKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity
            ];
        }
        return true;
    }

    private static function handleLoggedInUserRemoveMultiple($items, $userId)
    {
        global $pdo;
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                DELETE ci FROM cart_item ci
                JOIN cart c ON ci.cart_id = c.cart_id
                WHERE ci.variant_id <=> ? AND ci.product_id = ? AND c.user_id = ?
            ");
            foreach ($items as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $variantId = $item['variant_id'] ?? null;
                $dbVariantId = ($variantId <= 0) ? null : (int)$variantId;
                $stmt->execute([$dbVariantId, $productId, $userId]);
            }
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }

    private static function handleGuestUserRemoveMultiple($items)
    {
        if (!isset($_SESSION['cart'])) {
            return true;
        }
        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $variantId = $item['variant_id'] ?? null;
            $dbVariantId = ($variantId <= 0) ? null : (int)$variantId;
            $sessionKey = ($dbVariantId === null) ? 'p_' . $productId : 'v_' . $dbVariantId;
            if (isset($_SESSION['cart'][$sessionKey])) {
                unset($_SESSION['cart'][$sessionKey]);
            }
        }
        return true;
    }
}
