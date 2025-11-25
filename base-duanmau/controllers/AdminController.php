<?php
include_once './config/db_connection.php';
include_once './models/AdminModel.php';
include_once './models/PostModel.php';
include_once './models/ProductModel.php';

class AdminController
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
            $_SESSION['login_error'] = 'Bạn không có quyền truy cập.';
            header('Location: index.php?class=login&act=login');
            exit;
        }
    }

    public function dashboard()
    {
        global $pdo;

        $stats = AdminModel::getDashboardStats($pdo);
        $monthlySales = AdminModel::getRevenueStats($pdo, 'month');
        $topProducts = AdminModel::getTopProducts($pdo);

        $chartData = [
            'months' => array_keys($monthlySales),
            'sales' => array_values($monthlySales),
            'product_names' => array_column($topProducts, 'name'),
            'product_sales' => array_column($topProducts, 'sold')
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';

        $orders = AdminModel::getOrders($pdo, $status, $page, $limit);
        $totalOrders = AdminModel::countOrders($pdo, $status);
        $totalPages = ceil($totalOrders / $limit);

        require_once './views/admin/dashboard.php';
    }
    public function get_revenue_chart_data()
    {
        global $pdo;
        $timeframe = $_GET['timeframe'] ?? 'month';

        $data = AdminModel::getRevenueStats($pdo, $timeframe);

        echo json_encode([
            'labels' => array_keys($data),
            'values' => array_values($data)
        ]);
        exit;
    }

    public function order_detail()
    {
        global $pdo;
        $orderId = $_GET['id'] ?? 0;

        $order = AdminModel::getOrderWithCoupon($pdo, $orderId);

        $items = AdminModel::getOrderDetails($pdo, $orderId);

        if (!$order) {
            echo "Đơn hàng không tồn tại.";
            return;
        }

        require_once './views/admin/orders/detail.php';
    }

    public function products()
    {
        global $pdo;
        $products = AdminModel::getAllProducts($pdo);
        require_once './views/admin/products/list.php';
    }

    public function delete_product()
    {
        global $pdo;
        $id = $_GET['id'];
        AdminModel::deleteProduct($pdo, $id);
        header('Location: index.php?class=admin&act=products');
    }

    public function orders()
    {
        global $pdo;
        $orders = AdminModel::getOrders($pdo, 'all');
        require_once './views/admin/orders/list.php';
    }


    public function filter_orders()
    {
        global $pdo;
        $status = $_GET['status'] ?? 'all';

        $orders = AdminModel::getOrders($pdo, $status);

        if (empty($orders)) {
            echo '<tr><td colspan="6" class="text-center py-4">Không tìm thấy đơn hàng nào.</td></tr>';
            exit;
        }

        foreach ($orders as $order) {
            $orderId = $order['order_id'];
            $fullName = htmlspecialchars($order['full_name'] ?? 'Khách vãng lai');
            $address = htmlspecialchars(substr($order['shipping_address'], 0, 30)) . '...';
            $total = number_format($order['total_amount']);
            $date = date('d/m H:i', strtotime($order['created_at']));

            // Sửa thành mảng
            $statusMap = [
                'PENDING'   => 'warning text-dark',
                'PREPARING' => 'info text-dark',
                'SHIPPING'  => 'primary',
                'DELIVERED' => 'secondary',
                'COMPLETED' => 'success',
                'CANCELLED' => 'danger'
            ];
            $statusColor = $statusMap[$order['order_status']] ?? 'light text-dark';

            $buttons = "";

            $buttons .= "<button class='btn btn-sm btn-outline-secondary me-1' onclick='viewOrderDetail($orderId)' title='Chi tiết'>Chi tiết</i></button>";

            if ($order['order_status'] == 'PENDING') {
                $buttons .= "
                    <button class='btn btn-sm btn-primary' onclick=\"updateStatus($orderId, 'PREPARING')\" title='Xác nhận đơn'><i class='bx bx-check'></i></button>
                    <button class='btn btn-sm btn-outline-danger' onclick=\"updateStatus($orderId, 'CANCELLED')\" title='Hủy đơn'><i class='bx bx-x'></i></button>
                ";
            } elseif ($order['order_status'] == 'PREPARING') {
                $buttons .= "<button class='btn btn-sm btn-info text-white' onclick=\"updateStatus($orderId, 'SHIPPING')\" title='Giao cho shipper'><i class='bx bxs-truck'></i></button>";
            } elseif ($order['order_status'] == 'SHIPPING') {
                $buttons .= "<button class='btn btn-sm btn-warning' onclick=\"updateStatus($orderId, 'DELIVERED')\" title='Đã đến nơi'><i class='bx bx-package'></i></button>";
            } elseif ($order['order_status'] == 'DELIVERED') {
            }

            echo "
            <tr>
                <td class='fw-bold'>
                <td>
                    <div class='d-flex flex-column'>
                        <span class='fw-bold'>$fullName</span>
                        <small class='text-muted' style='font-size: 12px;'>$address</small>
                    </div>
                </td>
                <td class='text-danger fw-bold'>$total đ</td>
                <td>$date</td>
                <td>
                    <span class='badge rounded-pill bg-$statusColor'>
                        {$order['order_status']}
                    </span>
                </td>
                <td class='text-end'>
                    $buttons
                </td>
            </tr>
            ";
        }

        exit;
    }

    public function update_order_status()
    {
        global $pdo;
        $orderId = $_POST['order_id'];
        $status = $_POST['status'];
        include_once __DIR__ . '/../utils/MailHelper.php';
        AdminModel::updateOrderStatus($pdo, $orderId, $status);
        $mailResult = ['success' => true, 'message' => 'Không cần gửi mail'];

        // --- XỬ LÝ GỬI MAIL TOKEN KHI GIAO HÀNG ---
        if ($status === 'DELIVERED') {
            $order = AdminModel::getOrders($pdo, 'all');
            $currentOrder = null;
            foreach ($order as $o) {
                if ($o['order_id'] == $orderId) {
                    $currentOrder = $o;
                    break;
                }
            }

            if ($currentOrder) {
                // 1. Tạo Token ngẫu nhiên (An toàn hơn md5 tĩnh)
                $loginToken = bin2hex(random_bytes(32));

                // 2. Lưu token vào DB User (Để đối chiếu khi click)
                if (!empty($currentOrder['user_id'])) {
                    $stmt = $pdo->prepare("UPDATE user SET one_time_token = ? WHERE user_id = ?");
                    $stmt->execute([$loginToken, $currentOrder['user_id']]);
                }

                // 3. Chuẩn bị thông tin gửi mail
                $userEmail = $currentOrder['email'] ?? '';
                $userName = $currentOrder['full_name'] ?? 'Khách hàng';

                // (Logic cũ: Tìm email từ address nếu không có trong cột email)
                if (empty($userEmail)) {
                    preg_match('/Email:\s*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $currentOrder['shipping_address'], $matches);
                    if (!empty($matches[1])) {
                        $userEmail = $matches[1];
                    }

                    if ($userName === 'Khách hàng') {
                        preg_match('/Người nhận:\s*([^|]+)/', $currentOrder['shipping_address'], $nameMatches);
                        if (!empty($nameMatches[1])) {
                            $userName = trim($nameMatches[1]);
                        }
                    }
                }

                // 4. Gửi mail kèm Token
                if (!empty($userEmail)) {
                    // Gọi hàm sendConfirmationEmail với tham số mới ($loginToken)
                    $mailResult = MailHelper::sendConfirmationEmail($userEmail, $userName, $orderId, $loginToken);
                } else {
                    $mailResult = ['success' => false, 'message' => 'Không tìm thấy email khách hàng'];
                }
            }
        }
        echo json_encode([
            'status' => 'success',
            'mail_status' => $mailResult['success'],
            'mail_message' => $mailResult['message']
        ]);
    }

    public function users()
    {
        global $pdo;
        $users = AdminModel::getAllUsers($pdo);
        require_once './views/admin/users/list.php';
    }

    public function toggle_user()
    {
        global $pdo;
        $id = $_POST['id'];
        $status = $_POST['status'];
        AdminModel::toggleUserStatus($pdo, $id, $status);
        echo json_encode(['status' => 'success']);
    }


    public function get_order_detail_html()
    {
        global $pdo;
        $orderId = $_GET['order_id'] ?? 0;

        $items = AdminModel::getOrderDetails($pdo, $orderId);

        $order = AdminModel::getOrderWithCoupon($pdo, $orderId);

        if (empty($items)) {
            echo '<tr><td colspan="4" class="text-center">Không có dữ liệu sản phẩm.</td></tr>';
            exit;
        }

        $subtotal = 0;

        foreach ($items as $item) {
            $imgUrl = htmlspecialchars($item['main_image_url']);
            $name = htmlspecialchars($item['name']);
            $price = number_format($item['unit_price']);
            $qty = $item['quantity'];
            $itemTotalVal = $item['unit_price'] * $qty;
            $itemTotal = number_format($itemTotalVal);

            $subtotal += $itemTotalVal;

            echo "
            <tr>
                <td>
                    <div class='d-flex align-items-center'>
                        <img src='$imgUrl' style='width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;'>
                        <span class='text-truncate' style='max-width: 200px;' title='$name'>$name</span>
                    </div>
                </td>
                <td class='text-center'>$qty</td>
                <td class='text-end'>$price ₫</td>
                <td class='text-end fw-bold'>$itemTotal ₫</td>
            </tr>
            ";
        }

        $shippingFee = isset($order['shipping_fee']) ? $order['shipping_fee'] : 0;
        $discount = isset($order['discount_amount']) ? $order['discount_amount'] : 0;
        $couponCode = !empty($order['coupon_code']) ? "({$order['coupon_code']})" : "";

        $finalTotal = isset($order['total_amount']) ? $order['total_amount'] : ($subtotal + $shippingFee - $discount);

        echo "
        <tr class='bg-light'>
            <td colspan='3' class='text-end'>Tạm tính:</td>
            <td class='text-end'>" . number_format($subtotal) . " ₫</td>
        </tr>
        <tr>
            <td colspan='3' class='text-end'>Phí vận chuyển:</td>
            <td class='text-end'>" . number_format($shippingFee) . " ₫</td>
        </tr>";

        if ($discount > 0) {
            echo "
            <tr>
                <td colspan='3' class='text-end text-success'>Giảm giá $couponCode:</td>
                <td class='text-end text-success'>-" . number_format($discount) . " ₫</td>
            </tr>";
        }

        echo "
        <tr class='border-top border-2'>
            <td colspan='3' class='text-end fw-bold fs-5'>Tổng cộng:</td>
            <td class='text-end fw-bold fs-5 text-primary'>" . number_format($finalTotal) . " ₫</td>
        </tr>
        ";

        exit;
    }
    public function update_user_role()
    {
        global $pdo;

        if ($_SESSION['user_id'] != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn không đủ quyền hạn để thực hiện (Chỉ Super Admin).']);
            return;
        }

        $id = $_POST['id'];
        $role = $_POST['role'];

        if ($id == $_SESSION['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'Không thể tự thay đổi quyền của chính mình!']);
            return;
        }

        if ($id == 1) {
            echo json_encode(['status' => 'error', 'message' => 'Không thể thay đổi quyền của Super Admin!']);
            return;
        }

        AdminModel::updateUserRole($pdo, $id, $role);
        echo json_encode(['status' => 'success']);
    }
    public function add_product()
    {
        global $pdo;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imagePath = 'image/default.png';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "image/products/product_thumbnails/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                }
            }

            $data = [
                'name' => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'brand_id' => $_POST['brand_id'],
                'price' => $_POST['price'],
                'short_description' => $_POST['short_description'],
                'detail_description' => $_POST['detail_description'],
                'image' => $imagePath
            ];

            AdminModel::addProduct($pdo, $data);
            header('Location: index.php?class=admin&act=products');
        } else {
            $categories = ProductModel::getCategories($pdo);
            $brands = ProductModel::getBrands($pdo);
            require_once './views/admin/products/add.php';
        }
    }

    public function attributes()
    {
        global $pdo;

        if (isset($_POST['add_attribute'])) {
            AdminModel::addAttribute($pdo, $_POST['name']);
            header('Location: index.php?class=admin&act=attributes');
        }

        if (isset($_POST['add_value'])) {
            AdminModel::addAttributeValue($pdo, $_POST['attribute_id'], $_POST['value']);
            header('Location: index.php?class=admin&act=attributes');
        }

        $attributes = AdminModel::getAllAttributes($pdo);
        foreach ($attributes as &$attr) {
            $attr['values'] = AdminModel::getAttributeValues($pdo, $attr['attribute_id']);
        }
        unset($attr);
        require_once './views/admin/attributes/list.php';
    }

    public function edit_product()
    {
        global $pdo;
        $id = $_GET['id'] ?? 0;

        if (isset($_POST['add_variant'])) {
            $varImg = 'image/default.png';
            if (!empty($_FILES['variant_image']['name'])) {
                $targetDir = "image/products/variant_thumbnails/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
                $fileName = time() . "_" . basename($_FILES['variant_image']['name']);
                if (move_uploaded_file($_FILES['variant_image']['tmp_name'], $targetDir . $fileName)) {
                    $varImg = $targetDir . $fileName;
                }
            }

            $variantId = AdminModel::addProductVariant(
                $pdo,
                $id,
                $_POST['var_price'],
                $_POST['var_original_price'],
                $_POST['var_qty'],
                $varImg
            );

            if (!empty($_POST['attribute_values'])) {
                foreach ($_POST['attribute_values'] as $valId) {
                    if ($valId > 0) AdminModel::mapVariantAttribute($pdo, $variantId, $valId);
                }
            }

            header("Location: index.php?class=admin&act=edit_product&id=$id");
            exit;
        }

        if (isset($_POST['update_product'])) {
            $imagePath = $_POST['current_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "image/products/product_thumbnails/";
                $fileName = time() . '_' . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $fileName)) {
                    $imagePath = $targetDir . $fileName;
                }
            }
            $data = [
                'category_id' => $_POST['category_id'],
                'brand_id' => $_POST['brand_id'],
                'name' => $_POST['name'],
                'price' => $_POST['price'],
                'short_description' => $_POST['short_description'],
                'detail_description' => $_POST['detail_description'],
                'image' => $imagePath,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            AdminModel::updateProduct($pdo, $id, $data);
            header("Location: index.php?class=admin&act=edit_product&id=$id");
            exit;
        }

        if (isset($_GET['delete_variant'])) {
            AdminModel::deleteVariant($pdo, $_GET['delete_variant']);
            header("Location: index.php?class=admin&act=edit_product&id=$id");
            exit;
        }

        $product = AdminModel::getProductById($pdo, $id);
        if (!$product) {
            echo "Sản phẩm không tồn tại.";
            return;
        }

        $categories = ProductModel::getCategories($pdo);
        $brands = ProductModel::getBrands($pdo);

        $variants = AdminModel::getProductVariants($pdo, $id);

        $allAttributes = AdminModel::getAllAttributes($pdo);
        foreach ($allAttributes as &$attr) {
            $attr['values'] = AdminModel::getAttributeValues($pdo, $attr['attribute_id']);
        }

        require_once './views/admin/products/edit.php';
    }

    public function posts()
    {
        global $pdo;
        $posts = PostModel::getAllPosts($pdo);
        require_once './views/admin/posts/list.php';
    }

    public function add_post()
    {
        global $pdo;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imagePath = 'image/new.webp';
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
                $targetDir = "image/posts/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES["thumbnail"]["name"]);
                if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetDir . $fileName)) {
                    $imagePath = $targetDir . $fileName;
                }
            }

            $data = [
                'user_id' => $_SESSION['user_id'],
                'category_id' => $_POST['category_id'] ?? 4,
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'thumbnail_url' => $imagePath,
                'is_published' => isset($_POST['is_published']) ? 1 : 0
            ];

            PostModel::addPost($pdo, $data);
            header('Location: index.php?class=admin&act=posts');
        } else {
            $categories = ProductModel::getCategories($pdo);
            require_once './views/admin/posts/add.php';
        }
    }

    public function edit_post()
    {
        global $pdo;
        $id = $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imagePath = $_POST['current_thumbnail'];
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
                $targetDir = "image/posts/";
                $fileName = time() . '_' . basename($_FILES["thumbnail"]["name"]);
                if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetDir . $fileName)) {
                    $imagePath = $targetDir . $fileName;
                }
            }

            $data = [
                'category_id' => $_POST['category_id'],
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'thumbnail_url' => $imagePath,
                'is_published' => isset($_POST['is_published']) ? 1 : 0
            ];

            PostModel::updatePost($pdo, $id, $data);
            header('Location: index.php?class=admin&act=posts');
        } else {
            $post = PostModel::getPostById($pdo, $id);
            $categories = ProductModel::getCategories($pdo);
            require_once './views/admin/posts/edit.php';
        }
    }

    public function delete_post()
    {
        global $pdo;
        $id = $_GET['id'];
        PostModel::deletePost($pdo, $id);
        header('Location: index.php?class=admin&act=posts');
    }

    public function feedbacks()
    {
        global $pdo;
        $feedbacks = AdminModel::getFeedbacks($pdo, 'all');
        require_once './views/admin/feedbacks/list.php';
    }

    public function filter_feedbacks()
    {
        global $pdo;
        $status = $_GET['status'] ?? 'all';

        $feedbacks = AdminModel::getFeedbacks($pdo, $status);

        if (empty($feedbacks)) {
            echo '<tr><td colspan="7" class="text-center py-4">Không tìm thấy phản hồi nào.</td></tr>';
            exit;
        }

        foreach ($feedbacks as $fb) {
            $id = $fb['feedback_id'];
            $name = htmlspecialchars($fb['full_name']);
            $email = htmlspecialchars($fb['email']);
            $title = htmlspecialchars($fb['title']);
            $contentRaw = htmlspecialchars($fb['content']);
            $contentShort = (strlen($contentRaw) > 50) ? substr($contentRaw, 0, 50) . '...' : $contentRaw;

            $date = date('d/m H:i', strtotime($fb['created_at']));

            // 1. Map màu sắc
            $colorMap = [
                'NEW'         => 'danger',
                'IN_PROGRESS' => 'warning text-dark',
                'RESOLVED'    => 'success'
            ];
            $badgeColor = $colorMap[$fb['status']] ?? 'secondary';

            // 2. Map nội dung chữ
            $textMap = [
                'NEW'         => 'Mới',
                'IN_PROGRESS' => 'Đang xử lý',
                'RESOLVED'    => 'Đã giải quyết'
            ];
            $statusText = $textMap[$fb['status']] ?? $fb['status'];

            $buttons = "";
            if ($fb['status'] == 'NEW') {
                $buttons .= "<button class='btn btn-sm btn-warning me-1' onclick=\"updateFeedback($id, 'IN_PROGRESS')\" title='Đánh dấu đang xử lý'><i class='bx bx-loader-circle'></i></button>";
            }
            if ($fb['status'] != 'RESOLVED') {
                $buttons .= "<button class='btn btn-sm btn-success me-1' onclick=\"updateFeedback($id, 'RESOLVED')\" title='Đánh dấu đã xong'><i class='bx bx-check'></i></button>";
            }
            $buttons .= "<button class='btn btn-sm btn-outline-danger' onclick=\"deleteFeedback($id)\" title='Xóa'><i class='bx bx-trash'></i></button>";

            echo "
            <tr>
                <td>
                <td>
                    <div class='fw-bold'>$name</div>
                    <div class='small text-muted'>$email</div>
                </td>
                <td>$title</td>
                <td title='$contentRaw'>$contentShort</td>
                <td><span class='badge bg-$badgeColor'>$statusText</span></td>
                <td>$date</td>
                <td class='text-end'>$buttons</td>
            </tr>
            ";
        }
        exit;
    }

    public function update_feedback_status()
    {
        global $pdo;
        $id = $_POST['id'];
        $status = $_POST['status'];

        if (AdminModel::updateFeedbackStatus($pdo, $id, $status)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function delete_feedback()
    {
        global $pdo;
        $id = $_GET['id'];
        AdminModel::deleteFeedback($pdo, $id);
        header('Location: index.php?class=admin&act=feedbacks');
    }
    // --- CONTROLLER ĐÁNH GIÁ ---

    public function reviews()
    {
        global $pdo;
        $reviews = AdminModel::getAllReviews($pdo);
        require_once './views/admin/reviews/list.php';
    }

    public function delete_review()
    {
        global $pdo;
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            AdminModel::deleteReview($pdo, $id);
        }
        header('Location: index.php?class=admin&act=reviews');
        exit;
    }

    public function toggle_review()
    {
        global $pdo;
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? 0;

        AdminModel::toggleReviewStatus($pdo, $id, $status);
        header('Location: index.php?class=admin&act=reviews');
        exit;
    }
}
