<?php

class ClauseController
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
