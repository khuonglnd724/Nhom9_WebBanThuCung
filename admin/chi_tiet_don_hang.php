<?php
// Redirect if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $id = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['order_id']) ? $_GET['order_id'] : '');
    header('Location: index.php?p=chi_tiet_don_hang&id=' . urlencode($id));
    exit;
}

require_once __DIR__ . '/../connect.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0);
if ($order_id <= 0) {
    echo '<p style="color:red">Mã đơn hàng không hợp lệ.</p>';
    echo '<a href="index.php?p=donhang" class="btn-add">← Quay lại danh sách đơn hàng</a>';
    return;
}

// Fetch order header
$sqlOrder = "SELECT o.id, o.order_code, o.user_id, u.full_name, u.email, u.phone,
                    o.recipient_name, o.total_amount, o.status, o.payment_method, o.shipping_address, o.notes, o.created_at
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id = ?";
$stmt = $conn->prepare($sqlOrder);
if (!$stmt) die('Prepare failed: ' . $conn->error);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
    echo '<p style="color:red">Không tìm thấy đơn hàng.</p>';
    echo '<a href="index.php?p=donhang" class="btn-add">← Quay lại danh sách đơn hàng</a>';
    return;
}

// Fetch order items
$sqlItems = "SELECT 
    od.id,
    od.item_type,
    od.item_id,
    od.quantity,
    (
      CASE od.item_type 
        WHEN 'pet' THEN (SELECT p.name FROM pets p WHERE p.id = od.item_id)
        WHEN 'accessory' THEN (SELECT a.name FROM accessories a WHERE a.id = od.item_id)
        ELSE NULL
      END
    ) AS item_name,
    (
      CASE od.item_type 
        WHEN 'pet' THEN (SELECT p.price FROM pets p WHERE p.id = od.item_id)
        WHEN 'accessory' THEN (SELECT a.price FROM accessories a WHERE a.id = od.item_id)
        ELSE 0
      END
    ) AS item_price,
    (
      SELECT i.image_url FROM images i 
      WHERE i.item_type = od.item_type AND i.item_id = od.item_id AND i.is_primary = 1
      ORDER BY i.display_order ASC, i.id ASC
      LIMIT 1
    ) AS image_url
  FROM order_details od
  WHERE od.order_id = ?
  ORDER BY od.id ASC";

$stmt = $conn->prepare($sqlItems);
if (!$stmt) die('Prepare failed: ' . $conn->error);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
$items = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Compute totals
$totalQty = 0; $computedTotal = 0.0;
foreach ($items as $it) {
    $qty = (int)($it['quantity'] ?? 0);
    $price = (float)($it['item_price'] ?? 0);
    $totalQty += $qty;
    $computedTotal += ($price * $qty);
}

// Helper: image path normalization for admin
function admin_image_path($url) {
    if (!$url) return '';
    $url = trim($url);
    if ($url === '') return '';
    if ($url[0] === '/') return '..' . $url; // root-relative to admin
    return '../' . ltrim($url, '/');
}

$statusLabel = [
    'PENDING' => 'Chờ xử lý',
    'PAID' => 'Đã thanh toán',
    'SHIPPED' => 'Đang giao',
    'COMPLETED' => 'Đã hoàn thành',
    'CANCELED' => 'Đã hủy'
];
?>

<h2 class="page-title">Chi Tiết Đơn Hàng</h2>

<div style="display:flex; gap:16px; flex-wrap:wrap; margin-bottom:16px;">
  <a href="index.php?p=donhang" class="btn-add" style="background:#6c757d;">
    <i class="fas fa-arrow-left"></i> Quay lại
  </a>
  <?php if ($order['status'] === 'PENDING'): ?>
    <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=PAID" class="btn-edit" style="background-color:#17a2b8;" onclick="return confirm('Chuyển sang Đã thanh toán?');">
      <i class="fas fa-dollar-sign"></i> Đã thanh toán
    </a>
    <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=CANCELED" class="btn-delete" onclick="return confirm('Hủy đơn hàng?');">
      <i class="fas fa-times"></i> Hủy
    </a>
  <?php elseif ($order['status'] === 'PAID'): ?>
    <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=SHIPPED" class="btn-edit" style="background-color:#ffc107;" onclick="return confirm('Chuyển sang Đang giao?');">
      <i class="fas fa-truck"></i> Giao hàng
    </a>
  <?php elseif ($order['status'] === 'SHIPPED'): ?>
    <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=COMPLETED" class="btn-edit" style="background-color:#28a745;" onclick="return confirm('Đánh dấu Đã hoàn thành?');">
      <i class="fas fa-check"></i> Hoàn thành
    </a>
  <?php endif; ?>
