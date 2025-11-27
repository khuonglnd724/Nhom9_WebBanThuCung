<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
require_once("../connect.php");
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Liên hệ - StarryPets</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
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
          <li><a href="index.php">Trang chủ</a></li>
          <li class="dropdown">
            <a href="pet.php" class="dropdown-toggle">Thú cưng <span class="caret" style="font-size:12px">▼</span></a>
            <ul class="dropdown-menu">
              <?php
                if (isset($conn) && !$conn->connect_error) {
                  $conn->set_charset('utf8mb4');
                  $breedSql = "SELECT id, name FROM breeds WHERE pet_type='DOG' ORDER BY name ASC";
                  if ($breedRes = $conn->query($breedSql)) {
                    if ($breedRes->num_rows > 0) {
                      while ($br = $breedRes->fetch_assoc()) {
                        echo '<div><a href="pet.php?breed_id=' . (int)$br['id'] . '">Chó ' . htmlspecialchars($br['name']) . '</a></div>';
                      }
                    }
                  }
                  $catSql = "SELECT id, name FROM breeds WHERE pet_type='CAT' ORDER BY name ASC";
                  if ($catRes = $conn->query($catSql)) {
                    if ($catRes->num_rows > 0) {
                      while ($cr = $catRes->fetch_assoc()) {
                        echo '<div><a href="pet.php?breed_id=' . (int)$cr['id'] . '">Mèo ' . htmlspecialchars($cr['name']) . '</a></div>';
                      }
                    }
                  }
                }
              ?>
            </ul>
          </li>
          <li class="dropdown">
            <a href="category.php" class="dropdown-toggle">Phụ kiện <span class="caret" style="font-size:12px">▼</span></a>
            <ul class="dropdown-menu">
              <?php
                if (isset($conn) && !$conn->connect_error) {
                  $accCatSql = "SELECT id, name FROM categories WHERE type='ACCESSORY' ORDER BY name ASC";
                  if ($accCatRes = $conn->query($accCatSql)) {
                    if ($accCatRes->num_rows > 0) {
                      while ($ac = $accCatRes->fetch_assoc()) {
                        echo '<div><a href="category.php?category_id=' . (int)$ac['id'] . '">Phụ kiện ' . htmlspecialchars($ac['name']) . '</a></div>';
                      }
                    }
                  }
                }
              ?>
            </ul>
          </li>
          <li><a href="gioithieu.php">Giới thiệu</a></li>
          <li class="active"><a href="lienhe.php">Liên hệ</a></li>
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

    <!-- Banner Slider Start -->
    <div class="banner-slider">
      <div class="slides">
        <div class="slide active"><img src="../assets/images/banner 1.jpg" alt="Banner 1"></div>
        <div class="slide"><img src="../assets/images/banner 2.jpg" alt="Banner 2"></div>
        <div class="slide"><img src="../assets/images/banner 3.jpg" alt="Banner 3"></div>
        <div class="slide"><img src="../assets/images/banner 4.jpg" alt="Banner 4"></div>
      </div>
      <button class="slider-btn prev">&#10094;</button>
      <button class="slider-btn next">&#10095;</button>
      <div class="slider-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>
    <!-- Banner Slider End -->
  </header>

  <main>
    <!-- Content Section -->
    <section style="margin: 40px auto; padding: 40px 20px; max-width: 1200px;">
      <div>
        <h2 style="font-size: 32px; margin-bottom: 20px; color: #333; text-align: center;">Liên Hệ Với StarryPets</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
          <div style="text-align: center;">
            <h4 style="font-size: 18px; margin-bottom: 15px; color: #333;">📞 Điện Thoại</h4>
            <p style="font-size: 16px; color: #666;">
              <a href="tel:0939863696" style="color: #ff6b6b; text-decoration: none; font-weight: bold;">0939 86 36 96</a>
            </p>
            <p style="font-size: 14px; color: #999;">028 3899 2862</p>
          </div>
          
          <div style="text-align: center;">
            <h4 style="font-size: 18px; margin-bottom: 15px; color: #333;">📧 Email</h4>
            <p style="font-size: 16px; color: #666;">
              <a href="mailto:starrypet@gmail.com" style="color: #ff6b6b; text-decoration: none;">starrypet@gmail.com</a>
            </p>
          </div>
        </div>
        
        <div style="background: #f9f9f9; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
          <h4 style="font-size: 18px; margin-bottom: 15px; color: #333;">📍 Địa Chỉ</h4>
          <p style="font-size: 16px; color: #666; line-height: 1.8;">
            70 Đường Tô Ký, Tân Chánh Hiệp, Quận 12, Thành phố Hồ Chí Minh
          </p>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
          <p style="font-size: 16px; color: #666; margin-bottom: 20px;">
            Chúng tôi luôn sẵn sàng tư vấn và hỗ trợ bạn. Đừng ngần ngại liên hệ với chúng tôi bất cứ lúc nào!
          </p>
        </div>
      </div>
    </section>

  </main>

  <section class="instagram-row">
    <div class="container insta-inner">
      <div class="insta-list">
        <img src="../assets/images/insta-list/1.png" alt="pet">
        <img src="../assets/images/insta-list/2.png" alt="pet">
        <img src="../assets/images/insta-list/3.png" alt="pet">
        <img src="../assets/images/insta-list/4.png" alt="pet">
        <img src="../assets/images/insta-list/5.png" alt="pet">
        <img src="../assets/images/insta-list/6.png" alt="pet">
        <img src="../assets/images/insta-list/7.png" alt="pet">
      </div>
    </div>
  </section>
  <footer class="site-footer">
    <div class="container footer-inner">
      <div class="col">
        <h4>Liên hệ</h4>
        <p>Địa chỉ: 70 Đ. Tô Ký, Tân Chánh Hiệp, Quận 12, Thành phố Hồ Chí Minh</p>
        <p>Điện thoại: <a href="tel:02838992862">028 3899 2862</a></p>
        <p>Email: <a href="mailto:starrypet@gmail.com">starrypet@gmail.com</a></p>
      </div>
      <div class="col">
        <h4>Follow</h4>
        <div class="socials">
          <a href="#">Facebook</a>
          <a href="#">Instagram</a>
          <a href="#">YouTube</a>
        </div>
      </div>
      <div class="col">
        <h4>Thông tin</h4>
        <ul class="footer-links">
          <li><a href="#">Chính sách</a></li>
          <li><a href="#">Terms & Conditions</a></li>
          <li><a href="#">Site Map</a></li>
        </ul>
      </div>
    </div>
    <div class="container footer-credits">Design by:StarryPets Team</div>
  </footer>
  <script src="../assets/js/script.js"></script>
  <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/product-modal.js"></script>
    <script>
      // Fallback slider script (ensures slider always works)
      document.addEventListener('DOMContentLoaded', function() {
        var slides = document.querySelectorAll('.banner-slider .slide');
        var dots = document.querySelectorAll('.slider-dots .dot');
        var prevBtn = document.querySelector('.slider-btn.prev');
        var nextBtn = document.querySelector('.slider-btn.next');
        var current = 0;
        var timer;

        function showSlide(idx) {
          slides.forEach(function(slide, i) {
            slide.classList.toggle('active', i === idx);
            if (dots[i]) dots[i].classList.toggle('active', i === idx);
          });
          current = idx;
        }
        function nextSlide() {
          showSlide((current + 1) % slides.length);
        }
        function prevSlide() {
          showSlide((current - 1 + slides.length) % slides.length);
        }
        if (nextBtn && prevBtn) {
          nextBtn.addEventListener('click', function() {
            nextSlide();
            resetTimer();
          });
          prevBtn.addEventListener('click', function() {
            prevSlide();
            resetTimer();
          });
        }
        dots.forEach(function(dot, i) {
          dot.addEventListener('click', function() {
            showSlide(i);
            resetTimer();
          });
        });
        function autoSlide() {
          timer = setInterval(nextSlide, 4000);
        }
        function resetTimer() {
          clearInterval(timer);
          autoSlide();
        }
        showSlide(0);
        autoSlide();
      });
    </script>
  <div class="hotline-btn" id="hotlineBtn">
    <div class="phone-icon">📞</div>
    <div>
      <div style="font-size:12px;opacity:0.9">HOTLINE</div>
      <div style="font-weight:700">0939.86.36.96</div>
    </div>
  </div>
</body>
</html>






