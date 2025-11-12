<?php
class AdminController
{

    public function __construct()
    {
        // (session_start() đã được gọi ở index.php)

        // KIỂM TRA QUYỀN ADMIN
        // Nếu session không có user_id HOẶC is_admin không phải là 1
        if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
            // "Đá" về trang đăng nhập
            $_SESSION['login_error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: index.php?class=login&act=login');
            exit;
        }
    }

    /**
     * Hiển thị trang admin dashboard
     */
    public function admin()
    {
        // Nếu đã vượt qua hàm __construct() ở trên,
        // nghĩa là user hợp lệ, ta hiển thị trang admin.

        // Giả sử file admin.php của anh nằm ở 'views/admin/admin.php'
        require_once './views/admin/admin.php';
    }
}
