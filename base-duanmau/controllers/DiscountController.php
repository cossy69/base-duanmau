<?php
include_once './config/db_connection.php';
include_once './models/CouponModel.php';
include_once './models/ProductModel.php';
include_once './models/CartModel.php';

class DiscountController
{
    public function discount()
    {
        global $pdo;

        $coupons = CouponModel::getActiveCoupons($pdo);

        $categories = ProductModel::getCategories($pdo);
        $cartItemCount = CartModel::getCartItemCount();
        $userId = $_SESSION['user_id'] ?? 0;

        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/discount.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
