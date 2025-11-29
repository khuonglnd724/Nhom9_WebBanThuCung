<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Pagination setup
$limit = 16;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }

// Optional filter by accessory category
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$priceParam = isset($_GET['price']) ? trim($_GET['price']) : '';
$selectedCategoryName = null;

require_once("../connect.php");
$totalAcc = 0;
$totalPages = 1;
if ($conn && !$conn->connect_error) {
  $conn->set_charset("utf8mb4");
  // Lấy tên loại phụ kiện nếu có
  if ($categoryId > 0) {
    $catNameRes = $conn->query("SELECT name FROM categories WHERE id=" . $categoryId . " LIMIT 1");
    if ($catNameRes && $catNameRow = $catNameRes->fetch_assoc()) {
      $selectedCategoryName = $catNameRow['name'];
    }
  }

  // Xây WHERE theo filter
  $conditions = [];
  $conditions[] = "a.is_visible = 1"; // Chỉ hiển thị phụ kiện visible
  $conditions[] = "a.stock > 0"; // Chỉ hiển thị phụ kiện còn hàng
  if ($categoryId > 0) { $conditions[] = "a.category_id = $categoryId"; }
  // Lọc theo giá phù hợp phụ kiện (VND)
  // low: dưới 100k; mid: 100k–300k; high: 300k–700k; over: trên 700k
  if ($priceParam === 'low') {
    $conditions[] = "a.price < 100000";
  } elseif ($priceParam === 'mid') {
    $conditions[] = "a.price >= 100000 AND a.price <= 500000";
  } elseif ($priceParam === 'high') {
    $conditions[] = "a.price > 500000 AND a.price <= 1000000";
  } elseif ($priceParam === 'over') {
    $conditions[] = "a.price > 1000000";
  }
  $whereSql = count($conditions) ? ('WHERE ' . implode(' AND ', $conditions)) : '';

  // Đếm tổng theo filter (nếu có)
  $countSql = "SELECT COUNT(*) AS cnt FROM accessories a $whereSql";
  $countRes = $conn->query($countSql);
  if ($countRes && $countRow = $countRes->fetch_assoc()) {
    $totalAcc = (int)$countRow['cnt'];
    $totalPages = max(1, (int)ceil($totalAcc / $limit));
    if ($page > $totalPages) { $page = $totalPages; }
  }
  $offset = ($page - 1) * $limit;
  $sqlAcc = <<<SQL
SELECT a.id, a.name, a.price, a.stock, a.description, a.brand, a.material, a.size, a.created_at,
       (SELECT image_url FROM images i
          WHERE i.item_type='ACCESSORY' AND i.item_id = a.id
          ORDER BY is_primary DESC, display_order ASC, id ASC
          LIMIT 1) AS image_url
FROM accessories a
$whereSql
ORDER BY a.created_at DESC, a.id DESC
LIMIT $limit OFFSET $offset
SQL;
  $accRes = $conn->query($sqlAcc);
} else {
  $accRes = false;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Danh sách phụ kiện - StarryPets</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style> .dropdown-menu>div>a{display:block;padding:6px 10px;color:#333;text-decoration:none} .dropdown-menu>div>a:hover{background:#f2f2f2} .title-row{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap} .filter-bar{display:flex;align-items:center;gap:8px} .filter-bar select{padding:6px 8px;border:1px solid #ddd;border-radius:6px;min-width:140px} .filter-bar .btn{padding:7px 12px} </style>
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
                // Giống chó
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
                    } else { echo '<div><span>Chưa có giống chó</span></div>'; }
                  } else { echo '<div><span>Lỗi tải giống chó</span></div>'; }
                }
                // Giống mèo
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
                    } else { echo '<div><span>Chưa có giống mèo</span></div>'; }
                  } else { echo '<div><span>Lỗi tải giống mèo</span></div>'; }
                }
              ?>
            </ul>
          </li>
          <li class="dropdown active">
            <a href="category.php" class="dropdown-toggle">Phụ kiện <span class="caret" style="font-size:12px">▼</span></a>
            <ul class="dropdown-menu">
              <?php
                // Danh mục phụ kiện (categories.type = 'ACCESSORY')
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
                    } else { echo '<div><span>Chưa có loại phụ kiện</span></div>'; }
                  } else { echo '<div><span>Lỗi tải loại phụ kiện</span></div>'; }
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
          <a href="thanhtoan.php" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>
    </div>

    <!-- Banner Slider Start -->
    <div class="banner-slider">
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

    <!-- Modal Container -->
    <div id="modalContainer"></div>

    <!-- Optional search (hidden in UI) -->
    <!-- <div class="container header-search-bar" style="margin-top: 0;">
      <div class="search-wrap">
        <select class="cat-select"><option>Tất cả danh mục</option></select>
        <input class="search-input" placeholder="Tìm kiếm..." id="searchInput" />
        <button class="btn search-btn" aria-label="search" id="searchBtn">🔍</button>
      </div>
    </div> -->
  </header>

  <main>
    <section id="accessories" class="container products-section" style="padding:40px 0;">
      <div class="title-row">
        <h1 class="section-title" style="margin:0"><?php echo ($categoryId > 0 && $selectedCategoryName) ? ('Phụ kiện loại ' . htmlspecialchars($selectedCategoryName)) : 'Tất cả phụ kiện'; ?></h1>
        <form class="filter-bar" id="accFilterForm" method="get" action="category.php">
          <select id="filter-category" name="category_id">
            <option value="">Lọc theo danh mục</option>
            <?php
              if (isset($conn) && !$conn->connect_error) {
                $accCatsSql = "SELECT id, name FROM categories WHERE type='ACCESSORY' ORDER BY name ASC";
                if ($accCatsRes = $conn->query($accCatsSql)) {
                  while ($ac = $accCatsRes->fetch_assoc()) {
                    $cid = (int)$ac['id'];
                    $sel = ($cid === $categoryId) ? ' selected' : '';
                    echo '<option value="'.$cid.'"'.$sel.'>'.htmlspecialchars($ac['name']).'</option>';
                  }
                }
              }
            ?>
          </select>
          <select id="filter-price" name="price">
            <option value="">Lọc theo giá</option>
            <option value="low" <?php echo ($priceParam==='low')?'selected':''; ?>>Dưới 100k</option>
            <option value="mid" <?php echo ($priceParam==='mid')?'selected':''; ?>>100k – 500k</option>
            <option value="high" <?php echo ($priceParam==='high')?'selected':''; ?>>500k – 1000k</option>
            <option value="over" <?php echo ($priceParam==='over')?'selected':''; ?>>Trên 1000k</option>
          </select>
          <button id="filter-btn" class="btn btn-primary" type="submit">Lọc</button>
          <?php if ($categoryId>0 || $priceParam): ?>
            <a class="btn" href="category.php">Xóa lọc</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="products-grid" id="accGrid">
        <?php
          if ($accRes && $accRes->num_rows > 0) {
            while ($row = $accRes->fetch_assoc()) {
              $img = $row['image_url'] ? ('../' . $row['image_url']) : ('https://placehold.co/600x500?text=' . rawurlencode($row['name']));
              $price = number_format((float)$row['price'], 0, ',', '.') . '₫';
              $dataAttrs = 'data-id="acc-' . (int)$row['id'] . '" ';
              $dataAttrs .= 'data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-price="' . htmlspecialchars($price, ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-image="' . htmlspecialchars($img, ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-brand="' . htmlspecialchars($row['brand'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-material="' . htmlspecialchars($row['material'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-size="' . htmlspecialchars($row['size'] ?: '—', ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-status="Hiển thị" ';
              $dataAttrs .= 'data-description="' . htmlspecialchars($row['description'] ?: 'Chưa có thông tin chi tiết.', ENT_QUOTES) . '"';

              echo '<article class="product-card">';
              echo '  <div class="thumb"><img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($row['name']) . '"></div>';
              echo '  <h3 class="title">' . htmlspecialchars($row['name']) . '</h3>';
              if (!empty($row['brand'])) {
                echo '  <div class="meta">' . htmlspecialchars($row['brand']) . '</div>';
              }
              echo '  <div class="price">' . $price . '</div>';
              echo '  <div class="actions">';
              echo '    <button class="btn add-to-cart" data-id="acc-' . (int)$row['id'] . '" data-stock="' . (int)$row['stock'] . '">Mua hàng</button>';
              echo '    <button class="btn btn-outline view-accessory-btn" ' . $dataAttrs . '>Xem</button>';
              echo '  </div>';
              echo '</article>';
            }
          } else {
            echo '<p>Không có phụ kiện nào.</p>';
          }
        ?>
      </div>
      <div class="pagination" style="margin-top:30px; text-align:center;">
        <?php if ($totalPages > 1): ?>
          <div class="pager" style="display:inline-flex; gap:6px; flex-wrap:wrap;">
            <?php
              $qPrefix = 'category.php?';
              if ($categoryId > 0) { $qPrefix .= 'category_id=' . $categoryId . '&'; }
              if ($priceParam) { $qPrefix .= 'price=' . urlencode($priceParam) . '&'; }
            ?>
            <?php if ($page > 1): ?>
              <a class="btn" href="<?php echo $qPrefix; ?>page=<?php echo $page-1; ?>">« Trước</a>
            <?php endif; ?>
            <?php
              $start = max(1, $page - 3);
              $end = min($totalPages, $page + 3);
              for ($i = $start; $i <= $end; $i++) {
                if ($i == $page) {
                  echo '<span class="btn btn-primary" style="opacity:0.85">' . $i . '</span>';
                } else {
                  echo '<a class="btn" href="' . $qPrefix . 'page=' . $i . '">' . $i . '</a>';
                }
              }
            ?>
            <?php if ($page < $totalPages): ?>
              <a class="btn" href="<?php echo $qPrefix; ?>page=<?php echo $page+1; ?>">Sau »</a>
            <?php endif; ?>
          </div>
          <div style="margin-top:10px; font-size:13px; color:#555;">Trang <?php echo $page; ?> / <?php echo $totalPages; ?> • Tổng phụ kiện: <?php echo $totalAcc; ?></div>
        <?php endif; ?>
      </div>
    </section>
  </main>
<!--
  <section class="instagram-row">
    <div class="container insta-inner">
      <div class="insta-list">
        <img src="https://placehold.co/120x120?text=acc1" alt="accessory">
        <img src="https://placehold.co/120x120?text=acc2" alt="accessory">
        <img src="https://placehold.co/120x120?text=acc3" alt="accessory">
        <img src="https://placehold.co/120x120?text=acc4" alt="accessory">
        <img src="https://placehold.co/120x120?text=acc5" alt="accessory">
        <img src="https://placehold.co/120x120?text=acc6" alt="accessory">
        <img src="https://placehold.co/120x120?text=acc7" alt="accessory">
      </div>
    </div>
  </section>
-->
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
  <script src="../assets/js/accessory-modal.js"></script>
  <script src="../assets/js/accessory-modal-handler.js"></script>
  <script>
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
      function nextSlide() { showSlide((current + 1) % slides.length); }
      function prevSlide() { showSlide((current - 1 + slides.length) % slides.length); }
      if (nextBtn && prevBtn) {
        nextBtn.addEventListener('click', function() { nextSlide(); resetTimer(); });
        prevBtn.addEventListener('click', function() { prevSlide(); resetTimer(); });
      }
      dots.forEach(function(dot, i) { dot.addEventListener('click', function() { showSlide(i); resetTimer(); }); });
      function autoSlide() { timer = setInterval(nextSlide, 4000); }
      function resetTimer() { clearInterval(timer); autoSlide(); }
      showSlide(0); autoSlide();
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






