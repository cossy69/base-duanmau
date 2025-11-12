<?php
// Đảm bảo file config được gọi
include_once  './config/db_connection.php';
// Gọi CartController để dùng hàm đếm
include_once __DIR__ . '/CartController.php';

class LoginController
{

    /**
     * Hiển thị trang đăng nhập
     */
    public function login()
    {
        // Lấy số lượng giỏ hàng cho header
        $cartItemCount = CartController::getCartItemCount();

        // Include các file view
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/login.php'; // Đây là file login.php của anh
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * Xử lý logic khi người dùng bấm nút "Đăng Nhập"
     */
    public function handleLogin()
    {
        global $pdo;

        // 1. Kiểm tra xem có phải là POST request không
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // 2. Kiểm tra email và mật khẩu
            if (empty($email) || empty($password)) {
                $this->redirectWithError('index.php?class=login&act=login', 'Vui lòng nhập đầy đủ email và mật khẩu.');
            }

            // 3. Truy vấn CSDL để tìm user
            try {
                $stmt = $pdo->prepare("SELECT user_id, full_name, password_hash, is_admin FROM user WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // 4. Xác thực mật khẩu
                // Dùng password_verify() để so sánh với hash trong CSDL
                if ($user && password_verify($password, $user['password_hash'])) {

                    // 5. Đăng nhập thành công -> Lưu vào Session
                    // (session_start() đã được gọi ở index.php)
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['is_admin'] = $user['is_admin'];

                    // 6. Phân quyền và điều hướng
                    if ($user['is_admin'] == 1) {
                        // Nếu là Admin, chuyển đến trang Admin
                        header('Location: index.php?class=admin&act=admin');
                        exit;
                    } else {
                        // Nếu là User, chuyển về trang chủ
                        header('Location: index.php?class=home&act=home');
                        exit;
                    }
                } else {
                    // 7. Sai email hoặc mật khẩu
                    $this->redirectWithError('index.php?class=login&act=login', 'Email hoặc mật khẩu không chính xác.');
                }
            } catch (PDOException $e) {
                $this->redirectWithError('index.php?class=login&act=login', 'Lỗi CSDL: ' . $e->getMessage());
            }
        } else {
            // Nếu không phải POST, đá về trang login
            header('Location: index.php?class=login&act=login');
            exit;
        }
    }
    /**
     * HÀM MỚI 1: Hiển thị trang đăng ký
     * (Tương ứng với index.php?class=login&act=register)
     */
    public function register()
    {
        // Lấy số lượng giỏ hàng cho header
        $cartItemCount = CartController::getCartItemCount();

        // Include các file view
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/register.php'; // Đây là file register.php của anh
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * HÀM MỚI 2: Xử lý form đăng ký
     * (Tương ứng với index.php?class=login&act=handleRegister)
     */
    public function handleRegister()
    {
        global $pdo;

        // 1. Chỉ chạy khi là POST
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            header('Location: index.php?class=login&act=register');
            exit;
        }

        // 2. Lấy dữ liệu từ form
        $fullName = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // 3. Validate dữ liệu
        if (empty($fullName) || empty($email) || empty($password)) {
            $this->redirectWithStatus('register', 'error', 'Vui lòng điền đầy đủ thông tin.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithStatus('register', 'error', 'Email không hợp lệ.');
        }
        if (strlen($password) < 8) {
            $this->redirectWithStatus('register', 'error', 'Mật khẩu phải có ít nhất 8 ký tự.');
        }
        if ($password !== $confirmPassword) {
            $this->redirectWithStatus('register', 'error', 'Xác nhận mật khẩu không khớp.');
        }

        // 4. Kiểm tra email đã tồn tại chưa
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $this->redirectWithStatus('register', 'error', 'Email này đã được sử dụng.');
            }

            // 5. Mã hóa mật khẩu
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // 6. Thêm user mới vào CSDL
            // (is_admin và is_disabled sẽ tự dùng giá trị DEFAULT 0 trong CSDL)
            $stmt = $pdo->prepare("
                INSERT INTO user (full_name, email, password_hash, phone, is_admin, is_disabled) 
                VALUES (?, ?, ?, NULL, 0, 0)
            ");
            $stmt->execute([$fullName, $email, $passwordHash]);

            // 7. Đăng ký thành công -> Chuyển về trang đăng nhập
            $this->redirectWithStatus('login', 'success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        } catch (PDOException $e) {
            $this->redirectWithStatus('register', 'error', 'Lỗi CSDL: ' . $e->getMessage());
        }
    }

    /**
     * HÀM MỚI 3: Helper để tạo thông báo
     */
    private function redirectWithStatus($page, $type, $message)
    {
        // $page = 'login' hoặc 'register'
        // $type = 'success' hoặc 'error'
        $_SESSION["register_$type"] = $message;

        if ($page == 'login') {
            header('Location: index.php?class=login&act=login');
        } else {
            header('Location: index.php?class=login&act=register');
        }
        exit;
    }
    /**
     * Hàm helper để chuyển hướng kèm thông báo lỗi
     */
    private function redirectWithError($location, $message)
    {
        $_SESSION['login_error'] = $message;
        header("Location: $location");
        exit;
    }
    public function logout()
    {
        // (session_start() đã được gọi ở index.php)
        session_unset();    // Xóa tất cả biến session
        session_destroy();  // Hủy session

        // Chuyển hướng về trang chủ
        header('Location: index.php?class=home&act=home');
        exit;
    }
}
