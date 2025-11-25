<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giỏ hàng - StarryPets</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body<?php if ($isLoggedIn): ?> data-user-id="<?php echo htmlspecialchars($_SESSION['user_id']); ?>"<?php endif; ?>>
  <header class="site-header">
    <div class="container header-inner">
      <a class="logo" href="index.php">
        <img src="../assets/images/logo1.png" alt="StarryPets Logo" style="height:100px;width:auto;">
      </a>
      <nav class="main-nav" id="mainNav">
        <ul class="menu">
          <li><a href="index.php">Trang chủ</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle">Thú cưng <span style="font-size:12px">▼</span></a>
            <ul class="dropdown-menu">
              <li><a href="alaska.php">Chó Alaska Malamute</a></li>
              <li><a href="beagle.php">Chó Beagle</a></li>
              <li><a href="corgi.php">Chó Corgi</a></li>
              <li><a href="golden.php">Chó Golden Retriever</a></li>
              <li><a href="husky.php">Chó Husky Siberian</a></li>
              <li><a href="pomeranian.php">Chó Phốc Sóc – Pomeranian</a></li>
              <li><a href="poodle.php">Chó Poodle</a></li>
              <li><a href="pug.php">Chó Pug</a></li>
              <li><a href="samoyed.php">Chó Samoyed</a></li>
              <li><a href="meoanhlongdai.php">Mèo Anh (Dài + Ngắn)</a></li>
              <li><a href="meochanngan.php">Mèo Chân Ngắn</a></li>
              <li><a href="meotaicup.php">Mèo Tai Cụp</a></li>
            </ul>
          </li>
          <li><a href="category.php">Phụ kiện</a></li>
          <li><a href="gioithieu.php">Giới thiệu</a></li>
          <li><a href="lienhe.php">Liên hệ</a></li>
        </ul>
      </nav>
      <div class="header-actions">
        <button id="cartToggle" class="cart-btn">
          <span class="cart-icon">🛒</span>
          <span class="cart-label"><strong>Giỏ hàng</strong><br><span class="cart-count">0</span> sản phẩm - 0đ</span>
        </button>
        <button id="mobileToggle" class="mobile-toggle" aria-label="menu">☰</button>
      </div>
    </div>
    <div class="auth-links">
      <?php if ($isLoggedIn): ?>
        <span style="margin-right: 15px; color: #333;">Xin chào, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
        <a href="logout.php" class="btn-login">Đăng xuất</a>
      <?php else: ?>
        <a href="../frontend/login.php" class="btn-login">Đăng nhập</a>
        <a href="../frontend/register.php" class="btn-register">Đăng ký</a>
      <?php endif; ?>
    </div>
    <div class="mini-cart" id="miniCart" aria-hidden="true">
      <div class="mini-inner">
        <h4>Giỏ hàng (<span class="cart-count">0</span>)</h4>
        <div class="mini-items">Chưa có sản phẩm</div>
        <div class="mini-total">Tổng: <strong>0₫</strong></div>
        <div class="mini-actions">
          <a href="cart.php" class="btn">Xem giỏ</a>
          <a href="#" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>
    </div>
  </header>
  <main class="container">
    <h1>Giỏ hàng</h1>
    
    <div class="cart-page">
      <div class="cart-page-items" style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <!-- Các sản phẩm sẽ được thêm vào đây bởi cart.js -->
      </div>

      <div class="cart-summary" style="background: #fff; padding: 20px; border-radius: 8px; text-align: right;">
        <h3>Tổng tiền: <span class="cart-page-total">0₫</span></h3>
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
          <a href="index.php" class="btn" style="background: #ccc; color: #000;">Tiếp tục mua sắm</a>
          <a href="thanhtoan.php" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>
    </div>

    <!-- Banner Slider Start -->
    <div class="banner-slider" style="margin-top: 40px;">
      <div class="slides">
        <div class="slide active"><img src="../assets/images/banner 1.jpg" alt="Banner 1"></div>
        <div class="slide"><img src="../assets/images/banner 2.jpg" alt="Banner 2"></div>
        <div class="slide"><img src="../assets/images/banner 3.jpg" alt="Banner 3"></div>
      </div>
      <button class="slider-btn prev">&#10094;</button>
      <button class="slider-btn next">&#10095;</button>
      <div class="slider-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>
    <!-- Banner Slider End -->
          <li><a href="#">Site Map</a></li>
        </ul>
      </div>
    </div>
    <div class="container footer-credits">@2019 - Design by:StaryPets Team</div>
  </footer>

    <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/script.js"></script>
  <script src="../assets/js/product-modal.js"></script>
  <div class="hotline-btn" id="hotlineBtn">
    <div class="phone-icon">📞</div>
    <div>
      <div style="font-size:12px;opacity:0.9">HOTLINE</div>
      <div style="font-weight:700">0939.86.36.96</div>
    </div>
  </div>
</body>
</html>






