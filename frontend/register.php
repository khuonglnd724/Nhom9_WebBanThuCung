<?php
include '../connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Theo yêu cầu: không kiểm tra phía server, chỉ ghi dữ liệu.
    try {
        if (!isset($conn)) {
            throw new Exception('Không kết nối được CSDL.');
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, 'CUSTOMER', 'ACTIVE')");
        if (!$stmt) { throw new Exception('Lỗi prepare: ' . $conn->error); }
        $stmt->bind_param('sss', $full_name, $email, $password_hash);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success = 'Đăng ký thành công! Bạn có thể đăng nhập.';
        } else {
            $error = 'Không thể tạo tài khoản. Vui lòng thử lại.';
        }
        $stmt->close();
    } catch (Exception $e) {
        $error = 'Có lỗi xảy ra: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; }
        .form-box {
            width: 360px; margin: 80px auto; padding: 20px;
            background: #fff; border-radius: 6px; box-shadow: 0 0 10px #ccc;
        }
        h2 { text-align:center; margin-bottom:20px; }
        input {
            width:100%; padding:10px; margin:8px 0;
            border:1px solid #ccc; border-radius:4px;
        }
        button {
            width:100%; padding:10px; background:#28a745;
            color:#fff; border:none; border-radius:4px;
            cursor:pointer; font-size:16px;
        }
        button:hover { background:#1e7e33; }
        .text-center { text-align:center; margin-top:10px; }
        a { color:#007bff; text-decoration:none; }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Đăng ký</h2>

    <?php if ($error !== ''): ?>
        <div style="color:#b30000; background:#fdecea; padding:8px 10px; border-radius:4px; margin-bottom:10px;">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div style="color:#155724; background:#d4edda; padding:8px 10px; border-radius:4px; margin-bottom:10px;">
            <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <script>
            setTimeout(function(){ window.location.href = 'login.php'; }, 1200);
        </script>
    <?php endif; ?>

    <form method="post" action="" id="registerForm">
        <input type="text" name="full_name" id="full_name" placeholder="Họ và tên" maxlength="100" required value="<?php echo isset($full_name) ? htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') : '';?>">
        <input type="email" name="email" id="email" placeholder="Email" maxlength="120" required value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : '';?>">
        <input type="password" name="password" id="password" placeholder="Mật khẩu" minlength="6" required>

        <button type="button" onclick="register()">Tạo tài khoản</button>
    </form>

    <div class="text-center">
        Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
    </div>
    <script>
        function register() {
            var name = document.getElementById('full_name').value.trim();
            var email = document.getElementById('email').value.trim();
            var pass = document.getElementById('password').value;

            if (!name || !email || !pass) {
                alert('Vui lòng nhập đầy đủ thông tin!');
                return;
            }

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Email không hợp lệ!');
                return;
            }

            if (pass.length < 6) {
                alert('Mật khẩu tối thiểu 6 ký tự!');
                return;
            }

            document.getElementById('registerForm').submit();
        }
    </script>
</div>

</body>
</html>