</div>

<!-- Order Header Card -->
<div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff; margin-bottom:20px;">
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px;">
    <div>
      <div style="font-size:12px; color:#666;">Mã đơn</div>
      <div style="font-weight:600; font-size:16px;">#<?php echo htmlspecialchars($order['order_code'] ?: $order['id']); ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:#666;">Khách hàng</div>
      <div><?php echo htmlspecialchars($order['full_name'] ?? ''); ?></div>
      <div style="font-size:12px; color:#666;">Email: <?php echo htmlspecialchars($order['email'] ?? ''); ?></div>
      <div style="font-size:12px; color:#666;">Điện thoại: <?php echo htmlspecialchars($order['phone'] ?? ''); ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:#666;">Người nhận</div>
      <div><?php echo htmlspecialchars($order['recipient_name'] ?? ''); ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:#666;">Trạng thái</div>
      <div><?php echo $statusLabel[$order['status']] ?? $order['status']; ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:#666;">Thanh toán</div>
      <div><?php echo htmlspecialchars($order['payment_method'] ?? ''); ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:#666;">Ngày đặt</div>
      <div><?php echo htmlspecialchars($order['created_at'] ?? ''); ?></div>
    </div>
    <div>
      <div style="font-size:12px; color:#666;">Địa chỉ giao hàng</div>
      <div><?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?></div>
    </div>
  </div>
</div>

<!-- Items Table -->
<table class="pet-table">
  <thead>
    <tr>
      <th>#</th>
      <th>Ảnh</th>
      <th>Sản phẩm</th>
      <th>Loại</th>
      <th>Giá</th>
      <th>Số lượng</th>
      <th>Thành tiền</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($items)): ?>
      <tr><td colspan="7">Đơn hàng chưa có sản phẩm.</td></tr>
    <?php else: ?>
      <?php foreach ($items as $idx => $it): 
        $img = admin_image_path($it['image_url'] ?? '');
        $price = (float)($it['item_price'] ?? 0);
        $qty = (int)($it['quantity'] ?? 0);
        $line = $price * $qty;
      ?>
      <tr>
        <td><?php echo $idx + 1; ?></td>
        <td><?php if ($img): ?><img src="<?php echo htmlspecialchars($img); ?>" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:6px;" onerror="this.style.display='none'" /><?php endif; ?></td>
        <td><?php echo htmlspecialchars($it['item_name'] ?? ''); ?></td>
        <td><?php echo ($it['item_type'] === 'pet') ? 'Thú cưng' : 'Phụ kiện'; ?></td>
        <td><?php echo number_format($price, 0, ',', '.'); ?> đ</td>
        <td><?php echo number_format($qty, 0, ',', '.'); ?></td>
        <td><?php echo number_format($line, 0, ',', '.'); ?> đ</td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<!-- Totals -->
<div style="margin-top: 16px; display:flex; justify-content:flex-end;">
  <div style="border:1px solid #ddd; border-radius:8px; padding:12px 16px; background:#fff; min-width:280px;">
    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
      <span>Tổng số lượng</span>
      <strong><?php echo number_format($totalQty, 0, ',', '.'); ?></strong>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
      <span>Tổng tạm tính</span>
      <strong><?php echo number_format($computedTotal, 0, ',', '.'); ?> đ</strong>
    </div>
    <div style="display:flex; justify-content:space-between;">
      <span>Tổng tiền (theo đơn)</span>
      <strong><?php echo number_format((float)($order['total_amount'] ?? $computedTotal), 0, ',', '.'); ?> đ</strong>
    </div>
  </div>
</div>
