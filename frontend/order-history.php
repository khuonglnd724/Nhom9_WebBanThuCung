<?php
session_start();
require_once '../connect.php';

// Kiểm tra xem user đã đăng nhập hay không
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

// Lấy lịch sử đơn hàng của user
$conn->set_charset('utf8mb4');
$sqlOrders = "SELECT o.id, o.order_code, o.total_amount, o.status, o.payment_method, o.created_at
              FROM orders o
              WHERE o.user_id = ?
              ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sqlOrders);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Map status
$statusMap = [
    'PENDING' => 'Chờ xác nhận',
    'PAID' => 'Đã thanh toán',
    'SHIPPED' => 'Đang giao',
    'COMPLETED' => 'Đã giao',
    'CANCELED' => 'Đã hủy'
];

// Map payment method
$paymentMap = [
    'COD' => 'Thanh toán khi nhận',
    'BANK' => 'Chuyển khoản ngân hàng',
    'VNPAY' => 'VNPay',
    'MOMO' => 'Momo'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lịch sử mua hàng - StarryPets</title>
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
                        $bid = (int)$br['id'];
                        $bname = htmlspecialchars($br['name']);
                        echo '<div><a href="pet.php?breed_id=' . $bid . '">Chó ' . $bname . '</a></div>';
                      }
                    }
                  }

                  $catSql = "SELECT id, name FROM breeds WHERE pet_type='CAT' ORDER BY name ASC";
                  if ($catRes = $conn->query($catSql)) {
                    if ($catRes->num_rows > 0) {
                      while ($cr = $catRes->fetch_assoc()) {
                        $cid = (int)$cr['id'];
                        $cname = htmlspecialchars($cr['name']);
                        echo '<div><a href="pet.php?breed_id=' . $cid . '">Mèo ' . $cname . '</a></div>';
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
                  $conn->set_charset('utf8mb4');
                  $accCatSql = "SELECT id, name FROM categories WHERE type='ACCESSORY' ORDER BY name ASC";
                  if ($accCatRes = $conn->query($accCatSql)) {
                    if ($accCatRes->num_rows > 0) {
                      while ($ac = $accCatRes->fetch_assoc()) {
                        $aid = (int)$ac['id'];
                        $aname = htmlspecialchars($ac['name']);
                        echo '<div><a href="category.php?category_id=' . $aid . '">Phụ kiện ' . $aname . '</a></div>';
                      }
                    }
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
          <a href="thanhtoan.php" class="btn btn-primary">Thanh toán</a>
        </div>
      </div>
    </div>
  </header>

  <main class="container">
    <h1>📋 Lịch sử mua hàng</h1>

    <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
      <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="cart.php" class="btn" style="background: #ccc; color: #000;">← Quay lại giỏ hàng</a>
        
      </div>
    </div>

    <?php if (count($orders) === 0): ?>
      <div style="background: #fff; padding: 40px; border-radius: 8px; text-align: center;">
        <p style="font-size: 18px; color: #999; margin-bottom: 20px;">Bạn chưa có đơn hàng nào.</p>
        <a href="index.php" class="btn btn-primary">Bắt đầu mua sắm</a>
      </div>
    <?php else: ?>
      <div style="background: #fff; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
              <th style="padding: 15px; text-align: left;">Mã đơn hàng</th>
              <th style="padding: 15px; text-align: left;">Ngày đặt</th>
              <th style="padding: 15px; text-align: left;">Tổng tiền</th>
              <th style="padding: 15px; text-align: left;">Phương thức</th>
              <th style="padding: 15px; text-align: left;">Trạng thái</th>
              <th style="padding: 15px; text-align: center;">Chi tiết</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): 
              $status = $order['status'];
              $statusText = $statusMap[$status] ?? $status;
              $statusColor = 'gray';
              
              if ($status === 'COMPLETED') $statusColor = 'green';
              elseif ($status === 'SHIPPED') $statusColor = '#ff9800';
              elseif ($status === 'PAID') $statusColor = '#2196f3';
              elseif ($status === 'CANCELED') $statusColor = 'red';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 15px; font-weight: 600;"><?php echo htmlspecialchars($order['order_code']); ?></td>
              <td style="padding: 15px;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
              <td style="padding: 15px; color: #e74c3c; font-weight: 600;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>₫</td>
              <td style="padding: 15px;"><?php echo htmlspecialchars($paymentMap[$order['payment_method']] ?? $order['payment_method']); ?></td>
              <td style="padding: 15px;">
                <span style="background: <?php echo $statusColor; ?>; color: white; padding: 5px 12px; border-radius: 20px; display: inline-block; font-size: 12px;">
                  <?php echo $statusText; ?>
                </span>
              </td>
              <td style="padding: 15px; text-align: center;">
                <a href="order-confirmation.php?order_id=<?php echo (int)$order['id']; ?>" class="btn" style="background: #2196f3; color: white; padding: 5px 15px; text-decoration: none; font-size: 12px;">Xem</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>

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

  <script src="../assets/js/cart.js"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>
