<?php
echo "Đang kiểm tra kết nối MySQL...<br><br>";

// Test 1: Kết nối tới server MySQL
echo "<strong>Test 1: Kết nối MySQL Server</strong><br>";
$test1 = @mysqli_connect('localhost', 'root', '');
if ($test1) {
    echo "✓ Kết nối MySQL thành công<br>";
    mysqli_close($test1);
} else {
    echo "✗ Lỗi kết nối: " . mysqli_connect_error() . "<br>";
    echo "Giải pháp: Vui lòng start MySQL từ XAMPP Control Panel<br><br>";
    exit;
}

// Test 2: Kiểm tra database pet
echo "<br><strong>Test 2: Kiểm tra database</strong><br>";
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die("✗ Lỗi: " . $conn->connect_error);
}

// Lấy danh sách database
$result = $conn->query("SHOW DATABASES");
$databases = [];
while ($row = $result->fetch_row()) {
    $databases[] = $row[0];
}

if (in_array('pet', $databases)) {
    echo "✓ Database <strong>pet</strong> tồn tại<br>";
} else {
    echo "✗ Database <strong>pet</strong> không tồn tại<br>";
    echo "Các database có sẵn: " . implode(", ", $databases) . "<br>";
    echo "Cần tạo database mới...<br>";
}

// Test 3: Kết nối tới database
echo "<br><strong>Test 3: Kết nối tới pet</strong><br>";
$conn2 = new mysqli('localhost', 'root', '', 'pet');
if ($conn2->connect_error) {
    echo "✗ Lỗi: " . $conn2->connect_error . "<br>";
    echo "Giải pháp: Hãy import file database/dbwebthucung.sql<br>";
} else {
    echo "✓ Kết nối database thành công<br>";
    
    // Kiểm tra bảng users
    $tables = $conn2->query("SHOW TABLES");
    $table_list = [];
    while ($row = $tables->fetch_row()) {
        $table_list[] = $row[0];
    }
    
    if (in_array('users', $table_list)) {
        echo "✓ Bảng <strong>users</strong> tồn tại<br>";
        
        // Đếm user
        $count = $conn2->query("SELECT COUNT(*) as cnt FROM users");
        $r = $count->fetch_assoc();
        echo "✓ Có " . $r['cnt'] . " user trong database<br>";
    } else {
        echo "✗ Bảng <strong>users</strong> không tồn tại<br>";
        echo "Các bảng có sẵn: " . implode(", ", $table_list) . "<br>";
    }
    
    $conn2->close();
}

$conn->close();

echo "<br><br><strong>Kết luận:</strong><br>";
echo "Nếu tất cả test đều ✓, bạn có thể đăng nhập bình thường.<br>";
echo "Nếu có ✗, hãy làm theo hướng dẫn ở trên.";
?>
