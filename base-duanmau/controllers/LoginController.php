<?php
include_once './config/db_connection.php';

include_once __DIR__ . '/CartController.php';
include_once __DIR__ . '/FavoriteController.php';
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/UserModel.php';
include_once __DIR__ . '/../utils/MailHelper.php';

class LoginController
{
    public function login()
    {
        global $pdo;

        $cartItemCount = CartModel::getCartItemCount();
        $categories = ProductModel::getCategories($pdo);
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/login.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function handleLogin()
    {
        global $pdo;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember-me']);

            $user = UserModel::findUserByEmail($pdo, $email);

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['is_verified'] == 0) {
                    $this->redirectWithError('index.php?class=login&act=login', 'Tài khoản chưa kích hoạt.');
                }

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                
                // Cập nhật is_admin dựa trên role
                $isAdmin = ($user['role'] === 'admin' || $user['role'] === 'super_admin') ? 1 : 0;
                $_SESSION['is_admin'] = $isAdmin;

                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    // Cần include CartModel nếu chưa có (dù function login đã gọi nhưng handleLogin thì chưa chắc)
                    if (!class_exists('CartModel')) {
                        include_once __DIR__ . '/../models/CartModel.php';
                    }

                    foreach ($_SESSION['cart'] as $item) {
                        $p_id = (int)($item['product_id'] ?? 0); // Ép kiểu int
                        $qty  = (int)($item['quantity'] ?? 0);   // Ép kiểu int

                        // Kiểm tra kỹ variant_id
                        $raw_vid = $item['variant_id'] ?? 0;
                        $v_id = ($raw_vid && (int)$raw_vid > 0) ? (int)$raw_vid : null;

                        if ($p_id > 0 && $qty > 0) {
                            CartModel::addToCart($p_id, $v_id, $qty, $_SESSION['user_id']);
                        }
                    }
                    unset($_SESSION['cart']);
                }
                if ($remember) {
                    $token = bin2hex(random_bytes(32));

                    UserModel::updateRememberToken($pdo, $user['user_id'], $token);

                    setcookie('remember_user', $token, time() + (86400 * 30), "/", "", false, true);
                }
                session_write_close();
                if ($user['is_admin'] == 1) header('Location: index.php?class=admin&act=dashboard');
                else header('Location: index.php?class=home&act=home');
                exit;
            } else {
                $this->redirectWithError('index.php?class=login&act=login', 'Email hoặc mật khẩu không chính xác.');
            }
        }
    }

    public function handleRegister()
    {
        global $pdo;

        $fullName = $_POST['full_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($password !== $confirmPassword) {
            $this->redirectWithStatus('register', 'error', 'Mật khẩu xác nhận không khớp.');
        }

        if (UserModel::checkIfEmailExists($pdo, $email)) {
            $this->redirectWithStatus('register', 'error', 'Email này đã được sử dụng.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        if (UserModel::createUser($pdo, $fullName, $email, $passwordHash, $token)) {
            MailHelper::sendVerificationEmail($email, $fullName, $token);
            $this->redirectWithStatus('login', 'success', 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.');
        } else {
            $this->redirectWithStatus('register', 'error', 'Lỗi hệ thống.');
        }
    }

    public function verify_account()
    {
        global $pdo;
        $token = $_GET['token'] ?? '';
        if (UserModel::verifyUser($pdo, $token)) {
            $this->redirectWithStatus('login', 'success', 'Kích hoạt thành công! Bạn có thể đăng nhập ngay.');
        } else {
            $this->redirectWithError('index.php?class=login&act=login', 'Link kích hoạt không hợp lệ hoặc đã hết hạn.');
        }
    }

    public function forgot_password()
    {
        global $pdo;
        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/forgot_password.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function send_reset_link()
    {
        global $pdo;
        $email = $_POST['email'] ?? '';

        $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            UserModel::createPasswordReset($pdo, $user['user_id'], $token);
            MailHelper::sendResetPasswordEmail($email, $token);
        }

        $_SESSION['login_error'] = "Nếu email tồn tại, link đổi mật khẩu đã được gửi.";
        header('Location: index.php?class=login&act=forgot_password');
    }

    public function reset_password()
    {
        global $pdo;
        $token = $_GET['token'] ?? '';
        $resetData = UserModel::verifyResetToken($pdo, $token);

        if (!$resetData) {
            $this->redirectWithError('index.php?class=login&act=login', 'Link đổi mật khẩu không hợp lệ hoặc đã hết hạn.');
        }

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/reset_password.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function handle_reset_password()
    {
        global $pdo;
        $token = $_POST['token'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if ($password !== $confirm) {
            echo "Mật khẩu không khớp";
            exit;
        }

        $resetData = UserModel::verifyResetToken($pdo, $token);
        if ($resetData) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            UserModel::updatePassword($pdo, $resetData['user_id'], $hash);
            $this->redirectWithStatus('login', 'success', 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.');
        } else {
            $this->redirectWithError('index.php?class=login&act=login', 'Lỗi token.');
        }
    }

    public function register()
    {
        global $pdo;

        $cartItemCount = CartModel::getCartItemCount();
        $categories = ProductModel::getCategories($pdo);
        $userId = $_SESSION['user_id'] ?? 0;
        $favoriteCount = FavoriteModel::getFavoriteCount($pdo, $userId);

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/register.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

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

    private function redirectWithError($location, $message)
    {
        $_SESSION['login_error'] = $message;
        header("Location: $location");
        exit;
    }

    public function logout()
    {
        global $pdo;

        if (isset($_SESSION['user_id'])) {
            UserModel::updateRememberToken($pdo, $_SESSION['user_id'], null);
        }

        session_unset();
        session_destroy();

        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, '/');
        }

        session_start();

        header('Location: index.php?class=home&act=home');
        exit;
    }
    public function checkAutoLogin()
    {
        global $pdo;
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
            $token = $_COOKIE['remember_user'];

            $user = UserModel::findUserByRememberToken($pdo, $token);

            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                
                // Cập nhật is_admin dựa trên role
                $isAdmin = ($user['role'] === 'admin' || $user['role'] === 'super_admin') ? 1 : 0;
                $_SESSION['is_admin'] = $isAdmin;

                setcookie('remember_user', $token, time() + (86400 * 30), "/", "", false, true);
            }
        }
    }
}
