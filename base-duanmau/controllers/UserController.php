<?php

//Trang chu
class homeController
{
    public function home()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/home.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
class adminController
{
    public function admin()
    {

        require_once './views/user/admin.php';
    }
}
//San pham
class productController
{
    public function product()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/product.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function product_detail()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/product_detail.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//tin tuc
class newsController
{
    public function news()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/news.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function new_detail()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/new_detail.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//gioi thieu
class introduceController
{
    public function introduce()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/introduce.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//lien he
class contactController
{
    public function contact()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/contact.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
//san pham yeu thich
class favouriteController
{
    public function favourite()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/favourite.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
//gio hang
//tai khoan
class accountController
{
    public function account()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/account.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//dang ky + dang nhap
class loginController
{
    public function login()
    {
        include './views/user/header_link.php';
        require_once './views/user/login.php';
        include './views/user/footter_link.php';
    }

    public function register()
    {
        include './views/user/header_link.php';
        require_once './views/user/register.php';
        include './views/user/footter_link.php';
    }
}

//chinh sach bao hanh
class guaranteeController
{
    public function guarantee()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/bao_hanh.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//dieu khoan su dung
class clauseController
{
    public function clause()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/dieu_khoan.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//ban hang va hoan tien
class refundController
{
    public function refund()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/hoan_tien.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//so sanh
class compareController
{
    public function compare()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/compare.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}

//ma giam gia
class discoundController
{
    public function discound()
    {
        include './views/user/header_link.php';
        include_once './views/user/header.php';
        require_once './views/user/discound.php';
        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
