<?php
session_start();
require_once '../connect.php';

// Kiểm tra xem user đã đăng nhập hay không
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Lấy order_id từ URL hoặc session
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 0);

if ($orderId <= 0) {
    echo '<p style="color:red; text-align:center; padding:20px;">Không tìm thấy thông tin đơn hàng. Vui lòng thử lại.</p>';
    echo '<a href="index.php" class="btn" style="display:inline-block; text-align:center; margin-left:20px;">← Quay lại trang chủ</a>';
    exit;
}

// Lấy thông tin đơn hàng
$conn->set_charset('utf8mb4');
$sqlOrder = "SELECT o.id, o.order_code, o.user_id, 
                    o.recipient_name, u.email, o.phone,
                    o.total_amount, o.status, o.payment_method, 
                    o.shipping_address, o.notes, o.created_at
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.id = ?";

$stmt = $conn->prepare($sqlOrder);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $orderId);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
    echo '<p style="color:red; text-align:center; padding:20px;">Không tìm thấy đơn hàng này.</p>';
    exit;
}

// Lấy chi tiết đơn hàng
$sqlItems = "SELECT 
    od.id,
    od.item_type,
    od.item_id,
    od.quantity,
    od.unit_price,
    (
      CASE od.item_type 
        WHEN 'PET' THEN (SELECT p.name FROM pets p WHERE p.id = od.item_id)
        WHEN 'ACCESSORY' THEN (SELECT a.name FROM accessories a WHERE a.id = od.item_id)
        ELSE NULL
      END
    ) AS item_name,
    (
      SELECT i.image_url FROM images i 
      WHERE (i.item_type = od.item_type OR (od.item_type = 'PET' AND i.item_type = 'pet') OR (od.item_type = 'ACCESSORY' AND i.item_type = 'accessory'))
        AND i.item_id = od.item_id AND i.is_primary = 1
      ORDER BY i.display_order ASC, i.id ASC
      LIMIT 1
    ) AS image_url
  FROM order_details od
  WHERE od.order_id = ?
  ORDER BY od.id ASC";

$stmt = $conn->prepare($sqlItems);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $orderId);
$stmt->execute();
$res = $stmt->get_result();
$items = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Tính tổng
$totalQty = 0;
$computedTotal = 0.0;
foreach ($items as $it) {
    $qty = (int)($it['quantity'] ?? 0);
    $price = (float)($it['unit_price'] ?? 0);
    $totalQty += $qty;
    $computedTotal += ($price * $qty);
}

// Định dạng trạng thái đơn hàng
$statusMap = [
    'PENDING' => ['label' => 'Chờ xác nhận', 'color' => '#FF9800'],
    'CONFIRMED' => ['label' => 'Đã xác nhận', 'color' => '#2196F3'],
    'SHIPPED' => ['label' => 'Đang giao', 'color' => '#9C27B0'],
    'COMPLETED' => ['label' => 'Đã giao', 'color' => '#4CAF50'],
    'CANCELED' => ['label' => 'Đã hủy', 'color' => '#F44336']
];

$currentStatus = $statusMap[$order['status']] ?? ['label' => $order['status'], 'color' => '#999'];

