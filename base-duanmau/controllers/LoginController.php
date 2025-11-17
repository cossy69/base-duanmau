<?php
include_once './config/db_connection.php';

// SỬA: Gọi các Controller và Model cần thiết
include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/FavoriteController.php';
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/UserModel.php'; // (File Model mới)

class LoginController
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function login()
    {
        global $pdo;

        // SỬA: Lấy dữ liệu cho header
        $cartItemCount = CartModel::getCartItemCount();
        $categories = ProductModel::getCategories($pdo);
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        // Include các file view
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/login.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * Xử lý logic khi người dùng bấm nút "Đăng Nhập"
     */
    public function handleLogin()
    {
        global $pdo;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->redirectWithError('index.php?class=login&act=login', 'Vui lòng nhập đầy đủ email và mật khẩu.');
            }

            // SỬA: Gọi từ Model
            $user = UserModel::findUserByEmail($pdo, $email);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Đăng nhập thành công -> Lưu vào Session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                // (Thêm: Gộp giỏ hàng session vào CSDL - logic này có thể làm sau)

                if ($user['is_admin'] == 1) {
                    header('Location: index.php?class=admin&act=admin');
                } else {
                    header('Location: index.php?class=home&act=home');
                }
                exit;
            } else {
                $this->redirectWithError('index.php?class=login&act=login', 'Email hoặc mật khẩu không chính xác.');
            }
        } else {
            header('Location: index.php?class=login&act=login');
            exit;
        }
    }

    /**
     * Hiển thị trang đăng ký
     */
    public function register()
    {
        global $pdo;

        // SỬA: Lấy dữ liệu cho header
        $cartItemCount = CartModel::getCartItemCount();
        $categories = ProductModel::getCategories($pdo);
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        // Include các file view
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/register.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    /**
     * Xử lý form đăng ký
     */
    public function handleRegister()
    {
        global $pdo;

        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            header('Location: index.php?class=login&act=register');
            exit;
        }

        $fullName = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate
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

        // SỬA: Gọi từ Model
        if (UserModel::checkIfEmailExists($pdo, $email)) {
            $this->redirectWithStatus('register', 'error', 'Email này đã được sử dụng.');
        }

        // Mã hóa mật khẩu
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // SỬA: Gọi từ Model
        if (UserModel::createUser($pdo, $fullName, $email, $passwordHash)) {
            // Thành công
            $this->redirectWithStatus('login', 'success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        } else {
            // Lỗi
            $this->redirectWithStatus('register', 'error', 'Lỗi CSDL: Không thể tạo tài khoản.');
        }
    }

    /**
     * HÀM MỚI 3: Helper để tạo thông báo
     */
    private function redirectWithStatus($page, $type, $message)
    {
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

    /**
     * Đăng xuất
     */
    public function logout()
    {
        session_unset();
        session_destroy();

        // Bắt đầu lại session để lưu giỏ hàng (nếu có)
        session_start();

        header('Location: index.php?class=home&act=home');
        exit;
    }
}
