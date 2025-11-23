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

    <input type="text" id="username" placeholder="Tên đăng nhập">
    <input type="password" id="password" placeholder="Mật khẩu">

    <button onclick="login()">Đăng nhập</button>

    <div class="text-center">
        Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
    </div>
</div>

<script>
    function login() {
        let user = document.getElementById("username").value;
        let pass = document.getElementById("password").value;

        if (user === "" || pass === "") {
            alert("Vui lòng nhập đầy đủ thông tin!");
            return;
        }

        // Giả lập đăng nhập thành công
        localStorage.setItem("userLoggedIn", "true");
        localStorage.setItem("username", user);

        alert("Đăng nhập thành công!");
        window.location.href = "index.php";
    }
</script>

</body>
</html>






