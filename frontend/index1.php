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
<body>

  <header class="site-header">
    <div class="container header-inner">
      <a class="logo" href="index.php">
        <img src="../assets/images/logo.png" alt="StarryPets Logo" style="height:100px;width:auto;">
      </a>
      <nav class="main-nav" id="mainNav">
        <ul class="menu">
          <li class="active"><a href="index.php">Trang chủ</a></li>
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
          <li><a href="#">Dịch vụ</a></li>
          <li><a href="#">Giới thiệu</a></li>
          <li><a href="#">Liên hệ</a></li>
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
      <a href="../frontend/login.php" class="btn-login">Đăng nhập</a>
      <a href="../frontend/register.php" class="btn-register">Đăng ký</a>
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

    <!-- Search & Filter moved below banner -->
    <div class="container header-search-bar" style="margin-top: 0;">
      <div class="search-wrap">
        <select class="cat-select"><option>Tất cả danh mục</option></select>
        <input class="search-input" placeholder="Tìm kiếm..." />
        <button class="btn search-btn" aria-label="search">🔍</button>
      </div>
    </div>
    </header>

  <main>
    <!-- Hero (dùng ảnh bạn upload làm preview) -->
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

    <!-- Breadcrumb -->
    <section class="breadcrumb container">
      <span>Trang chủ</span> <span class="sep">|</span> <span>Danh mục sản phẩm</span>
    </section>

    <!-- New products (populated from site content) -->
    <section id="products" class="container products-section">
      <h2 class="section-title">SẢN PHẨM MỚI</h2>

      <!-- Chỗ code lọc sản phẩm -->
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
      <div class="products-grid">
        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=GOLDEN+ĐẸP+TRAI" alt="GOLDEN ĐẸP TRAI"></div>
          <h3 class="title">GOLDEN ĐẸP TRAI</h3>
          <div class="price">15.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="golden-dep-trai">Mua hàng</button>
            <button class="btn btn-outline view-product-btn" data-id="golden-dep-trai" data-name="GOLDEN ĐẸP TRAI" data-price="15.000.000₫" data-image="https://placehold.co/600x500?text=GOLDEN+ĐẸP+TRAI">Xem</button>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=SAMOYED+XINH" alt="SAMOYED XINH"></div>
          <h3 class="title">SAMOYED XINH</h3>
          <div class="price">14.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="samoyed-xinh">Mua hàng</button>
            <button class="btn btn-outline view-product-btn" data-id="samoyed-xinh" data-name="SAMOYED XINH" data-price="14.000.000₫" data-image="https://placehold.co/600x500?text=SAMOYED+XINH">Xem</button>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=ALASKA+XAM+CUNG" alt="ALASKA XÁM CƯNG"></div>
          <h3 class="title">ALASKA XÁM CƯNG</h3>
          <div class="price">24.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="alaska-xam-cung">Mua hàng</button>
            <button class="btn btn-outline view-product-btn" data-id="alaska-xam-cung" data-name="ALASKA XÁM CƯNG" data-price="24.000.000₫" data-image="https://placehold.co/600x500?text=ALASKA+XAM+CUNG">Xem</button>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=BAC+KINH+SIEU+BEO" alt="BẮC KINH SIÊU BÉO"></div>
          <h3 class="title">BẮC KINH SIÊU BÉO</h3>
          <div class="meta">ID: WEBSITE1758107906</div>
          <div class="price">7.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="bac-kinh-sieu-beo">Mua hàng</button>
            <button class="btn btn-outline view-product-btn" data-id="bac-kinh-sieu-beo" data-name="BẮC KINH SIÊU BÉO" data-price="7.000.000₫" data-image="https://placehold.co/600x500?text=BAC+KINH+SIEU+BEO">Xem</button>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=BICHON+TRANG" alt="BICHON TRẮNG XINH XINH"></div>
          <h3 class="title">BICHON TRẮNG XINH XINH</h3>
          <div class="price">30.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="bichon-trang">Mua hàng</button>
            <button class="btn btn-outline view-product-btn" data-id="bichon-trang" data-name="BICHON TRẮNG XINH XINH" data-price="30.000.000₫" data-image="https://placehold.co/600x500?text=BICHON+TRANG">Xem</button>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=PHOC+SOC" alt="PHỐC SÓC BÉ XÍU CƯNG XĨU"></div>
          <h3 class="title">PHỐC SÓC BÉ XÍU CƯNG XĨU</h3>
          <div class="price">20.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="phoc-soc">Mua hàng</button>
            <a class="btn btn-outline" href="product.php">Xem</a>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=SAMOYED+TRANG" alt="SAMOYED TRẮNG TINH XINH YÊU"></div>
          <h3 class="title">SAMOYED TRẮNG TINH XINH YÊU</h3>
          <div class="price">16.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="samoyed-trang">Mua hàng</button>
            <a class="btn btn-outline" href="product.php">Xem</a>
          </div>
        </article>

        <article class="product-card">
          <div class="thumb"><img src="https://placehold.co/600x500?text=MEO+GOLDEN+LUN" alt="MÈO GOLDEN LÙN"></div>
          <h3 class="title">MÈO GOLDEN LÙN</h3>
          <div class="price">21.000.000₫</div>
          <div class="actions">
            <button class="btn add-to-cart" data-id="meo-golden-lun">Mua hàng</button>
            <a class="btn btn-outline" href="product.php">Xem</a>
          </div>
        </article>

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