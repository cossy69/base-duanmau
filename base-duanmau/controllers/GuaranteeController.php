<?php

class GuaranteeController
{

    public function guarantee()
    {

        include './views/user/header_link.php';
        include_once './views/user/header.php';

        require_once './views/user/bao_hanh.php';

        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }

    public function return_policy()
    {

        include './views/user/header_link.php';
        include_once './views/user/header.php';

        require_once './views/user/hoan_tien.php';

        include_once './views/user/footter.php';
        include './views/user/footter_link.php';
    }
}
