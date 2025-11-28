<?php
require_once __DIR__ . '/auth.php';
// Redirect if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
    header('Location: index.php?p=order_history&user_id=' . urlencode($user_id));
    exit;
}

require_once __DIR__ . '/../connect.php';

// Get user_id from URL
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    echo '<p style="color:red">ID người dùng không hợp lệ.</p>';
    echo '<a href="index.php?p=user" class="btn-add">← Quay lại danh sách người dùng</a>';
    return;
}

// Fetch user info from DB
$stmt = $conn->prepare('SELECT id, full_name, email, phone FROM users WHERE id = ?');
if (!$stmt) die('Prepare failed: ' . $conn->error);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_info = $res->fetch_assoc();
$stmt->close();

if (!$user_info) {
    echo '<p style="color:red">Không tìm thấy người dùng.</p>';
    echo '<a href="index.php?p=user" class="btn-add">← Quay lại danh sách người dùng</a>';
    return;
}

// Fetch orders with item names (first item per order)
$sql = "SELECT 
    o.id,
    o.order_code,
    o.total_amount,
    o.status,
    o.payment_method,
    o.created_at,
    (
      SELECT 
        CASE od.item_type 
          WHEN 'pet' THEN (SELECT p.name FROM pets p WHERE p.id = od.item_id)
          WHEN 'accessory' THEN (SELECT a.name FROM accessories a WHERE a.id = od.item_id)
          ELSE NULL
        END
      FROM order_details od 
      WHERE od.order_id = o.id 
      ORDER BY od.id ASC 
      LIMIT 1
    ) AS item_name
  FROM orders o
  WHERE o.user_id = ?
  ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) die('Prepare failed: ' . $conn->error);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$status_labels = [
    'PENDING' => 'Chờ xử lý',
    'CONFIRMED' => 'Đã xác nhận',
    'SHIPPED' => 'Đang giao',
    'COMPLETED' => 'Đã hoàn thành',
    'CANCELED' => 'Đã hủy'
];

$payment_labels = [
    'COD' => 'Tiền mặt',
    'BANK' => 'Chuyển khoản',
    'VNPAY' => 'VNPay',
    'MOMO' => 'MoMo'
];
?>

<h2 class="page-title">Lịch Sử Mua Hàng</h2>

<div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
    <p style="margin: 5px 0;"><strong>Khách hàng:</strong> <?php echo htmlspecialchars($user_info['full_name']); ?></p>
    <p style="margin: 5px 0;"><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
    <p style="margin: 5px 0;"><strong>Điện thoại:</strong> <?php echo htmlspecialchars($user_info['phone'] ?? ''); ?></p>
    <p style="margin: 5px 0;"><strong>ID:</strong> <?php echo htmlspecialchars($user_info['id']); ?></p>
</div>

<div class="top-bar" style="justify-content: flex-start;">
    <a href="index.php?p=donhang" class="btn-add" style="background-color: #6c757d;">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<table class="pet-table">
    <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Tên đơn (mặt hàng)</th>
            <th>Tổng tiền</th>
            <th>Phương thức</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    
    <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="7">Người dùng này chưa có đơn hàng nào.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($order['order_code']); ?></strong></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($order['item_name'] ?? ''); ?></td>
                    <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</td>
                    <td><?php echo isset($payment_labels[$order['payment_method']]) ? $payment_labels[$order['payment_method']] : $order['payment_method']; ?></td>
                    <td>
                        <?php 
                        $status = $order['status'];
                        $label = $status_labels[$status] ?? $status;
                        $class = '';
                        if ($status === 'COMPLETED') $class = 'status-active';
                        elseif ($status === 'CANCELED') $class = 'status-inactive';
                        ?>
                        <span class="<?php echo $class; ?>"><?php echo htmlspecialchars($label); ?></span>
                    </td>
                    <td>
                        <?php if ($order['status'] === 'PENDING'): ?>
                            <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=CONFIRMED&user_id=<?php echo urlencode($user_id); ?>" class="btn-edit" style="background-color:#17a2b8; font-size:12px; padding:4px 8px;" onclick="return confirm('Chuyển sang Đã xác nhận?');">
                                <i class="fas fa-check"></i> Đã xác nhận
                            </a>
                            <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=CANCELED&user_id=<?php echo urlencode($user_id); ?>" class="btn-delete" style="font-size:12px; padding:4px 8px;" onclick="return confirm('Hủy đơn hàng?');">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        <?php elseif ($order['status'] === 'CONFIRMED'): ?>
                            <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=SHIPPED&user_id=<?php echo urlencode($user_id); ?>" class="btn-edit" style="background-color:#ffc107; font-size:12px; padding:4px 8px;" onclick="return confirm('Chuyển sang Đang giao?');">
                                <i class="fas fa-truck"></i> Giao hàng
                            </a>
                        <?php elseif ($order['status'] === 'SHIPPED'): ?>
                            <a href="capnhat_trangthai_donhang.php?id=<?php echo urlencode($order['id']); ?>&status=COMPLETED&user_id=<?php echo urlencode($user_id); ?>" class="btn-edit" style="background-color:#28a745; font-size:12px; padding:4px 8px;" onclick="return confirm('Đánh dấu Đã hoàn thành?');">
                                <i class="fas fa-check"></i> Hoàn thành
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 6px;">
    <p style="margin: 0;"><strong>Tổng số đơn hàng:</strong> <?php echo count($orders); ?></p>
    <p style="margin: 5px 0 0 0;"><strong>Tổng giá trị:</strong> 
        <?php 
        $total = array_sum(array_column($orders, 'total_amount'));
        echo number_format($total, 0, ',', '.'); 
        ?> đ
    </p>
</div>
