<?php
// Thông tin kết nối CSDL
$host = '127.0.0.1';     // Hoặc 'localhost'
$dbName = 'project';     // Tên database anh đã cung cấp
$username = 'root';      // Username của MySQL/MariaDB (thường là 'root')
$password = '';          // Mật khẩu của MySQL/MariaDB (để trống nếu không có)
$charset = 'utf8mb4';    // Bảng mã utf8mb4 để hỗ trợ emoji

// Chuỗi DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

// Cấu hình các tùy chọn cho PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi (ném ra Exception)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Đặt chế độ fetch mặc định là mảng kết hợp (tên cột)
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Tắt chế độ mô phỏng prepared statements (dùng native)
];

// Khởi tạo kết nối PDO
try {
    $pdo = new PDO($dsn, $username, $password, $options);
    // echo "Kết nối CSDL thành công!"; // Anh có thể bỏ dòng này đi khi đã chạy ổn định
} catch (\PDOException $e) {
    // Nếu kết nối thất bại, hiển thị thông báo lỗi chi tiết
    // Trong môi trường production, anh không nên hiển thị chi tiết lỗi này cho người dùng
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
