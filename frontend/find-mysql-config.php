<?php
// File test kết nối với nhiều cách khác nhau
echo "<h1>Test kết nối MySQL với các cách khác nhau</h1>";

$tests = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'db' => 'pet', 'desc' => 'Không password'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'db' => 'pet', 'desc' => 'Password: root'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'db' => 'pet', 'desc' => '127.0.0.1 - Không password'],
];

$connected = false;

foreach ($tests as $index => $test) {
    echo "<h3>Test " . ($index + 1) . ": " . $test['desc'] . "</h3>";
    
    try {
        $conn = new mysqli($test['host'], $test['user'], $test['pass'], $test['db']);
        
        if ($conn->connect_error) {
            echo "❌ Lỗi: " . $conn->connect_error . "<br>";
            $conn->close();
        } else {
            echo "✓ Kết nối thành công!<br>";
            echo "Host: " . $test['host'] . "<br>";
            echo "User: " . $test['user'] . "<br>";
            echo "Password: " . ($test['pass'] ? "***" : "(không có)") . "<br>";
            echo "Database: " . $test['db'] . "<br>";
            
            // Kiểm tra user trong database
            $count = $conn->query("SELECT COUNT(*) as cnt FROM users");
            $r = $count->fetch_assoc();
            echo "📊 Số user trong database: " . $r['cnt'] . "<br>";
            
            echo "<div style='background:lightgreen; padding:10px; margin:10px 0; border-radius:5px;'>";
            echo "<strong>✅ Cấu hình hoạt động!</strong><br>";
            echo "Hãy dùng:<br>";
            echo "- Host: <code>" . $test['host'] . "</code><br>";
            echo "- User: <code>" . $test['user'] . "</code><br>";
            echo "- Password: <code>" . ($test['pass'] ? $test['pass'] : "(rỗng)") . "</code><br>";
            echo "- Database: <code>" . $test['db'] . "</code>";
            echo "</div>";
            
            $connected = true;
            $conn->close();
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "<br>";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
}

if (!$connected) {
    echo "<h3 style='color:red;'>⚠️ Không thể kết nối với bất kỳ cấu hình nào!</h3>";
    echo "<p>Vui lòng kiểm tra:</p>";
    echo "<ol>";
    echo "<li>✓ MySQL server có chạy không? (Start từ XAMPP Control Panel)</li>";
    echo "<li>✓ Port 3306 có bị block không?</li>";
    echo "<li>✓ Cấu hình my.ini hoặc my.cnf</li>";
    echo "</ol>";
}
?>

