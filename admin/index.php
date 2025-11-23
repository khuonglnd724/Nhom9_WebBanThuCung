<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>PetSky - Trang Chủ</title>
<link rel="stylesheet" href="../assets/css/styleadmin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div id="container">
    <!-- Sidebar -->
    <div id="sidebar">
        <div id="logo">
            <img src="../assets/images/logo1.png" >
            <span>StarryPets</span>
        </div>
        <?php include('menu.php'); ?>
    </div>

    <!-- Main area -->
    <div id="main-area">
        <div id="banner">StarryPets - Thế Giới Thú Cưng</div>

        <div id="main-content">
            <?php 
                // Lấy tham số p từ URL, mặc định là main
                $page = isset($_GET['p']) ? $_GET['p'] : 'main';
                
                // Danh sách các file hợp lệ
                $valid_pages = ['main', 'thucung', 'phukien', 'donhang','thongke','user'];

                if (in_array($page, $valid_pages)) {
                    include($page . '.php');
                } else {
                    include('main.php');
                }
            ?>
        </div>

        <div id="footer">© 2025 PetSky - All Rights Reserved</div>
    </div>
</div>

</body>
</html>
