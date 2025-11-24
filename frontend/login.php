<?php
$mysqli = new mysqli('localhost', 'root', '', 'wedthucung');
if ($mysqli->connect_error) {
    die("Kết nối CSDL thất bại: " . $mysqli->connect_error);
}

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            alert('Đăng nhập thành công với username: $username');
            window.location.href = 'index.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Đăng nhập thất bại! Sai tên đăng nhập hoặc mật khẩu.');</script>";
    }
}

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['reg_username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['reg_password'];

    $stmt = $mysqli->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $phone, $password);
    $register_success = $stmt->execute();

    if ($register_success) {
        echo "<script>
            alert('Đăng ký thành công với email: $email');
            document.addEventListener('DOMContentLoaded', function() {
              const container = document.querySelector('.container');
              container.classList.remove('active');
            });
        </script>";
    } else {
        echo "<script>alert('Đăng ký thất bại! Có thể email hoặc username đã tồn tại.');</script>";
    }
}

// Xử lý form quên mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot'])) {
    $email = $_POST['forgot_email'];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Liên kết đặt lại mật khẩu đã được gửi đến email: $email');</script>";
    } else {
        echo "<script>alert('Không tìm thấy email trong hệ thống.');</script>";
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
            <a href="gioithieu.php">Giới thiệu</a>
            <a href="lienhe.php">Liên hệ</a>
        </nav>
    </header>

    <div class="container">
        <!-- Đăng nhập -->
        <div class="from-box login">
            <form action="" method="POST">
                <h1>Đăng nhập</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Tên Đăng Nhập" required />
                    <i class="bx bx-user"></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Mật khẩu" required />
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="forgot-link">
                    <a href="#" class="forgot-btn">Quên mật khẩu</a>
                </div>
                <button type="submit" name="login" class="btn">Đăng nhập</button>
                <p>hoặc đăng nhập trên phương tiện khác</p>
                <div class="social-icons">
                    <a href="#"><i class="bx bxl-google"></i></a>
                    <a href="#"><i class="bx bxl-facebook"></i></a>
                    <a href="#"><i class="bx bxl-github"></i></a>
                </div>
            </form>
        </div>

        <!-- Đăng ký -->
        <div class="from-box register">
            <form action="" method="POST">
                <h1>Đăng ký</h1>
                <div class="input-box">
                    <input type="text" name="reg_username" placeholder="Tên Đăng Ký" required />
                    <i class="bx bx-user"></i>
                </div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required />
                    <i class="bx bxs-envelope"></i>
                </div>

                <div class="input-box">
                    <input type="tel" name="phone" placeholder="Số điện thoại" required />
                    <i class="bx bx-phone"></i>
                </div>

                <div class="input-box">
                    <input type="password" name="reg_password" placeholder="Mật khẩu" required />
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="forgot-link">
                    <a href="#" class="forgot-btn">Quên mật khẩu</a>
                </div>
                <button type="submit" name="register" class="btn">Đăng ký</button>
                <p>hoặc đăng nhập trên phương tiện khác</p>
                <div class="social-icons">
                    <a href="#"><i class="bx bxl-google"></i></a>
                    <a href="#"><i class="bx bxl-facebook"></i></a>
                    <a href="#"><i class="bx bxl-github"></i></a>
                </div>
            </form>
        </div>

        <!-- Quên mật khẩu -->
        <div class="from-box forgot">
            <form action="" method="POST">
                <h1>Quên mật khẩu</h1>
                <p>Nhập địa chỉ email để nhận liên kết đặt lại mật khẩu.</p>

                <div class="input-box">
                    <input type="email" name="forgot_email" placeholder="Email đã đăng ký" required />
                    <i class="bx bxs-envelope"></i>
                </div>

                <button type="submit" name="forgot" class="btn">Gửi liên kết đặt lại</button>
                <p><a href="#" class="back-to-login">Quay lại đăng nhập</a></p>
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
</body>

</html>






