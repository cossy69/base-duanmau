<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$vnp_TmnCode = "4S5A6BNR";
$vnp_HashSecret = "UH7SFZBZJXTPTLJI8AOD5HPNR37LT6XY";
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

// Trả về đúng domain hiện tại (tránh nhảy sang techhubstore.io.vn)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/\\');
$vnp_Returnurl = $scheme . '://' . $host . $basePath . "/index.php?class=order&act=vnpay_return";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
