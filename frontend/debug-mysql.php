<?php
// Test kết nối với error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📊 Debug MySQL Connection</h1>";

// Kiểm tra mysqli extension
echo "<h3>1. Kiểm tra mysqli extension</h3>";
if (extension_loaded('mysqli')) {
    echo "✓ mysqli extension đã load<br>";
} else {
    echo "✗ mysqli extension chưa load<br>";
    die();
}

// Kiểm tra php.ini settings
echo "<h3>2. Kiểm tra PHP configuration</h3>";
echo "mysqli.default_host: " . (ini_get('mysqli.default_host') ?: "(không set)") . "<br>";
echo "mysqli.default_user: " . (ini_get('mysqli.default_user') ?: "(không set)") . "<br>";
echo "mysqli.default_socket: " . (ini_get('mysqli.default_socket') ?: "(không set)") . "<br>";
echo "mysqli.default_port: " . (ini_get('mysqli.default_port') ?: "(không set)") . "<br>";

// Test kết nối từng bước
echo "<h3>3. Test kết nối MySQL</h3>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'pet';

echo "Cấu hình:<br>";
echo "- Host: $host<br>";
echo "- User: $user<br>";
echo "- Pass: (rỗng)<br>";
echo "- DB: $db<br><br>";

echo "Đang kết nối...<br>";

try {
    // Set error mode
    mysqli_report(MYSQLI_REPORT_STRICT);
    
    $mysqli = new mysqli($host, $user, $pass, $db);
    
    echo "✓ Kết nối thành công!<br>";
    echo "Server info: " . $mysqli->server_info . "<br>";
    echo "Protocol: " . $mysqli->protocol_version . "<br>";
    
    // Test query
    $result = $mysqli->query("SELECT COUNT(*) as cnt FROM users");
    $row = $result->fetch_assoc();
    echo "Users count: " . $row['cnt'] . "<br>";
    
    echo "<div style='background:lightgreen; padding:10px; margin:10px 0;'>";
    echo "✅ <strong>MySQL hoạt động bình thường!</strong><br>";
    echo "Hãy reload trang login.php";
    echo "</div>";
    
    $mysqli->close();
    
} catch (mysqli_sql_exception $e) {
    echo "✗ Lỗi: " . $e->getMessage() . "<br>";
    echo "Error code: " . $e->getCode() . "<br>";
    
    // Gợi ý
    echo "<div style='background:#ffe6e6; padding:10px; margin:10px 0;'>";
    if (strpos($e->getMessage(), 'using password: NO') !== false) {
        echo "<strong>⚠️ Gợi ý:</strong><br>";
        echo "Có thể MySQL không chạy hoặc socket bị lỗi.<br>";
        echo "Hãy:<br>";
        echo "1. Restart MySQL từ XAMPP Control Panel<br>";
        echo "2. Hoặc thử dùng '127.0.0.1' thay vì 'localhost'";
    }
    echo "</div>";
}

// Kiểm tra socket file
echo "<h3>4. Kiểm tra MySQL Socket</h3>";
$socket_paths = [
    '/tmp/mysql.sock',
    '/var/run/mysqld/mysqld.sock',
    'C:\\xampp\\mysql\\mysql.sock',
];

foreach ($socket_paths as $socket) {
    if (file_exists($socket)) {
        echo "✓ Found: $socket<br>";
    }
}
?>
