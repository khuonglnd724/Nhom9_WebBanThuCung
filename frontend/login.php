<?php
require_once("../connect.php");

// Tắt báo lỗi kiểu exception tự động (nếu được bật ở cấu hình) để tự xử lý
mysqli_report(MYSQLI_REPORT_OFF);

// Mảng lỗi cho từng form
$loginErrors = [];
$registerErrors = [];
$forgotErrors = [];
$activePanel = 'login'; // login | register | forgot
// Nếu có tham số panel trên URL, chuyển panel tương ứng
if (isset($_GET['panel'])) {
    $panel = strtolower(trim($_GET['panel']));
    if (in_array($panel, ['login','register','forgot'], true)) {
        $activePanel = $panel;
    }
}

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    $error_msg = $conn->connect_error;
    if (strpos($error_msg, 'using password: NO') !== false || strpos($error_msg, 'Connection refused') !== false) {
        die("<h2>Lỗi: MySQL Server không chạy!</h2><p>Vui lòng mở XAMPP và Start MySQL rồi refresh.</p><p>Chi tiết: " . htmlspecialchars($error_msg) . "</p>");
    }
    die("Kết nối CSDL thất bại: " . htmlspecialchars($error_msg));
}

$conn->set_charset("utf8mb4");

// ===== XỬ LÝ ĐĂNG NHẬP =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $activePanel = 'login';

    if ($email === '') { $loginErrors['username'] = 'Vui lòng nhập email.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $loginErrors['username'] = 'Định dạng email không hợp lệ.'; }
    if ($password === '') { $loginErrors['password'] = 'Vui lòng nhập mật khẩu.'; }

    if (empty($loginErrors)) {
        $stmt = $conn->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $loginErrors['username'] = 'Email không tồn tại.';
        } else {
            $user = $result->fetch_assoc();
            if (!password_verify($password, $user['password_hash'])) {
                $loginErrors['password'] = 'Mật khẩu không đúng.';
            } else {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: " . ($user['role']==='ADMIN' ? '../admin/index.php' : 'index.php'));
                exit();
            }
        }
    }
}

