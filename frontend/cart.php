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
        <img src="../assets/images/logo.png" alt="StarryPets Logo" style="height:100px;width:auto;">
      </a>
      <nav class="main-nav" id="mainNav">
        <ul class="menu">
          <li class="active"><a href="index.php">Trang chủ</a></li>
          <li class="dropdown">
            <a href="pet.php" class="dropdown-toggle">Thú cưng <span class="caret" style="font-size:12px">▼</span></a>
            <ul class="dropdown-menu">
              <?php
                // Hiển thị các giống chó từ DB, chỉ loại DOG
                if (isset($conn) && !$conn->connect_error) {
                  $conn->set_charset('utf8mb4');
                  $breedSql = "SELECT id, name FROM breeds WHERE pet_type='DOG' ORDER BY name ASC";
                  if ($breedRes = $conn->query($breedSql)) {
                    if ($breedRes->num_rows > 0) {
                      while ($br = $breedRes->fetch_assoc()) {
                        $bid = (int)$br['id'];
                        $bname = htmlspecialchars($br['name']);
                        echo '<div><a href="pet.php?breed_id=' . $bid . '">Chó ' . $bname . '</a></div>';
                      }
                    } else {
                      echo '<div><span>Chưa có giống chó</span></div>';
                    }
                  } else {
                    echo '<div><span>Lỗi tải giống chó</span></div>';
                  }
                }

                // Hiển thị các giống mèo từ DB, chỉ loại CAT
                if (isset($conn) && !$conn->connect_error) {
                  $conn->set_charset('utf8mb4');
                  $catSql = "SELECT id, name FROM breeds WHERE pet_type='CAT' ORDER BY name ASC";
                  if ($catRes = $conn->query($catSql)) {
                    if ($catRes->num_rows > 0) {
                      while ($cr = $catRes->fetch_assoc()) {
                        $cid = (int)$cr['id'];
                        $cname = htmlspecialchars($cr['name']);
                        echo '<div><a href="pet.php?breed_id=' . $cid . '">Mèo ' . $cname . '</a></div>';
                      }
                    } else {
                      echo '<div><span>Chưa có giống mèo</span></div>';
                    }
                  } else {
                    echo '<div><span>Lỗi tải giống mèo</span></div>';
                  }
                }
              ?>
            </ul>
          </li>
          <li class="dropdown">
            <a href="category.php" class="dropdown-toggle">Phụ kiện <span class="caret" style="font-size:12px">▼</span></a>
            <ul class="dropdown-menu">
              <?php
                // Hiển thị các loại phụ kiện từ DB (bảng categories), chỉ type ACCESSORY
                if (isset($conn) && !$conn->connect_error) {
                  $conn->set_charset('utf8mb4');
                  $accCatSql = "SELECT id, name FROM categories WHERE type='ACCESSORY' ORDER BY name ASC";
                  if ($accCatRes = $conn->query($accCatSql)) {
                    if ($accCatRes->num_rows > 0) {
                      while ($ac = $accCatRes->fetch_assoc()) {
                        $aid = (int)$ac['id'];
                        $aname = htmlspecialchars($ac['name']);
                        echo '<div><a href="category.php?category_id=' . $aid . '">Phụ kiện ' . $aname . '</a></div>';
                      }
                    } else {
                      echo '<div><span>Chưa có loại phụ kiện</span></div>';
                    }
                  } else {
                    echo '<div><span>Lỗi tải loại phụ kiện</span></div>';
                  }
                }
              ?>
            </ul>
          </li>
          <li><a href="gioithieu.php">Giới thiệu</a></li>
          <li><a href="lienhe.php">Liên hệ</a></li>
        </ul>
      </nav>
      <div class="header-actions">
        <a id="cartToggle" href="cart.php" class="cart-btn" title="Xem giỏ hàng">
          <span class="cart-icon">🛒</span>
          <span class="cart-label"><strong>Giỏ hàng</strong><br><span class="cart-count">0</span> sản phẩm - 0đ</span>
        </a>
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
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
          <a href="index.php" class="btn" style="background: #ccc; color: #000;">Tiếp tục mua sắm</a>
          <a href="thanhtoan.php" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>

      <div style="background: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: center; border-top: 2px solid #f0f0f0;">
        <p style="margin-bottom: 15px; color: #666;">Muốn xem các đơn hàng trước đây?</p>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
          
          <a href="order-history.php" class="btn" style="background: #666; color: #fff; text-decoration: none;">📋 Lịch sử mua hàng</a>
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






