<?php
include_once './config/db_connection.php';
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/FavoriteModel.php';
include_once __DIR__ . '/../models/ProductModel.php';

class CartController
{
    public function cart()
    {
        global $pdo;

        $cartData = CartModel::getCartContents($pdo);
        $cartItems = $cartData['items'];
        $subtotal = $cartData['subtotal'];
        $shippingMethods = CartModel::getShippingMethods($pdo);
        $coupons = CartModel::getAvailableCoupons($pdo);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $favoriteProductIds = FavoriteModel::getFavoriteProductIds($pdo, $userId);
        $userAddress = '';
        if (isset($_SESSION['user_id'])) {
            $userInfo = CartModel::getUserInfo($pdo, $_SESSION['user_id']);
            if (!empty($userInfo['address'])) {
                $userAddress = $userInfo['address'];
            }
        }
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/cart.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function addToCart()
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }

        $dbVariantId = ($variantId <= 0) ? null : $variantId;
        $userId = $_SESSION['user_id'] ?? null;


        $result = CartModel::addToCart($productId, $dbVariantId, $quantity, $userId);

        if ($result['success']) {
            $totalQuantity = CartModel::getCartItemCount();
            $this->jsonResponse('success', $result['message'], ['total_quantity' => $totalQuantity]);
        } else {
            $this->jsonResponse('error', $result['message']);
        }
    }


    public function updateQuantity()
    {
        global $pdo;
        $productId = (int)($_POST['product_id'] ?? 0);
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($quantity <= 0 || ($productId <= 0 && $variantId <= 0)) {
            $this->jsonResponse('error', 'Dữ liệu không hợp lệ.');
            return;
        }
        $dbVariantId = ($variantId <= 0) ? null : $variantId;
        $userId = $_SESSION['user_id'] ?? null;

        $result = CartModel::updateQuantity($productId, $dbVariantId, $quantity, $userId);

        if ($result['success']) {
            $cartData = CartModel::getCartContents($pdo);
            $cartData['total_quantity'] = CartModel::getCartItemCount();
            $this->jsonResponse('success', $result['message'], $cartData);
        } else {
            $this->jsonResponse('error', $result['message']);
        }
    }

    public function removeSelectedItems()
    {
        global $pdo;
        $itemsJson = $_POST['items_json'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            $this->jsonResponse('error', 'Không có sản phẩm nào được chọn.');
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;

        CartModel::removeSelectedItems($items, $userId);

        $cartData = CartModel::getCartContents($pdo);
        $cartData['total_quantity'] = CartModel::getCartItemCount();
        $this->jsonResponse('success', 'Đã xóa các sản phẩm đã chọn.', $cartData);
    }
    public function checkout()
    {
        global $pdo;
        
        // Lấy danh sách sản phẩm được chọn từ form
        $selectedItemsJson = $_POST['selected_items'] ?? '';
        $selectedItems = [];
        if (!empty($selectedItemsJson)) {
            $selectedItems = json_decode($selectedItemsJson, true) ?? [];
        }

        // Lấy toàn bộ giỏ hàng
        $cartData = CartModel::getCartContents($pdo);
        $allCartItems = $cartData['items'];
        
        // Nếu có sản phẩm được chọn, chỉ lấy các sản phẩm đó
        if (!empty($selectedItems)) {
            $cartItems = [];
            $subtotal = 0;
            
            foreach ($allCartItems as $item) {
                foreach ($selectedItems as $selected) {
                    if ($item['product_id'] == $selected['product_id'] && 
                        ($item['variant_id'] ?? 0) == ($selected['variant_id'] ?? 0)) {
                        // Cập nhật số lượng từ danh sách được chọn
                        $item['quantity'] = $selected['quantity'] ?? $item['quantity'];
                        $item['item_total'] = $item['price'] * $item['quantity'];
                        $cartItems[] = $item;
                        $subtotal += $item['item_total'];
                        break;
                    }
                }
            }
        } else {
            // Nếu không có sản phẩm nào được chọn, lấy tất cả
            $cartItems = $allCartItems;
            $subtotal = $cartData['subtotal'];
        }

        if (empty($cartItems)) {
            header('Location: index.php?class=cart&act=cart');
            exit;
        }

        $shippingFee = (int)($_POST['shipping_fee'] ?? 0);
        $shippingMethod = $_POST['shipping_method'] ?? 'Chưa tính';
        $discountAmount = (int)($_POST['discount_amount'] ?? 0);
        $couponCode = $_POST['coupon_code'] ?? '';
        $customerAddress = $_POST['customer_address'] ?? '';

        $totalAmount = $subtotal + $shippingFee - $discountAmount;

        $userPhone = '';
        $userName = '';
        $userEmail = '';

        if (isset($_SESSION['user_id'])) {
            $userInfo = CartModel::getUserInfo($pdo, $_SESSION['user_id']);
            if ($userInfo) {
                $userPhone = $userInfo['phone'];
                $userName = $userInfo['full_name'];
                $userEmail = $userInfo['email'];
            }
        }

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);
        $shippingMethods = CartModel::getShippingMethods($pdo);
        
        // Lưu selected_items vào session để dùng khi submit order
        if (!empty($selectedItems)) {
            $_SESSION['checkout_selected_items'] = json_encode($selectedItems);
        } else {
            unset($_SESSION['checkout_selected_items']);
        }

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/checkout.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
    public function calculateDistance()
    {
        $address = $_POST['address'] ?? '';
        
        if (empty($address)) {
            $this->jsonResponse('error', 'Vui lòng nhập địa chỉ.');
            return;
        }

        // Tọa độ shop
        $shopLat = 19.774325609178057;
        $shopLon = 105.78243656630887;

        try {
            // 1. Geocode địa chỉ bằng Nominatim
            $geoUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($address . ', Việt Nam') . '&limit=1&addressdetails=1';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $geoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'E-Commerce Shipping Calculator/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $geoResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$geoResponse) {
                throw new Exception('Không thể kết nối đến dịch vụ bản đồ.');
            }

            $geoData = json_decode($geoResponse, true);

            if (empty($geoData) || !isset($geoData[0]['lat']) || !isset($geoData[0]['lon'])) {
                throw new Exception('Không tìm thấy địa chỉ này trên bản đồ. Vui lòng nhập địa chỉ cụ thể hơn.');
            }

            $userLat = floatval($geoData[0]['lat']);
            $userLon = floatval($geoData[0]['lon']);

            // 2. Tính khoảng cách bằng Haversine (đường chim bay)
            $distanceKm = $this->haversineDistance($shopLat, $shopLon, $userLat, $userLon);
            
            // Nhân với hệ số 1.3 để ước tính đường thực tế
            $distanceKm = $distanceKm * 1.3;
            $distanceKm = round($distanceKm, 1);

            // 3. Xác định mức phí
            global $pdo;
            $shippingMethods = CartModel::getShippingMethods($pdo);
            $rateNoiThanh = !empty($shippingMethods[0]) ? intval($shippingMethods[0]['price']) : 30000;
            $rateNgoaiThanh = !empty($shippingMethods[1]) ? intval($shippingMethods[1]['price']) : 50000;
            $rateNgoaiTinh = !empty($shippingMethods[2]) ? intval($shippingMethods[2]['price']) : 70000;

            $fee = 0;
            $name = '';
            
            if ($distanceKm <= 30) {
                $fee = $rateNoiThanh;
                $name = 'Nội thành';
            } else if ($distanceKm <= 100) {
                $fee = $rateNgoaiThanh;
                $name = 'Ngoại thành';
            } else {
                $fee = $rateNgoaiTinh;
                $name = 'Ngoại tỉnh';
            }

            $this->jsonResponse('success', 'Tính phí thành công', [
                'distance' => $distanceKm,
                'fee' => $fee,
                'name' => $name,
                'user_lat' => $userLat,
                'user_lon' => $userLon
            ]);

        } catch (Exception $e) {
            $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // Bán kính trái đất (km)
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $R * $c;
    }

    public function validateCoupon()
    {
        global $pdo;
        
        $couponCode = $_POST['coupon_code'] ?? '';
        $orderTotal = (float)($_POST['order_total'] ?? 0);
        
        if (empty($couponCode)) {
            $this->jsonResponse('error', 'Mã giảm giá không được để trống.');
            return;
        }
        
        if ($orderTotal <= 0) {
            $this->jsonResponse('error', 'Vui lòng chọn ít nhất một sản phẩm để áp dụng mã giảm giá.');
            return;
        }
        
        // Sử dụng hàm validation từ OrderModel
        include_once __DIR__ . '/../models/OrderModel.php';
        $result = OrderModel::validateAndApplyCoupon($pdo, $couponCode, $orderTotal);
        
        if ($result['valid']) {
            $this->jsonResponse('success', $result['message'], [
                'discount' => $result['discount'],
                'coupon_id' => $result['coupon_id']
            ]);
        } else {
            $this->jsonResponse('error', $result['message']);
        }
    }

    private function jsonResponse($status, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    }
}
