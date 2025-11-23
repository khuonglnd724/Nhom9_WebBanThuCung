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

    <input type="text" id="username" placeholder="Tên đăng nhập">
    <input type="email" id="email" placeholder="Email">
    <input type="password" id="password" placeholder="Mật khẩu">

    <button onclick="register()">Tạo tài khoản</button>

    <div class="text-center">
        Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
    </div>
</div>

<script>
    function register() {
        let user = document.getElementById("username").value;
        let email = document.getElementById("email").value;
        let pass = document.getElementById("password").value;

        if (user === "" || email === "" || pass === "") {
            alert("Vui lòng nhập đầy đủ thông tin!");
            return;
        }

        alert("Đăng ký thành công! Hãy đăng nhập.");
        window.location.href = "login.php";
    }
</script>

</body>
</html>






