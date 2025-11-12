<?php
// Giả sử anh đã có file kết nối database và khởi tạo session ở một nơi nào đó trong dự án
include_once './config/db_connection.php'; // File kết nối PDO của anh

class CartController
{
    public function cart()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/cart.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function addToCart()
    {
        // --- BƯỚC 1: LẤY DỮ LIỆU GỬI LÊN ---
        // Dữ liệu này sẽ được gửi từ JavaScript phía client
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        // --- BƯỚC 2: KIỂM TRA DỮ LIỆU ĐẦU VÀO ---
        if ($variantId <= 0 || $productId <= 0 || $quantity <= 0) {
            // Gửi phản hồi lỗi nếu dữ liệu không hợp lệ
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        // --- BƯỚC 3: PHÂN LOẠI USER VÀ XỬ LÝ GIỎ HÀNG ---
        if (isset($_SESSION['user_id'])) {
            // **TRƯỜNG HỢP 1: USER ĐÃ ĐĂNG NHẬP -> LƯU VÀO DATABASE**
            $this->handleLoggedInUser($productId, $variantId, $quantity, $_SESSION['user_id']);
        } else {
            // **TRƯỜNG HỢP 2: USER CHƯA ĐĂNG NHẬP (KHÁCH) -> LƯU VÀO SESSION**
            $this->handleGuestUser($productId, $variantId, $quantity);
        }
    }

    // --- Xử lý cho người dùng đã đăng nhập ---
    private function handleLoggedInUser($productId, $variantId, $quantity, $userId)
    {
        global $pdo; // Sử dụng biến kết nối CSDL toàn cục

        try {
            // 1. Tìm hoặc tạo giỏ hàng cho user
            $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cart = $stmt->fetch();

            $cartId = null;
            if ($cart) {
                $cartId = $cart['cart_id'];
            } else {
                // Nếu user chưa có giỏ hàng, tạo mới
                $stmt = $pdo->prepare("INSERT INTO cart (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                $cartId = $pdo->lastInsertId();
            }

            // 2. Kiểm tra xem sản phẩm (biến thể) đã có trong giỏ hàng chưa
            $stmt = $pdo->prepare("SELECT quantity FROM cart_item WHERE cart_id = ? AND variant_id = ?");
            $stmt->execute([$cartId, $variantId]);
            $existingItem = $stmt->fetch();

            if ($existingItem) {
                // Nếu đã có, cập nhật số lượng
                $newQuantity = $existingItem['quantity'] + $quantity;
                $stmt = $pdo->prepare("UPDATE cart_item SET quantity = ? WHERE cart_id = ? AND variant_id = ?");
                $stmt->execute([$newQuantity, $cartId, $variantId]);
            } else {
                // Nếu chưa có, thêm mới
                $stmt = $pdo->prepare("INSERT INTO cart_item (cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)");
                $stmt->execute([$cartId, $productId, $variantId, $quantity]);
            }

            // Gửi phản hồi thành công
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!']);
        } catch (PDOException $e) {
            // Xử lý lỗi CSDL
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
        }
    }

    // --- Xử lý cho khách (chưa đăng nhập) ---
    private function handleGuestUser($productId, $variantId, $quantity)
    {
        // 1. Khởi tạo giỏ hàng trong session nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // 2. Kiểm tra xem sản phẩm (biến thể) đã có trong giỏ hàng session chưa
        if (isset($_SESSION['cart'][$variantId])) {
            // Nếu đã có, cập nhật số lượng
            $_SESSION['cart'][$variantId]['quantity'] += $quantity;
        } else {
            // Nếu chưa có, thêm mới
            // Ở đây mình có thể lấy thêm thông tin sản phẩm để lưu vào session cho tiện hiển thị
            // global $pdo;
            // $stmt = $pdo->prepare("SELECT p.name, pv.current_variant_price, pv.main_image_url FROM products p JOIN product_variants pv ON p.product_id = pv.product_id WHERE pv.variant_id = ?");
            // $stmt->execute([$variantId]);
            // $productInfo = $stmt->fetch();

            $_SESSION['cart'][$variantId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                // 'name' => $productInfo['name'], // Lấy từ DB
                // 'price' => $productInfo['current_variant_price'], // Lấy từ DB
                // 'image' => $productInfo['main_image_url'], // Lấy từ DB
            ];
        }

        // Gửi phản hồi thành công
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    public function getCartContents()
    {
        global $pdo; // Lấy biến kết nối PDO

        $cartItems = [];
        $subtotal = 0;

        if (isset($_SESSION['user_id'])) {
            // ... (Phần này của user đăng nhập đã đúng) ...
            $userId = $_SESSION['user_id'];
            $sql = "
                SELECT
                    pv.variant_id,    
                    p.name,
                    pv.current_variant_price AS price,
                    ci.quantity,
                    (pv.current_variant_price * ci.quantity) AS item_total,
                    COALESCE(pv.main_image_url, p.main_image_url) AS image_url,
                    GROUP_CONCAT(CONCAT(a.name, ': ', av.value) SEPARATOR ', ') AS variant_details
                FROM cart c
                JOIN cart_item ci ON c.cart_id = ci.cart_id
                JOIN product_variants pv ON ci.variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                LEFT JOIN variant_attribute_map vam ON pv.variant_id = vam.variant_id
                LEFT JOIN attribute_values av ON vam.value_id = av.value_id
                LEFT JOIN attributes a ON av.attribute_id = a.attribute_id
                WHERE c.user_id = ?
                GROUP BY pv.variant_id, p.name, pv.current_variant_price, ci.quantity, image_url
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $cartItems = $stmt->fetchAll();
            foreach ($cartItems as $item) {
                $subtotal += $item['item_total'];
            }
        } else if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            // --- TRƯỜNG HỢP 2: USER LÀ KHÁCH (LẤY TỪ SESSION) ---
            $variantIds = array_keys($_SESSION['cart']);
            $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

            // SỬA 2: Đưa pv.variant_id lên làm cột đầu tiên
            $sql = "
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

            $stmt = $pdo->prepare($sql);
            $stmt->execute($variantIds);

            // SỬA 3: Dùng PDO::FETCH_UNIQUE để lấy variant_id làm key
            $productsInfo = $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

            // Gộp thông tin từ DB và số lượng từ SESSION
            foreach ($_SESSION['cart'] as $variantId => $itemData) {
                if (isset($productsInfo[$variantId])) {
                    $product = $productsInfo[$variantId];
                    $quantity = $itemData['quantity'];
                    $itemTotal = $product['price'] * $quantity;

                    $cartItems[] = [
                        'name' => $product['name'],
                        'variant_id' => $variantId,
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

        // Trả về dữ liệu để View có thể sử dụng
        return [
            'items' => $cartItems,
            'subtotal' => $subtotal
        ];
    }
}
