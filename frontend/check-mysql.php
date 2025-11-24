<?php
// File đơn giản để kiểm tra MySQL
echo "<h1>Kiểm tra MySQL Connection</h1>";

// Test kết nối cơ bản
echo "<h3>Test 1: Kết nối tới localhost:3306</h3>";
$link = @fsockopen('localhost', 3306, $errno, $errstr, 5);
if ($link) {
    echo "✓ MySQL server đang chạy trên port 3306<br>";
    fclose($link);
} else {
    echo "✗ MySQL server không chạy hoặc không lắng nghe port 3306<br>";
    echo "Giải pháp: Start MySQL từ XAMPP Control Panel<br>";
    die();
}

// Test mysqli kết nối
echo "<h3>Test 2: Kết nối mysqli</h3>";
try {
    $conn = @new mysqli('localhost', 'root', '');
    if ($conn->connect_error) {
        echo "✗ Lỗi: " . $conn->connect_error . "<br>";
    } else {
        echo "✓ Kết nối thành công<br>";
        
        // Liệt kê database
        $result = $conn->query("SHOW DATABASES");
        echo "<h3>Các database có sẵn:</h3>";
        echo "<ul>";
        while ($row = $result->fetch_row()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
        
        // Kiểm tra database 'pet'
        echo "<h3>Test 3: Kiểm tra database 'pet'</h3>";
        if ($conn->select_db('pet')) {
            echo "✓ Database 'pet' tồn tại<br>";
            
            // Liệt kê bảng
            $tables = $conn->query("SHOW TABLES");
            echo "<h3>Các bảng trong 'pet':</h3>";
            echo "<ul>";
            while ($row = $tables->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
            
            // Kiểm tra bảng users
            if ($conn->query("SELECT 1 FROM users LIMIT 1")) {
                echo "✓ Bảng 'users' tồn tại<br>";
                
                $count = $conn->query("SELECT COUNT(*) as cnt FROM users");
                $r = $count->fetch_assoc();
                echo "✓ Bảng users có " . $r['cnt'] . " user<br>";
                
                // Liệt kê user
                echo "<h3>Users trong database:</h3>";
                $users = $conn->query("SELECT id, email, role FROM users");
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Email</th><th>Role</th></tr>";
                while ($user = $users->fetch_assoc()) {
                    echo "<tr><td>" . $user['id'] . "</td><td>" . $user['email'] . "</td><td>" . $user['role'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "✗ Bảng 'users' không tồn tại<br>";
            }
        } else {
            echo "✗ Database 'pet' không tồn tại<br>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage();
}
?>