// ===== XỬ LÝ ĐĂNG KÝ =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = trim($_POST['reg_username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $activePanel = 'register';

    // Validate cơ bản
    if ($full_name === '') { $registerErrors['reg_username'] = 'Vui lòng nhập họ tên.'; }
    if ($email === '') { $registerErrors['email'] = 'Vui lòng nhập email.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $registerErrors['email'] = 'Định dạng email không hợp lệ.'; }
    if ($phone === '') { $registerErrors['phone'] = 'Vui lòng nhập số điện thoại.'; }
    if ($password === '') { $registerErrors['reg_password'] = 'Vui lòng nhập mật khẩu.'; }

    // Kiểm tra email tồn tại sớm
    if (!isset($registerErrors['email'])) {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        if ($checkRes->num_rows > 0) {
            $registerErrors['email'] = 'Email đã tồn tại.';
        }
    }

    if (empty($registerErrors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, 'CUSTOMER', 'ACTIVE')");
        $stmt->bind_param("ssss", $full_name, $email, $phone, $password_hash);
        try {
            $stmt->execute();
            // Sau khi đăng ký thành công chuyển về form đăng nhập
            $activePanel = 'login';
            $registerSuccessMsg = 'Đăng ký thành công! Bạn có thể đăng nhập.';
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $registerErrors['email'] = 'Email đã tồn tại.';
            } else {
                $registerErrors['general'] = 'Đăng ký thất bại: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}

// ===== XỬ LÝ QUÊN MẬT KHẨU =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot'])) {
    $email = trim($_POST['forgot_email'] ?? '');
    $activePanel = 'forgot';
    if ($email === '') { $forgotErrors['forgot_email'] = 'Vui lòng nhập email.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $forgotErrors['forgot_email'] = 'Định dạng email không hợp lệ.'; }
    if (empty($forgotErrors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $forgotErrors['forgot_email'] = 'Email không tồn tại.';
        } else {
            $forgotSuccessMsg = 'Liên kết đặt lại mật khẩu (demo) đã được xử lý.'; // Placeholder
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập / Đăng ký - StarryPets</title>
        <link rel="stylesheet" href="../assets/css/login-style.css" />
        <style>
            .input-box{position:relative;display:flex;flex-direction:column}
            .error-msg{color:#d93025;font-size:12px;margin-top:4px;line-height:1.3}
            .success-msg{color:#0a7b32;font-size:13px;margin-bottom:10px}
        </style>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <header class="navbar">
        <div class="navbar-logo">
            <i class='bx bxs-paw'></i>
            <a href="index.php">StarryPets</a>
        </div>
        <nav class="navbar-menu">
            <a href="index.php">Trang chủ</a>
            <a href="dichvu.php">Dịch vụ</a>
            <!--
            <a href="gioithieu.php">Giới thiệu</a>
            <a href="lienhe.php">Liên hệ</a>-->
        </nav>
    </header>

    <div class="container <?php echo $activePanel==='register' ? 'active' : ''; ?>">
        <!-- Đăng nhập -->
        <div class="from-box login">
            <form action="" method="POST" novalidate>
                <h1>Đăng nhập</h1>
                <?php if (!empty($loginErrors) && $activePanel==='login'): ?>
                  <div class="error-msg" style="margin-bottom:8px">Có lỗi, vui lòng kiểm tra bên dưới.</div>
                <?php endif; ?>
                <div class="input-box">
                    <input type="email" name="username" placeholder="Email" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required />
                    <i class="bx bx-envelope"></i>
                    <?php if(isset($loginErrors['username'])): ?><span class="error-msg"><?php echo htmlspecialchars($loginErrors['username']); ?></span><?php endif; ?>
                </div>

                <div class="input-box">
                    <input type="password" id="loginPassword" name="password" placeholder="Mật khẩu" required />
                    <button type="button" class="pw-toggle" data-target="loginPassword" aria-label="Hiển thị mật khẩu"><i class="bx bx-lock-alt"></i></button>
                    <?php if(isset($loginErrors['password'])): ?><span class="error-msg"><?php echo htmlspecialchars($loginErrors['password']); ?></span><?php endif; ?>
                </div>
                <div class="forgot-link">
                    <a href="#" class="forgot-btn">Quên mật khẩu</a>
                </div>
                <button type="submit" name="login" class="btn">Đăng nhập</button>
                <!--
                <p>hoặc đăng nhập trên phương tiện khác</p>
                <div class="social-icons">
                    <a href="#"><i class="bx bxl-google"></i></a>
                    <a href="#"><i class="bx bxl-facebook"></i></a>
                    <a href="#"><i class="bx bxl-github"></i></a>
                </div>-->
            </form>
        </div>

        <!-- Đăng ký -->
        <div class="from-box register">
            <form action="" method="POST" novalidate>
                <h1>Đăng ký</h1>
                <?php if (isset($registerSuccessMsg)): ?><div class="success-msg"><?php echo htmlspecialchars($registerSuccessMsg); ?></div><?php endif; ?>
                <?php if (!empty($registerErrors) && $activePanel==='register'): ?>
                  <div class="error-msg" style="margin-bottom:8px">Có lỗi, vui lòng kiểm tra bên dưới.</div>
                <?php endif; ?>
                <div class="input-box">
                    <input type="text" name="reg_username" placeholder="Họ và Tên" value="<?php echo htmlspecialchars($_POST['reg_username'] ?? ''); ?>" required />
                    <i class="bx bx-user"></i>
                    <?php if(isset($registerErrors['reg_username'])): ?><span class="error-msg"><?php echo htmlspecialchars($registerErrors['reg_username']); ?></span><?php endif; ?>
                </div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
                    <i class="bx bxs-envelope"></i>
                    <?php if(isset($registerErrors['email'])): ?><span class="error-msg"><?php echo htmlspecialchars($registerErrors['email']); ?></span><?php endif; ?>
                </div>

                <div class="input-box">
                    <input type="tel" name="phone" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required />
                    <i class="bx bx-phone"></i>
                    <?php if(isset($registerErrors['phone'])): ?><span class="error-msg"><?php echo htmlspecialchars($registerErrors['phone']); ?></span><?php endif; ?>
                </div>

                <div class="input-box">
                    <input type="password" id="registerPassword" name="reg_password" placeholder="Mật khẩu" required />
                    <button type="button" class="pw-toggle" data-target="registerPassword" aria-label="Hiển thị mật khẩu"><i class="bx bx-lock-alt"></i></button>
                    <?php if(isset($registerErrors['reg_password'])): ?><span class="error-msg"><?php echo htmlspecialchars($registerErrors['reg_password']); ?></span><?php endif; ?>
                </div>
                <button type="submit" name="register" class="btn">Đăng ký</button>
                <!--
                <p>hoặc đăng nhập trên phương tiện khác</p>
                <div class="social-icons">
                    <a href="#"><i class="bx bxl-google"></i></a>
                    <a href="#"><i class="bx bxl-facebook"></i></a>
                    <a href="#"><i class="bx bxl-github"></i></a>
                </div>-->
                <?php if(isset($registerErrors['general'])): ?><div class="error-msg" style="margin-top:8px"><?php echo htmlspecialchars($registerErrors['general']); ?></div><?php endif; ?>
            </form>
        </div>

        <!-- Quên mật khẩu -->
        <div class="from-box forgot">
            <form action="" method="POST" novalidate>
                <h1>Quên mật khẩu</h1>
                <p>Nhập địa chỉ email để nhận liên kết đặt lại mật khẩu.</p>

                <div class="input-box">
                    <input type="email" name="forgot_email" placeholder="Email đã đăng ký" value="<?php echo htmlspecialchars($_POST['forgot_email'] ?? ''); ?>" required />
                    <i class="bx bxs-envelope"></i>
                    <?php if(isset($forgotErrors['forgot_email'])): ?><span class="error-msg"><?php echo htmlspecialchars($forgotErrors['forgot_email']); ?></span><?php endif; ?>
                </div>

                <button type="submit" name="forgot" class="btn">Gửi liên kết đặt lại</button>
                <p><a href="#" class="back-to-login">Quay lại đăng nhập</a></p>
                <?php if(isset($forgotSuccessMsg)): ?><div class="success-msg"><?php echo htmlspecialchars($forgotSuccessMsg); ?></div><?php endif; ?>
            </form>
        </div>

        <!-- Panel chuyển đổi -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Xin chào</h1>
                <p>Nếu bạn chưa có tài khoản để đăng nhập thì hãy click vào nút bên </p>
                <button class="btn register-btn">Đăng Ký</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Chào mừng trở lại</h1>
                <p>Đã có sẵn tài khoản</p>
                <button class="btn login-btn">Đăng nhập</button>
            </div>
        </div>
    </div>

        <script src="../assets/js/login-scripts.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                document.querySelectorAll('.pw-toggle').forEach(function(btn){
                    var targetId = btn.getAttribute('data-target');
                    var input = document.getElementById(targetId);
                    btn.addEventListener('click', function(){
                        if (!input) return;
                        var isPwd = input.type === 'password';
                        input.type = isPwd ? 'text' : 'password';
                        btn.innerHTML = isPwd ? '<i class="bx bx-lock-open"></i>' : '<i class="bx bx-lock-alt"></i>';
                    });
                });
            });
        </script>
</body>

</html>






