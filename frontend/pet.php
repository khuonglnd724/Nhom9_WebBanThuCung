<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Pagination setup
$limit = 16;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }

// Optional filter by breed
$breedId = isset($_GET['breed_id']) ? (int)$_GET['breed_id'] : 0;
// Optional filter by type (dog/cat) and price range
$typeParam = isset($_GET['type']) ? trim($_GET['type']) : '';
$priceParam = isset($_GET['price']) ? trim($_GET['price']) : '';
$typeUpper = strtoupper($typeParam);
$typeUpper = ($typeUpper === 'DOG' || $typeUpper === 'CAT') ? $typeUpper : '';
$selectedBreedName = null;

require_once("../connect.php");
$totalPets = 0;
$totalPages = 1;
if ($conn && !$conn->connect_error) {
  $conn->set_charset("utf8mb4");
  // Lấy tên giống nếu có
  if ($breedId > 0) {
    $bnRes = $conn->query("SELECT name FROM breeds WHERE id=" . $breedId . " LIMIT 1");
    if ($bnRes && $bnRow = $bnRes->fetch_assoc()) {
      $selectedBreedName = $bnRow['name'];
    }
  }

  // Xây WHERE theo filter
  $conditions = [];
  if ($breedId > 0) { $conditions[] = "p.breed_id = $breedId"; }
  if ($typeUpper !== '') { $conditions[] = "b.pet_type = '" . $conn->real_escape_string($typeUpper) . "'"; }
  if ($priceParam === 'low') {
    $conditions[] = "p.price < 10000000";
  } elseif ($priceParam === 'mid') {
    $conditions[] = "p.price >= 10000000 AND p.price <= 20000000";
  } elseif ($priceParam === 'high') {
    $conditions[] = "p.price > 20000000";
  }
  $whereSql = count($conditions) ? ('WHERE ' . implode(' AND ', $conditions)) : '';

  // Đếm tổng theo filter (nếu có) - cần join breeds nếu lọc theo type
  $countSql = "SELECT COUNT(*) AS cnt FROM pets p LEFT JOIN breeds b ON p.breed_id = b.id $whereSql";
  $countRes = $conn->query($countSql);
  if ($countRes && $countRow = $countRes->fetch_assoc()) {
    $totalPets = (int)$countRow['cnt'];
    $totalPages = max(1, (int)ceil($totalPets / $limit));
    if ($page > $totalPages) { $page = $totalPages; }
  }
  $offset = ($page - 1) * $limit;
  // whereSql đã bao gồm mọi điều kiện
  $sqlPets = <<<SQL
SELECT p.id, p.name, p.price, p.stock, p.status, p.description, p.age_months, p.color, p.size, p.gender, p.created_at,
       b.name AS breed_name,
       (SELECT image_url FROM images i
          WHERE i.item_type='PET' AND i.item_id = p.id
          ORDER BY is_primary DESC, display_order ASC, id ASC
          LIMIT 1) AS image_url
FROM pets p
LEFT JOIN breeds b ON p.breed_id = b.id
$whereSql
ORDER BY p.created_at DESC, p.id DESC
LIMIT $limit OFFSET $offset
SQL;
  $petsRes = $conn->query($sqlPets);
} else {
  $petsRes = false;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Danh sách thú cưng - StarryPets</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    .title-row{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
    .filter-bar{display:flex;align-items:center;gap:8px}
    .filter-bar select{padding:6px 8px;border:1px solid #ddd;border-radius:6px;min-width:140px}
    .filter-bar .btn{padding:7px 12px}
  </style>
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

    <!-- Modal Container -->
    <div id="modalContainer"></div>

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
<!-- IGNORE 
    <div class="container header-search-bar" style="margin-top: 0;">
      <div class="search-wrap">
        <select class="cat-select"><option>Tất cả danh mục</option></select>
        <input class="search-input" placeholder="Tìm kiếm..." id="searchInput" />
        <button class="btn search-btn" aria-label="search" id="searchBtn">🔍</button>
      </div>
    </div>
  </header>

  <main>
    <section class="breadcrumb container">
      <span>Trang chủ</span> <span class="sep">|</span> <span>Danh sách thú cưng</span>
    </section>
-->
    <section id="pets" class="container products-section" style="padding:40px 0;">
      <div class="title-row">
        <h1 class="section-title" style="margin:0"><?php echo ($breedId > 0 && $selectedBreedName) ? ('Thú cưng giống ' . htmlspecialchars($selectedBreedName)) : 'Tất cả thú cưng'; ?></h1>
        <form class="filter-bar" id="petFilterForm" method="get" action="pet.php">
          <?php if ($breedId > 0): ?><input type="hidden" name="breed_id" value="<?php echo (int)$breedId; ?>" /><?php endif; ?>
          <select id="filter-type" name="type">
            <option value="">Lọc theo loại</option>
            <option value="dog" <?php echo ($typeUpper==='DOG')?'selected':''; ?>>Chó</option>
            <option value="cat" <?php echo ($typeUpper==='CAT')?'selected':''; ?>>Mèo</option>
          </select>
          <select id="filter-breed" name="breed_id">
            <option value="">Lọc theo giống</option>
            <?php
              if (isset($conn) && !$conn->connect_error) {
                $breedOptSql = "SELECT id, name FROM breeds" . ($typeUpper? (" WHERE pet_type='".$conn->real_escape_string($typeUpper)."'") : "") . " ORDER BY name ASC";
                if ($breedOptRes = $conn->query($breedOptSql)) {
                  while ($bo = $breedOptRes->fetch_assoc()) {
                    $bid = (int)$bo['id'];
                    $sel = ($bid === $breedId) ? ' selected' : '';
                    echo '<option value="'.$bid.'"'.$sel.'>'.htmlspecialchars($bo['name']).'</option>';
                  }
                }
              }
            ?>
          </select>
          <select id="filter-price" name="price">
            <option value="">Lọc theo giá</option>
            <option value="low" <?php echo ($priceParam==='low')?'selected':''; ?>>Dưới 10 triệu</option>
            <option value="mid" <?php echo ($priceParam==='mid')?'selected':''; ?>>10 – 20 triệu</option>
            <option value="high" <?php echo ($priceParam==='high')?'selected':''; ?>>Trên 20 triệu</option>
          </select>
          <button id="filter-btn" class="btn btn-primary" type="submit">Lọc</button>
          <?php if ($typeUpper || $breedId>0 || $priceParam): ?>
            <a class="btn" href="pet.php">Xóa lọc</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="products-grid" id="petsGrid">
        <?php
          if ($petsRes && $petsRes->num_rows > 0) {
            while ($row = $petsRes->fetch_assoc()) {
              $img = $row['image_url'] ? ('../' . $row['image_url']) : ('https://placehold.co/600x500?text=' . rawurlencode($row['name']));
              $price = number_format((float)$row['price'], 0, ',', '.') . '₫';
              $dataAttrs = 'data-id="pet-' . (int)$row['id'] . '" ';
              $dataAttrs .= 'data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-price="' . htmlspecialchars($price, ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-image="' . htmlspecialchars($img, ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-breed="' . htmlspecialchars($row['breed_name'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-age="' . ($row['age_months'] ? $row['age_months'] . ' tháng' : 'Chưa rõ') . '" ';
              $dataAttrs .= 'data-color="' . htmlspecialchars($row['color'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-size="' . htmlspecialchars($row['size'] ?: 'Chưa rõ', ENT_QUOTES) . '" ';
              $dataAttrs .= 'data-gender="' . ($row['gender'] === 'MALE' ? 'Đực' : ($row['gender'] === 'FEMALE' ? 'Cái' : 'Chưa rõ')) . '" ';
              $statusText = ($row['status'] === 'AVAILABLE' ? 'Còn hàng' : ($row['status'] === 'SOLD' ? 'Đã bán' : 'Ẩn'));
              $dataAttrs .= 'data-status="' . $statusText . '" ';
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
            echo '<p>Không có dữ liệu thú cưng.</p>';
          }
        ?>
      </div>
      <div class="pagination" style="margin-top:30px; text-align:center;">
        <?php if ($totalPages > 1): ?>
          <div class="pager" style="display:inline-flex; gap:6px; flex-wrap:wrap;">
            <?php
              $qPrefix = 'pet.php?';
              if ($typeUpper) { $qPrefix .= 'type=' . strtolower($typeUpper) . '&'; }
              if ($breedId > 0) { $qPrefix .= 'breed_id=' . $breedId . '&'; }
              if ($priceParam) { $qPrefix .= 'price=' . urlencode($priceParam) . '&'; }
            ?>
            <?php if ($page > 1): ?>
              <a class="btn" href="<?php echo $qPrefix; ?>page=<?php echo $page-1; ?>">« Trước</a>
            <?php endif; ?>
            <?php
              // Hiển thị tối đa 7 trang (current +/- 3) cho gọn
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
          <div style="margin-top:10px; font-size:13px; color:#555;">Trang <?php echo $page; ?> / <?php echo $totalPages; ?> • Tổng thú cưng: <?php echo $totalPets; ?></div>
        <?php endif; ?>
      </div>
    </section>
  </main>
<!-- IGNORE
  <section class="instagram-row">
    <div class="container insta-inner">
      <div class="insta-list">
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
-->
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
  <script src="../assets/js/product-modal-handler.js"></script>
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

      // Simple client-side search filter (optional enhancement)
      var searchInput = document.getElementById('searchInput');
      var searchBtn = document.getElementById('searchBtn');
      function doFilter() {
        var term = (searchInput.value || '').toLowerCase();
        document.querySelectorAll('#petsGrid .product-card').forEach(function(card){
          var name = card.querySelector('.title') ? card.querySelector('.title').textContent.toLowerCase() : '';
          if (!term || name.indexOf(term) !== -1) { card.style.display = ''; } else { card.style.display = 'none'; }
        });
      }
      if (searchBtn) searchBtn.addEventListener('click', doFilter);
      if (searchInput) searchInput.addEventListener('keyup', function(e){ if (e.key === 'Enter') doFilter(); });
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
