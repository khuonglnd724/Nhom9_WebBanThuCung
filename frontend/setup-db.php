<?php
// File setup database - tạo database và tables tự động
$mysqli = new mysqli('localhost', 'root', '');
if ($mysqli->connect_error) {
    die("Lỗi: Không thể kết nối MySQL. " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

// Tạo database nếu chưa tồn tại
$create_db = "CREATE DATABASE IF NOT EXISTS dbwebthucung";
if (!$mysqli->query($create_db)) {
    die("Lỗi tạo database: " . $mysqli->error);
}

// Chọn database
$mysqli->select_db("dbwebthucung");

// Tạo bảng users
$create_users = "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(120),
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (!$mysqli->query($create_users)) {
    die("Lỗi tạo bảng users: " . $mysqli->error);
}

echo "✓ Database setup thành công!<br>";
echo "✓ Bảng users đã được tạo<br>";
echo "<br><a href='login.php'>Quay lại Đăng nhập</a>";

$mysqli->close();
?>
