<?php
// File tạo database mới từ scratch
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔨 Tạo Database Mới</h1>";

// Kết nối MySQL mà không chọn database
$mysqli = new mysqli('localhost', 'root', '');
if ($mysqli->connect_error) {
    die("❌ Lỗi kết nối: " . $mysqli->connect_error);
}

echo "✓ Kết nối MySQL thành công<br><br>";

// Bước 1: Drop database cũ nếu tồn tại
echo "<h3>Bước 1: Xóa database 'pet' cũ (nếu có)</h3>";
if ($mysqli->query("DROP DATABASE IF EXISTS pet")) {
    echo "✓ Database 'pet' cũ đã xóa<br>";
} else {
    echo "⚠️ " . $mysqli->error . "<br>";
}

// Bước 2: Tạo database mới
echo "<h3>Bước 2: Tạo database 'pet' mới</h3>";
if ($mysqli->query("CREATE DATABASE pet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    echo "✓ Database 'pet' đã tạo<br>";
} else {
    die("❌ Lỗi: " . $mysqli->error);
}

// Bước 3: Chọn database
$mysqli->select_db('pet');
echo "✓ Đã chọn database 'pet'<br>";

// Bước 4: Tạo bảng users
echo "<h3>Bước 3: Tạo bảng users</h3>";
$create_users = "
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role ENUM('ADMIN','CUSTOMER') NOT NULL DEFAULT 'CUSTOMER',
  status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if ($mysqli->query($create_users)) {
    echo "✓ Bảng users đã tạo<br>";
} else {
    die("❌ Lỗi: " . $mysqli->error);
}

// Bước 5: Insert sample data
echo "<h3>Bước 4: Thêm sample users</h3>";

// Tạo bcrypt hash cho password
$users_data = [
    ['Admin User', 'admin@petshop.test', '0900000000', 'admin123', 'ADMIN'],
    ['Nguyen Van A', 'a.customer@petshop.test', '0911111111', 'customer123A', 'CUSTOMER'],
    ['Tran Thi B', 'b.customer@petshop.test', '0922222222', 'customer123B', 'CUSTOMER'],
];

foreach ($users_data as $user) {
    $full_name = $user[0];
    $email = $user[1];
    $phone = $user[2];
    $password = $user[3];
    $role = $user[4];
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $mysqli->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, ?, 'ACTIVE')");
    $stmt->bind_param('sssss', $full_name, $email, $phone, $password_hash, $role);
    
    if ($stmt->execute()) {
        echo "✓ Thêm user: $email (Password: $password)<br>";
    } else {
        echo "❌ Lỗi: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Bước 6: Kiểm tra
echo "<h3>Bước 5: Kiểm tra dữ liệu</h3>";
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM users");
$row = $result->fetch_assoc();
echo "✓ Tổng users: " . $row['cnt'] . "<br>";

echo "<h3>Danh sách users:</h3>";
$result = $mysqli->query("SELECT id, full_name, email, role FROM users");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Tên</th><th>Email</th><th>Role</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['full_name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$mysqli->close();

echo "<h3 style='background:lightgreen; padding:10px; border-radius:5px;'>";
echo "✅ <strong>Database đã tạo thành công!</strong><br>";
echo "Hãy test đăng nhập với:<br>";
echo "<code>Email: a.customer@petshop.test | Password: customer123A</code>";
echo "</h3>";

echo "<br><a href='login.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>→ Đi tới trang Login</a>";
?>