// Định dạng phương thức thanh toán
$paymentMap = [
    'COD' => 'Thanh toán khi nhận hàng (COD)',
    'BANK' => 'Chuyển khoản ngân hàng',
    'CREDIT' => 'Thẻ tín dụng/Ghi nợ',
    'MOMO' => 'Ví điện tử MOMO'
];
$paymentMethod = $paymentMap[$order['payment_method']] ?? $order['payment_method'];
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Xác nhận đơn hàng — StarryPets</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    .confirmation-container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .confirmation-header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 2px solid #f2a7c7;
    }
    .confirmation-header h1 {
      color: #d36b9f;
      margin-bottom: 10px;
    }
    .status-badge {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 20px;
      color: #fff;
      font-weight: bold;
    }
    .order-code {
      font-size: 18px;
      color: #666;
      margin: 10px 0;
    }
    .order-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin: 30px 0;
      padding: 20px;
      background: #f9f9f9;
      border-radius: 8px;
    }
    .info-group {
      margin-bottom: 15px;
    }
    .info-group label {
      display: block;
      color: #d36b9f;
      font-weight: bold;
      margin-bottom: 5px;
      font-size: 12px;
      text-transform: uppercase;
    }
    .info-group p {
      margin: 0;
      color: #333;
      line-height: 1.6;
    }
    .order-items {
      margin: 30px 0;
    }
    .order-items h2 {
      color: #d36b9f;
      margin-bottom: 15px;
      border-bottom: 2px solid #f2a7c7;
      padding-bottom: 10px;
    }
    .item-card {
      display: flex;
      gap: 15px;
      padding: 15px;
      margin-bottom: 10px;
      background: #f9f9f9;
      border-radius: 8px;
      border-left: 4px solid #d36b9f;
    }
    .item-image {
      width: 80px;
      height: 80px;
      border-radius: 6px;
      overflow: hidden;
      flex-shrink: 0;
    }
    .item-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .item-details {
      flex: 1;
    }
    .item-name {
      font-weight: bold;
      color: #222;
      margin-bottom: 5px;
    }
    .item-qty {
      color: #666;
      font-size: 14px;
      margin-bottom: 5px;
    }
    .item-price {
      color: #d36b9f;
      font-weight: bold;
      font-size: 16px;
    }
    .order-summary {
      background: linear-gradient(90deg, #f2a7c7 0%, #fdd7e6 100%);
      padding: 20px;
      border-radius: 8px;
      margin: 30px 0;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      color: #333;
    }
    .summary-row.total {
      font-size: 18px;
      font-weight: bold;
      color: #d36b9f;
      border-top: 2px solid rgba(0,0,0,0.1);
      padding-top: 10px;
    }
    .actions {
      text-align: center;
      margin: 30px 0;
      display: flex;
      gap: 10px;
      justify-content: center;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
    }
    .btn-primary {
      background: #d36b9f;
      color: #fff;
    }
    .btn-primary:hover {
      background: #b8508a;
      box-shadow: 0 4px 12px rgba(211,107,159,0.3);
    }
    .btn-secondary {
      background: #fff;
      color: #d36b9f;
      border: 2px solid #d36b9f;
    }
    .btn-secondary:hover {
      background: #f2a7c7;
      color: #fff;
    }
    .notes-section {
      background: #f0f0f0;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
    }
    .notes-section h3 {
      color: #d36b9f;
      margin: 0 0 10px 0;
    }
    .notes-section p {
      margin: 0;
      color: #666;
      line-height: 1.6;
    }
  </style>
</head>
<body>
  <div class="confirmation-container">
    <div class="confirmation-header">
      <h1>Chi tiết đơn hàng</h1>
      <p>Cảm ơn bạn đã đặt hàng tại StarryPets</p>
      <p class="order-code">Mã đơn hàng: <strong><?php echo htmlspecialchars($order['order_code']); ?></strong></p>
      <span class="status-badge" style="background: <?php echo $currentStatus['color']; ?>;">
        <?php echo $currentStatus['label']; ?>
      </span>
    </div>

    <!-- Thông tin đơn hàng -->
    <div class="order-info">
      <div>
        <div class="info-group">
          <label>Họ tên khách hàng</label>
          <p><?php echo htmlspecialchars($order['recipient_name'] ?? 'Không có'); ?></p>
        </div>
        <div class="info-group">
          <label>Email</label>
          <p><?php echo htmlspecialchars($order['email'] ?? 'Không có'); ?></p>
        </div>
        <div class="info-group">
          <label>Số điện thoại</label>
          <p><?php echo htmlspecialchars($order['phone'] ?? 'Không có'); ?></p>
        </div>
      </div>
      <div>
        <div class="info-group">
          <label>Địa chỉ giao hàng</label>
          <p><?php echo htmlspecialchars($order['shipping_address'] ?? 'Không có'); ?></p>
        </div>
        <div class="info-group">
          <label>Phương thức thanh toán</label>
          <p><?php echo htmlspecialchars($paymentMethod); ?></p>
        </div>
        <div class="info-group">
          <label>Ngày đặt hàng</label>
          <p><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
        </div>
      </div>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div class="order-items">
      <h2>Chi tiết sản phẩm</h2>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): 
          $imageUrl = $item['image_url'] ? ('../' . $item['image_url']) : 'https://placehold.co/80x80?text=' . rawurlencode($item['item_name'] ?? 'Sản phẩm');
          $itemTotal = (float)($item['unit_price'] ?? 0) * (int)($item['quantity'] ?? 0);
        ?>
        <div class="item-card">
          <div class="item-image">
            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
          </div>
          <div class="item-details">
            <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
            <div class="item-qty">Số lượng: <?php echo (int)$item['quantity']; ?></div>
            <div class="item-price">
              <?php echo number_format((float)($item['unit_price'] ?? 0), 0, ',', '.'); ?>₫ 
              × <?php echo (int)$item['quantity']; ?> = 
              <strong><?php echo number_format($itemTotal, 0, ',', '.'); ?>₫</strong>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Không có sản phẩm nào trong đơn hàng này.</p>
      <?php endif; ?>
    </div>

    <!-- Tóm tắt đơn hàng -->
    <div class="order-summary">
      <div class="summary-row">
        <span>Tổng số lượng:</span>
        <span><?php echo $totalQty; ?> sản phẩm</span>
      </div>
      <div class="summary-row">
        <span>Tổng tiền:</span>
        <span><?php echo number_format((float)($order['total_amount'] ?? $computedTotal), 0, ',', '.'); ?>₫</span>
      </div>
      <div class="summary-row total">
        <span>Thành tiền:</span>
        <span><?php echo number_format((float)($order['total_amount'] ?? $computedTotal), 0, ',', '.'); ?>₫</span>
      </div>
    </div>

    <!-- Ghi chú -->
    <?php if (!empty($order['notes'])): ?>
    <div class="notes-section">
      <h3>Ghi chú của bạn</h3>
      <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
    </div>
    <?php endif; ?>

    <!-- Các nút hành động -->
    <div class="actions">
      <a href="index.php" class="btn btn-primary">← Tiếp tục mua sắm</a>
      
    </div>
  </div>
</body>
</html>
