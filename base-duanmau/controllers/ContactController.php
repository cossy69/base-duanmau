<?php
include_once './config/db_connection.php';
include_once './models/ContactModel.php';
include_once './models/ProductModel.php';
include_once './models/CartModel.php';

class ContactController
{
    public function contact()
    {
        global $pdo;

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/contact.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function submit()
    {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (empty($fullName) || empty($email) || empty($message)) {
                $_SESSION['contact_error'] = "Vui lòng điền đầy đủ các trường bắt buộc (*)";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['contact_error'] = "Email không hợp lệ.";
            } else {
                $result = ContactModel::addFeedback($pdo, $fullName, $email, $subject, $message);

                if ($result) {
                    $_SESSION['contact_success'] = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";
                } else {
                    $_SESSION['contact_error'] = "Có lỗi xảy ra. Vui lòng thử lại sau.";
                }
            }
        }

        header('Location: index.php?class=contact&act=contact');
        exit;
    }
}
