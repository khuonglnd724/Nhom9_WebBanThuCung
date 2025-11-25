<?php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Chuyển hướng về trang chủ với script xóa giỏ hàng
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đăng xuất</title>
</head>
<body>
    <script>
        // Xóa tất cả giỏ hàng trong localStorage
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith('cart_user_')) {
                localStorage.removeItem(key);
            }
        });
        // Chuyển hướng về trang chủ
        window.location.href = 'index.php';
    </script>
</body>
</html>
