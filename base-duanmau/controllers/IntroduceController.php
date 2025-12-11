<?php
include_once './config/db_connection.php';
include_once './models/ProductModel.php';
include_once './models/CartModel.php';

class IntroduceController
{
    public function introduce()
    {
        global $pdo;

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;

        include './views/user/header_link.php';
        include_once './views/user/header.php';

        require_once './views/user/introduce.php';

        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
