<?php
session_start();

require_once __DIR__ . '/config/db_connection.php';
require_once __DIR__ . '/controllers/LoginController.php';

$autoLogin = new LoginController();
$autoLogin->checkAutoLogin();


$controllerClassName = $_GET['class'] ?? 'home';
$action = $_GET['act'] ?? 'home';

$controllerClass = ucfirst($controllerClassName) . "Controller";

$controllerFile = __DIR__ . "/controllers/" . $controllerClass . ".php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerClass)) {
        $controller = new $controllerClass;

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo "Khong tim thay action $action";
        }
    } else {
        echo "Khong tim thay class $controllerClass trong file $controllerFile";
    }
} else {
    echo "Khong tim thay file $controllerFile";
}
