<?php
session_start();

// Mặc định là trang chủ
$controllerClassName = $_GET['class'] ?? 'home';
$action = $_GET['act'] ?? 'home';

// Tên class đầy đủ, ví dụ: "CartController"
$controllerClass = ucfirst($controllerClassName) . "Controller";

// Tên file controller, ví dụ: ".../controllers/CartController.php"
$controllerFile = __DIR__ . "/controllers/" . $controllerClass . ".php";


if (file_exists($controllerFile)) {
    // Luôn dùng require_once để tránh lỗi "Cannot declare class"
    require_once $controllerFile;

    if (class_exists($controllerClass)) {
        $controller = new $controllerClass;

        // Kiểm tra phương thức trên đối tượng, không phải chuỗi tên class
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo "Khong tim thay action $action";
        }
    } else {
        // Lỗi này không nên xảy ra nếu tên file và tên class khớp nhau
        echo "Khong tim thay class $controllerClass trong file $controllerFile";
    }
} else {
    echo "Khong tim thay file $controllerFile";
}
