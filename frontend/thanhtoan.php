<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Yêu cầu đăng nhập để thanh toán
if (!$isLoggedIn) {
    header("Location: login.php?redirect=thanhtoan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán - StarryPets</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body data-user-id="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
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
          <a href="thanhtoan.php" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>
    </div>
  </header>
  <main class="container">
    <h1>Thanh toán & Đặt hàng</h1>
    
    <div class="checkout-wrapper" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
      <!-- Form Thông tin -->
      <div class="checkout-form" style="background: #fff; padding: 30px; border-radius: 8px;">
        <h2>Thông tin đặt hàng</h2>
        <form id="checkoutForm">
          <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Họ và tên *</label>
            <input type="text" id="fullName" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>

          <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email *</label>
            <input type="email" id="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>

          <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Số điện thoại *</label>
            <input type="tel" id="phone" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>

          <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Địa chỉ *</label>
            <input type="text" id="address" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>

          <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Thành phố/Tỉnh *</label>
            <input type="text" id="city" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
          </div>

          <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Ghi chú</label>
            <textarea id="notes" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; height: 100px;"></textarea>
          </div>

          <h3 style="margin-top: 30px; margin-bottom: 15px;">Phương thức thanh toán</h3>
          <div style="margin-bottom: 15px;">
            <input type="radio" id="payment-cod" name="payment" value="cod" checked>
            <label for="payment-cod" style="display: inline; margin-left: 5px;">Thanh toán khi nhận hàng (COD)</label>
          </div>
          <div style="margin-bottom: 15px;">
            <input type="radio" id="payment-transfer" name="payment" value="transfer">
            <label for="payment-transfer" style="display: inline; margin-left: 5px;">Chuyển khoản ngân hàng</label>
          </div>
          <div style="margin-bottom: 15px;">
            <input type="radio" id="payment-card" name="payment" value="card">
            <label for="payment-card" style="display: inline; margin-left: 5px;">Thẻ tín dụng/Ghi nợ</label>
          </div>
        </form>
      </div>

      <!-- Tóm tắt đơn hàng -->
      <div class="checkout-summary" style="background: #f9f9f9; padding: 30px; border-radius: 8px; height: fit-content;">
        <h3>Tóm tắt đơn hàng</h3>
        <div id="orderSummary" style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
          <!-- Các sản phẩm được thêm bởi JavaScript -->
        </div>
        
        <div style="border-top: 1px solid #ddd; padding-top: 15px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Tạm tính:</span>
            <span id="subtotal">0₫</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Phí vận chuyển:</span>
            <span id="shipping">30.000₫</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 18px; color: var(--pink2);">
            <span>Tổng:</span>
            <span id="orderTotal">0₫</span>
          </div>
        </div>

        <button type="button" onclick="placeOrder()" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 12px; font-size: 16px;">
          ĐẶT HÀNG
        </button>
        <a href="cart.php" class="btn" style="width: 100%; margin-top: 10px; padding: 12px; text-align: center; background: #ccc; color: #000;">
          Quay lại giỏ hàng
        </a>
      </div>
    </div>

    <!-- Banner Slider Start -->
    <div class="banner-slider" style="margin-top: 50px;">
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
    <script>
      // Hiển thị tóm tắt đơn hàng
      function renderCheckoutSummary() {
        const userId = document.body.getAttribute('data-user-id');
        const cartKey = userId ? `cart_user_${userId}` : 'cart_guest';
        const cart = JSON.parse(localStorage.getItem(cartKey)) || [];
        const orderSummary = document.getElementById("orderSummary");
        const subtotal = document.getElementById("subtotal");
        const orderTotal = document.getElementById("orderTotal");
        const shippingFee = 30000;

        if (cart.length === 0) {
          orderSummary.innerHTML = "<p style='color: #f00;'>Giỏ hàng trống. Vui lòng <a href='cart.php'>quay lại giỏ hàng</a>.</p>";
          subtotal.textContent = "0₫";
          orderTotal.textContent = shippingFee.toLocaleString() + "₫";
          return;
        }

        orderSummary.innerHTML = cart.map(item => `
          <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <strong>${item.name}</strong>
              <span>${item.qty} × ${item.price.toLocaleString()}₫</span>
            </div>
            <div style="text-align: right; color: var(--pink2); font-weight: 600;">
              ${(item.qty * item.price).toLocaleString()}₫
            </div>
          </div>
        `).join("");

        const total = cart.reduce((sum, item) => sum + item.qty * item.price, 0);
        subtotal.textContent = total.toLocaleString() + "₫";
        orderTotal.textContent = (total + shippingFee).toLocaleString() + "₫";
      }

      // Đặt hàng
      async function placeOrder() {
        const userId = document.body.getAttribute('data-user-id');
        const cartKey = userId ? `cart_user_${userId}` : 'cart_guest';
        const cart = JSON.parse(localStorage.getItem(cartKey)) || [];
        
        if (cart.length === 0) {
          alert("Giỏ hàng trống!");
          return;
        }

        const fullName = document.getElementById("fullName").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const address = document.getElementById("address").value.trim();
        const city = document.getElementById("city").value.trim();
        const notes = document.getElementById("notes").value.trim();
        const payment = document.querySelector('input[name="payment"]:checked').value;

        if (!fullName || !email || !phone || !address || !city) {
          alert("Vui lòng nhập đầy đủ thông tin bắt buộc!");
          return;
        }

        // Chuẩn bị dữ liệu gửi lên server
        const orderData = {
          fullName: fullName,
          email: email,
          phone: phone,
          address: address,
          city: city,
          notes: notes,
          payment: payment,
          items: cart
        };

        try {
          // Gửi request lên server
          console.log('Sending order data:', orderData);
          
          const response = await fetch('process_order.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
          });

          console.log('Response status:', response.status);
          console.log('Response headers:', response.headers);

          // Đọc response dưới dạng text trước để debug
          const responseText = await response.text();
          console.log('Raw response:', responseText);

          let result;
          try {
            result = JSON.parse(responseText);
          } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response was:', responseText);
            alert("❌ Server trả về lỗi: " + responseText.substring(0, 500));
            return;
          }

          console.log('Response data:', result);

          if (result.success) {
            // Lưu đơn hàng vào localStorage để theo dõi
            const ordersKey = `orders_user_${userId}`;
            let orders = JSON.parse(localStorage.getItem(ordersKey)) || [];
            orders.push({
              id: result.order_code,
              order_id: result.order_id,
              date: new Date().toLocaleString('vi-VN'),
              customer: { fullName, email, phone, address, city, notes },
              items: cart,
              total: cart.reduce((sum, item) => sum + item.qty * item.price, 0) + 30000,
              payment: payment,
              status: "Chờ xác nhận"
            });
            localStorage.setItem(ordersKey, JSON.stringify(orders));

            // Xóa giỏ hàng của user
            localStorage.removeItem(cartKey);

            alert(`✅ Đặt hàng thành công!\\nMã đơn hàng: ${result.order_code}\\n\\nCảm ơn bạn đã mua sắm tại StarryPets!`);
            
            // Quay lại trang chủ
            setTimeout(() => {
              window.location.href = "index.php";
            }, 1000);
          } else {
            alert("❌ Đặt hàng thất bại: " + result.message);
          }
        } catch (error) {
          console.error('Error:', error);
          console.error('Error details:', error.message, error.stack);
          alert("❌ Có lỗi xảy ra khi đặt hàng. Vui lòng kiểm tra console để xem chi tiết lỗi!");
        }
      }

      // Hiển thị khi tải trang
      document.addEventListener("DOMContentLoaded", () => {
        renderCheckoutSummary();
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






