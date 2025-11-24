<?php
session_start();
include '../connect.php';

$error = '';
$success = '';
$email_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_input = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_input = isset($_POST['password']) ? $_POST['password'] : '';

    try {
        if (!isset($conn)) {
            throw new Exception('Không kết nối được CSDL.');
        }
        $stmt = $conn->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) { throw new Exception('Lỗi prepare: ' . $conn->error); }
        $stmt->bind_param('s', $email_input);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password_input, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_role'] = $row['role'];
                $success = 'Đăng nhập thành công!';
            } else {
                $error = 'Sai mật khẩu.';
            }
        } else {
            $error = 'Email không tồn tại.';
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
    <title>Đăng nhập</title>
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
            width:100%; padding:10px; background:#007bff;
            color:#fff; border:none; border-radius:4px;
            cursor:pointer; font-size:16px;
        }
        button:hover { background:#005fc4; }
        .text-center { text-align:center; margin-top:10px; }
        a { color:#007bff; text-decoration:none; }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Đăng nhập</h2>

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
            <?php 
            $redirect_url = ($_SESSION['user_role'] === 'ADMIN') ? '../admin/index.php' : 'index.php';
            ?>
            setTimeout(function(){ window.location.href = '<?php echo $redirect_url; ?>'; }, 1000);
        </script>
    <?php endif; ?>

    <form method="post" action="" id="loginForm">
        <input type="email" name="email" id="email" placeholder="Email" maxlength="120" value="<?php echo htmlspecialchars($email_input, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="password" name="password" id="password" placeholder="Mật khẩu" minlength="6">
        <button type="button" onclick="login()">Đăng nhập</button>
    </form>

    <div class="text-center">
        Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
    </div>
</div>

<script>
    function login() {
        var email = document.getElementById('email').value.trim();
        var pass = document.getElementById('password').value;
        if (!email || !pass) {
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
        document.getElementById('loginForm').submit();
    }
</script>

</body>
</html>






