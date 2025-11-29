<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Theo dõi đơn hàng - StarryPets</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="logo" href="index.php">
        <img src="../assets/images/logo1.png" alt="StarryPets Logo" style="height:200px;width:auto;position:relative;top:-50px;left:-50px;">
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
          <li><a href="dichvu.php">Dịch vụ</a></li>
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
  </header>

  <main class="container">
    <h1>Theo dõi đơn hàng</h1>
    
    <div style="background: #fff; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
      <h3>Tìm kiếm đơn hàng</h3>
      <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" id="searchOrderId" placeholder="Nhập mã đơn hàng (vd: ORD1234567890)" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        <button onclick="searchOrder()" class="btn btn-primary">Tìm kiếm</button>
        <button onclick="showAllOrders()" class="btn">Xem tất cả</button>
      </div>
    </div>

    <div id="trackingContainer">
      <p style="text-align: center; color: #999;">Nhập mã đơn hàng để xem trạng thái</p>
    </div>
  </main>

  <footer style="background: #f0e6ee; padding: 30px 0; margin-top: 50px;">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div>
          <h4>CÔNG TY</h4>
          <ul style="list-style: none; padding: 0;">
            <li><a href="#">Giới thiệu</a></li>
            <li><a href="#">Liên hệ</a></li>
            <li><a href="#">Site Map</a></li>
          </ul>
        </div>
        <div>
          <h4>SẢN PHẨM</h4>
          <ul style="list-style: none; padding: 0;">
            <li><a href="#">Chó</a></li>
            <li><a href="#">Mèo</a></li>
            <li><a href="#">Phụ kiện</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container footer-credits">@2019 - Design by:StaryPets Team</div>
  </footer>

  <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/orders.js"></script>
  <script src="../assets/js/script.js"></script>
  <script src="../assets/js/product-modal.js"></script>
  <script>
    function searchOrder() {
      const orderId = document.getElementById("searchOrderId").value.trim();
      if (!orderId) {
        alert("Vui lòng nhập mã đơn hàng!");
        return;
      }
      
      const orders = getOrders();
      const order = orders.find(o => o.id === orderId);
      
      if (!order) {
        alert("Không tìm thấy đơn hàng: " + orderId);
        return;
      }
      
      displayOrderTracking(order);
    }

    function showAllOrders() {
      const orders = getOrders();
      const container = document.getElementById("trackingContainer");
      
      if (orders.length === 0) {
        container.innerHTML = "<p style='text-align: center; color: #999;'>Bạn chưa có đơn hàng nào.</p>";
        return;
      }

      container.innerHTML = orders.map(order => `
        <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid ${getStatusColorBorder(order.status)};">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
              <h4 style="margin: 0 0 5px;">${order.id}</h4>
              <p style="margin: 0; color: #999; font-size: 14px;">${order.date}</p>
            </div>
            <div style="text-align: right;">
              <p style="margin: 0; font-weight: 600; color: var(--pink2);">${order.total.toLocaleString()}₫</p>
              <span style="background: ${getStatusColor(order.status)}; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px;">${order.status}</span>
            </div>
          </div>
          <button onclick="displayOrderTracking(${JSON.stringify(order).replace(/"/g, '&quot;')})" class="btn btn-primary" style="margin-top: 10px; width: 100%;">Chi tiết</button>
        </div>
      `).join("");
    }

    function displayOrderTracking(order) {
      const container = document.getElementById("trackingContainer");
      const statusSteps = ["Chờ xác nhận", "Đã xác nhận", "Đang giao", "Đã giao"];
      const currentStep = statusSteps.indexOf(order.status);

      container.innerHTML = `
        <div style="background: #fff; padding: 30px; border-radius: 8px;">
          <h3>${order.id}</h3>
          
          <div style="margin: 30px 0;">
            <div style="display: flex; justify-content: space-between;">
              ${statusSteps.map((step, idx) => `
                <div style="flex: 1; text-align: center;">
                  <div style="width: 40px; height: 40px; margin: 0 auto 10px; border-radius: 50%; background: ${idx <= currentStep ? 'var(--pink2)' : '#ddd'}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                    ${idx + 1}
                  </div>
                  <p style="font-size: 12px; color: #666;">${step}</p>
                </div>
              `).join('')}
            </div>
            <div style="display: flex; margin-top: 10px;">
              ${statusSteps.map((step, idx) => `
                <div style="flex: 1; height: 4px; background: ${idx < currentStep ? 'var(--pink2)' : '#ddd'};"></div>
              `).join('')}
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div>
              <h4>Thông tin đơn hàng</h4>
              <p><strong>Khách hàng:</strong> ${order.customer.fullName}</p>
              <p><strong>Email:</strong> ${order.customer.email}</p>
              <p><strong>SĐT:</strong> ${order.customer.phone}</p>
              <p><strong>Địa chỉ:</strong> ${order.customer.address}, ${order.customer.city}</p>
              <p><strong>Phương thức thanh toán:</strong> ${getPaymentMethodLabel(order.payment)}</p>
              ${order.customer.notes ? `<p><strong>Ghi chú:</strong> ${order.customer.notes}</p>` : ''}
            </div>
            <div>
              <h4>Chi tiết giỏ hàng</h4>
              ${order.items.map(item => `
                <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
                  <div style="font-weight: 600;">${item.name}</div>
                  <div style="font-size: 14px; color: #666;">${item.qty} × ${item.price.toLocaleString()}₫</div>
                </div>
              `).join('')}
              <div style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #eee;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--pink2);">
                  <span>Tổng cộng:</span>
                  <span>${order.total.toLocaleString()}₫</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    function getStatusColorBorder(status) {
      const colors = {
        "Chờ xác nhận": "#ff9800",
        "Đã xác nhận": "#2196f3",
        "Đang giao": "#9c27b0",
        "Đã giao": "#4caf50",
        "Hủy": "#f44336"
      };
      return colors[status] || "#999";
    }

    function getStatusColor(status) {
      const colors = {
        "Chờ xác nhận": "#ff9800",
        "Đã xác nhận": "#2196f3",
        "Đang giao": "#9c27b0",
        "Đã giao": "#4caf50",
        "Hủy": "#f44336"
      };
      return colors[status] || "#999";
    }

    function getPaymentMethodLabel(method) {
      const methods = {
        "cod": "Thanh toán khi nhận hàng",
        "transfer": "Chuyển khoản ngân hàng",
        "card": "Thẻ tín dụng/Ghi nợ"
      };
      return methods[method] || method;
    }

    document.addEventListener("DOMContentLoaded", () => {
      showAllOrders();
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






