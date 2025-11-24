<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Chi tiết thú cưng - StarryPets</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

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
              <li><a href="pet.php?id=1">Chó Alaska Malamute</a></li>
              <li><a href="pet.php?id=2">Chó Beagle</a></li>
              <li><a href="pet.php?id=3">Chó Corgi</a></li>
              <li><a href="pet.php?id=4">Chó Golden Retriever</a></li>
              <li><a href="pet.php?id=5">Chó Husky Siberian</a></li>
              <li><a href="pet.php?id=6">Chó Phốc Sóc – Pomeranian</a></li>
              <li><a href="pet.php?id=7">Chó Poodle</a></li>
              <li><a href="pet.php?id=8">Chó Pug</a></li>
              <li><a href="pet.php?id=9">Chó Samoyed</a></li>
              <li><a href="pet.php?id=10">Mèo Anh (Dài + Ngắn)</a></li>
              <li><a href="pet.php?id=11">Mèo Chân Ngắn</a></li>
              <li><a href="pet.php?id=12">Mèo Tai Cụp</a></li>
            </ul>
          </li>
          <li><a href="category.php">Phụ kiện</a></li>
          <li><a href="dichvu.php">Dịch vụ</a></li>
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
    <!-- Breadcrumb -->
    <section class="breadcrumb container">
      <span>Trang chủ</span> <span class="sep">|</span> <span id="petBreadcrumb">Chi tiết thú cưng</span>
    </section>

    <!-- Content Section -->
    <section class="container" style="margin: 40px 0; padding: 40px 0;">
      <!-- ĐỂ LOAD DỮ LIỆU TỪ DATABASE -->
      <!-- Cấu trúc dữ liệu cần thiết từ DB:
        - id: ID thú cưng
        - name: Tên thú cưng (ví dụ: "Chó Alaska Malamute")
        - breed: Giống (ví dụ: "Alaska Malamute")
        - description: Mô tả chi tiết
        - price: Giá
        - image: URL ảnh
        - age: Tuổi
        - weight: Cân nặng
        - color: Màu sắc
        - status: Trạng thái (Sẵn có, Cọc, ...)
      -->
      
      <h1 id="petName" style="font-size: 32px; margin-bottom: 20px; color: #333;">Chi tiết thú cưng</h1>
      <p id="petDescription" style="font-size: 16px; line-height: 1.8; color: #666;">
        Dữ liệu sẽ được tải từ Database...
      </p>

      <!-- Placeholder để hiển thị dữ liệu từ DB -->
      <div id="petDetails" style="margin-top: 40px; padding: 30px; background: #f9f9f9; border-radius: 8px;">
        <p style="color: #999; text-align: center;">Chưa chọn thú cưng. Hãy chọn từ menu để xem chi tiết.</p>
      </div>
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

      // Load pet data from DB based on ID parameter
      const urlParams = new URLSearchParams(window.location.search);
      const petId = urlParams.get('id');
      
      if (petId) {
        // TODO: Fetch dữ liệu từ API/DB
        // Ví dụ: fetch(`/api/pets/${petId}`)
        //   .then(res => res.json())
        //   .then(pet => {
        //     document.getElementById('petName').textContent = pet.name;
        //     document.getElementById('petDescription').textContent = pet.description;
        //     document.getElementById('petBreadcrumb').textContent = pet.breed;
        //     document.getElementById('petDetails').innerHTML = `
        //       <h3>${pet.name}</h3>
        //       <p>Giá: ${pet.price}</p>
        //       <p>Tuổi: ${pet.age}</p>
        //       <p>Cân nặng: ${pet.weight}</p>
        //       <p>Màu sắc: ${pet.color}</p>
        //       <p>Trạng thái: ${pet.status}</p>
        //     `;
        //   });
      }
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
