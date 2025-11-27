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
  <title>StarryPets — Trang chủ</title>
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
          <a href="thanhtoan.php" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>
    </div>



  <!-- Modal Container -->
  <div id="modalContainer"></div>

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

<!--    
    <div class="container header-search-bar" style="margin-top: 0;">
      <div class="search-wrap">
        <select class="cat-select"><option>Tất cả danh mục</option></select>
        <input class="search-input" placeholder="Tìm kiếm..." />
        <button class="btn search-btn" aria-label="search">🔍</button>
      </div>
    </div>
    </header>

    
    <section class="hero container">
      <div class="hero-left">
        <h1>Chăm sóc & yêu thương thú cưng của bạn</h1>
        <p>Phụ kiện – Thực phẩm – Dịch vụ giao hàng toàn quốc</p>
        <a class="btn btn-primary" href="category.php">Xem sản phẩm</a>
      </div>
      <div class="hero-right">
        <img src="/mnt/data/b5ec088e-2759-450e-a7da-79bf94582a86.png" alt="StarryPets hero">
      </div>
    </section>

    
    <section class="breadcrumb container">
      <span>Trang chủ</span> <span class="sep">|</span> <span>Danh mục sản phẩm</span>
    </section>
-->
    <!-- New products (populated from site content) -->
    <section id="products" class="container products-section">
      <h2 class="section-title">Thú Cưng</h2>

      <!-- Chỗ code lọc sản phẩm 
    <div class="filter-bar">
        <select id="filter-type">
            <option value="">Lọc theo loại</option>
            <option value="dog">Chó</option>
            <option value="cat">Mèo</option>
        </select>

        <select id="filter-breed">
            <option value="">Lọc theo giống</option>
            <option value="alaska">Alaska</option>
            <option value="samoyed">Samoyed</option>
            <option value="golden">Golden</option>
            <option value="poodle">Poodle</option>
            <option value="pomeranian">Phốc sóc</option>
            <option value="pug">Pug</option>
            <option value="cat-short">Mèo lông ngắn</option>
            <option value="cat-golden">Mèo golden</option>
        </select>

        <select id="filter-price">
            <option value="">Lọc theo giá</option>
            <option value="low">Dưới 10 triệu</option>
            <option value="mid">10 – 20 triệu</option>
            <option value="high">Trên 20 triệu</option>
        </select>

      <button id="filter-btn" class="btn btn-primary">Lọc</button>
    </div>
    -->
      <div class="products-grid">
        <?php
          require_once("../connect.php");
          if ($conn && !$conn->connect_error) {
            $conn->set_charset("utf8mb4");
            $sql = "SELECT p.id, p.name, p.price, p.stock, p.status, p.description, p.age_months, p.color, p.size, p.gender,
                           b.name AS breed_name,
                           (SELECT image_url FROM images i
                            WHERE i.item_type='PET' AND i.item_id=p.id
                            ORDER BY is_primary DESC, display_order ASC, id ASC
                            LIMIT 1) AS image_url
                    FROM pets p
                    LEFT JOIN breeds b ON p.breed_id = b.id
                    WHERE p.stock > 0
                    ORDER BY p.created_at DESC, p.id DESC
                    LIMIT 8";
            $res = $conn->query($sql);
            if ($res && $res->num_rows > 0) {
              while ($row = $res->fetch_assoc()) {
                $img = $row['image_url'] ? ('../' . $row['image_url']) : ('https://placehold.co/600x500?text=' . rawurlencode($row['name']));
                $price = number_format((float)$row['price'], 0, ',', '.') . '₫';
                
                // Chuẩn bị dữ liệu cho modal
                $dataAttrs = 'data-id="pet-' . (int)$row['id'] . '" ';
                $dataAttrs .= 'data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '" ';
                $dataAttrs .= 'data-price="' . htmlspecialchars($price, ENT_QUOTES) . '" ';
                $dataAttrs .= 'data-image="' . htmlspecialchars($img, ENT_QUOTES) . '" ';
                $dataAttrs .= 'data-breed="' . htmlspecialchars($row['breed_name'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
                $dataAttrs .= 'data-age="' . ($row['age_months'] ? $row['age_months'] . ' tháng' : 'Chưa rõ') . '" ';
                $dataAttrs .= 'data-color="' . htmlspecialchars($row['color'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
                $dataAttrs .= 'data-size="' . htmlspecialchars($row['size'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
                $dataAttrs .= 'data-gender="' . ($row['gender'] === 'MALE' ? 'Đực' : ($row['gender'] === 'FEMALE' ? 'Cái' : 'Chưa rõ')) . '" ';
                $dataAttrs .= 'data-status="' . ($row['status'] === 'AVAILABLE' ? 'Còn hàng' : ($row['status'] === 'SOLD' ? 'Đã bán' : 'Không khả dụng')) . '" ';
                $dataAttrs .= 'data-description="' . htmlspecialchars($row['description'] ?: 'Chưa có thông tin chi tiết.', ENT_QUOTES) . '"';
                
                echo '<article class="product-card">';
                echo '  <div class="thumb"><img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($row['name']) . '"></div>';
                echo '  <h3 class="title">' . htmlspecialchars($row['name']) . '</h3>';
                if (!empty($row['breed_name'])) {
                  echo '  <div class="meta">' . htmlspecialchars($row['breed_name']) . '</div>';
                }
                echo '  <div class="price">' . $price . '</div>';
                echo '  <div class="actions">';
                echo '    <button class="btn add-to-cart" data-id="pet-' . (int)$row['id'] . '" data-stock="' . (int)$row['stock'] . '">Mua hàng</button>';
                echo '    <button class="btn btn-outline view-product-btn" ' . $dataAttrs . '>Xem</button>';
                echo '  </div>';
                echo '</article>';
              }
            } else {
              echo '<p>Chưa có thú cưng nào trong hệ thống.</p>';
            }
          } else {
            echo '<p>Lỗi kết nối CSDL. Vui lòng kiểm tra cấu hình trong connect.php</p>';
          }
        ?>
      </div>
      <div class="more center"><a class="btn" href="pet.php">Xem thêm</a></div>
      <h2 class="section-title">Phụ kiện</h2>
      <div class="products-grid">
          <?php
            // Hiển thị danh sách phụ kiện
            if (isset($conn) && !$conn->connect_error) {
              $sqlA = "SELECT a.id, a.name, a.price, a.stock, a.status, a.description, a.brand, a.material, a.size,
                              (SELECT image_url FROM images i
                               WHERE i.item_type='ACCESSORY' AND i.item_id=a.id
                               ORDER BY is_primary DESC, display_order ASC, id ASC
                               LIMIT 1) AS image_url
                       FROM accessories a
                       WHERE a.stock > 0
                       ORDER BY a.created_at DESC, a.id DESC
                       LIMIT 8";
              $resA = $conn->query($sqlA);
              if ($resA && $resA->num_rows > 0) {
                while ($rowA = $resA->fetch_assoc()) {
                  $imgA = $rowA['image_url'] ? ('../' . $rowA['image_url']) : ('https://placehold.co/600x500?text=' . rawurlencode($rowA['name']));
                  $priceA = number_format((float)$rowA['price'], 0, ',', '.') . '₫';
                  $statusText = ($rowA['status'] === 'ACTIVE') ? 'Đang bán' : (($rowA['status'] === 'OUT_OF_STOCK') ? 'Hết hàng' : 'Ngừng bán');

                  // Dữ liệu cho modal phụ kiện (modal riêng)
                  $dataAttrsA = 'data-id="acc-' . (int)$rowA['id'] . '" ';
                  $dataAttrsA .= 'data-name="' . htmlspecialchars($rowA['name'], ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-price="' . htmlspecialchars($priceA, ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-image="' . htmlspecialchars($imgA, ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-brand="' . htmlspecialchars($rowA['brand'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-material="' . htmlspecialchars($rowA['material'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-size="' . htmlspecialchars($rowA['size'] ?: '—', ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-status="' . htmlspecialchars($statusText, ENT_QUOTES) . '" ';
                  $dataAttrsA .= 'data-description="' . htmlspecialchars($rowA['description'] ?: 'Chưa có thông tin chi tiết.', ENT_QUOTES) . '"';

                  echo '<article class="product-card">';
                  echo '  <div class="thumb"><img src="' . htmlspecialchars($imgA) . '" alt="' . htmlspecialchars($rowA['name']) . '"></div>';
                  echo '  <h3 class="title">' . htmlspecialchars($rowA['name']) . '</h3>';
                  if (!empty($rowA['brand'])) {
                    echo '  <div class="meta">' . htmlspecialchars($rowA['brand']) . '</div>';
                  }
                  echo '  <div class="price">' . $priceA . '</div>';
                  echo '  <div class="actions">';
                  echo '    <button class="btn add-to-cart" data-id="acc-' . (int)$rowA['id'] . '" data-stock="' . (int)$rowA['stock'] . '">Mua hàng</button>';
                  echo '    <button class="btn btn-outline view-accessory-btn" ' . $dataAttrsA . '>Xem</button>';
                  echo '  </div>';
                  echo '</article>';
                }
              } else {
                echo '<p>Chưa có phụ kiện nào trong hệ thống.</p>';
              }
            } else {
              echo '<p>Lỗi kết nối CSDL. Vui lòng kiểm tra cấu hình trong connect.php</p>';
            }
          ?>
        </div>
        <div class="more center"><a class="btn" href="category.php">Xem thêm</a></div>

    </section>

  </main>

  <section class="instagram-row">
    <div class="container insta-inner">
      <div class="insta-list">
        <!-- placeholders for instagram thumbnails -->
        <img src="https://placehold.co/120x120?text=pet1" alt="pet">
        <img src="https://placehold.co/120x120?text=pet2" alt="pet">
        <img src="https://placehold.co/120x120?text=pet3" alt="pet">
        <img src="https://placehold.co/120x120?text=pet4" alt="pet">
        <img src="https://placehold.co/120x120?text=pet5" alt="pet">
        <img src="https://placehold.co/120x120?text=pet6" alt="pet">
        <img src="https://placehold.co/120x120?text=pet7" alt="pet">
      </div>
    </div>
  </section>

  <footer class="site-footer">
    <div class="container footer-inner">
      <div class="col">
        <h4>Liên hệ</h4>
        <p>Địa chỉ: 70 Đ. Tô Ký, Tân Chánh Hiệp, Quận 12, Thành phố Hồ Chí Minh</p>
        <p>Điện thoại: <a href="tel:0939863696">028 3899 2862</a></p>
        <p>Email: <a href="mailto:matpetfamily2011@gmail.com">matpetfamily2011@gmail.com</a></p>
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
    <div class="container footer-credits">@2019 - Design by:StaryPets Team</div>
  </footer>

  <script src="../assets/js/script.js"></script>
  <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/accessory-modal.js"></script>
  <script src="../assets/js/product-modal-handler.js"></script>
  <script src="../assets/js/accessory-modal-handler.js"></script>
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






