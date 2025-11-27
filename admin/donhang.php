<?php
// Redirect if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        header('Location: index.php?p=donhang');
        exit;
}

require_once __DIR__ . '/../connect.php';

// Filters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

/*
 Build order list with:
 - orders fields
 - user full_name via users
 - primary item name via order_details (first row by id asc) joining pets or accessories depending on item_type
*/

$sql = "SELECT 
        o.id,
        o.user_id,
        u.full_name AS user_name,
        o.order_code,
        o.recipient_name,
        o.total_amount,
        o.status,
        o.payment_method,
        o.shipping_address,
        o.phone,
        o.notes,
        o.created_at,
        -- Resolve one item name for quick display
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
    LEFT JOIN users u ON u.id = o.user_id
    WHERE 1=1";

$params = [];
$types = '';

if ($q !== '') {
        $sql .= " AND (o.order_code LIKE ? OR u.full_name LIKE ? OR o.phone LIKE ?)";
        $like = '%' . $q . '%';
        $params[] = $like; $params[] = $like; $params[] = $like;
        $types .= 'sss';
}

if ($status !== 'all') {
        $sql .= " AND o.status = ?";
        $params[] = $status;
        $types .= 's';
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
}
if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h2 class="page-title">Danh Sách Đơn Hàng</h2>

<?php
// Management summary: total orders and revenue (sum of total_amount)
$totalOrders = count($orders);
$totalRevenue = 0;
foreach ($orders as $o) {
        $totalRevenue += (float)($o['total_amount'] ?? 0);
}
?>

<div class="summary-cards" style="display:flex; gap:16px; margin:12px 0 20px; flex-wrap:wrap;">
    <div class="summary-card" style="border:1px solid #ddd; border-radius:8px; padding:12px 16px; min-width:220px; background:#fff;">
        <div style="font-size:12px; color:#666;">Tổng đơn hàng</div>
        <div style="font-size:22px; font-weight:600; margin-top:4px;"><?php echo number_format($totalOrders, 0, ',', '.'); ?></div>
    </div>
    <div class="summary-card" style="border:1px solid #ddd; border-radius:8px; padding:12px 16px; min-width:220px; background:#fff;">
        <div style="font-size:12px; color:#666;">Doanh thu</div>
        <div style="font-size:22px; font-weight:600; margin-top:4px;"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</div>
    </div>
</div>

<div class="top-bar">
    <form method="get" action="index.php" class="search-filter-form">
        <input type="hidden" name="p" value="donhang">
        <div class="search-controls">
            <div class="input-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Tìm theo mã, tên KH hoặc số điện thoại" class="search-input" />
            </div>
            <select name="status" class="filter-select">
                <option value="all" <?php echo $status==='all'?'selected':''; ?>>Tất cả trạng thái</option>
                <option value="PENDING" <?php echo $status==='PENDING'?'selected':''; ?>>PENDING</option>
                <option value="CONFIRMED" <?php echo $status==='CONFIRMED'?'selected':''; ?>>CONFIRMED</option>
                <option value="SHIPPED" <?php echo $status==='SHIPPED'?'selected':''; ?>>SHIPPED</option>
                <option value="DELIVERED" <?php echo $status==='DELIVERED'?'selected':''; ?>>DELIVERED</option>
                <option value="CANCELLED" <?php echo $status==='CANCELLED'?'selected':''; ?>>CANCELLED</option>
            </select>
        </div>
        <button type="submit" class="btn-submit btn-filter-standalone">
            <i class="fas fa-filter" aria-hidden="true"></i>
            <span style="margin-left:6px;">Lọc</span>
        </button>
    </form>
</div>

<table class="pet-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Mã đơn</th>
            <th>Tên đơn (mặt hàng)</th>
            <th>Khách hàng</th>
            <th>Người nhận</th>
            <th>Điện thoại</th>
            <th>Địa chỉ giao hàng</th>
            <th>Thanh toán</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
            <th>Tạo lúc</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="13">Không có đơn hàng.</td></tr>
        <?php else: ?>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?php echo htmlspecialchars($o['id']); ?></td>
                <td><?php echo htmlspecialchars($o['order_code']); ?></td>
                <td><?php echo htmlspecialchars($o['item_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($o['user_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($o['recipient_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($o['phone'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($o['shipping_address'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($o['payment_method'] ?? ''); ?></td>
                <td><?php echo number_format((float)$o['total_amount'], 0, ',', '.'); ?> đ</td>
                <td><?php echo htmlspecialchars($o['status'] ?? ''); ?></td>
                <td><?php echo nl2br(htmlspecialchars($o['notes'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars($o['created_at']); ?></td>
                <td>
                    <a href="index.php?p=order_history&user_id=<?php echo urlencode($o['user_id']); ?>" class="btn-edit">
                        <i class="fas fa-user"></i> Lịch sử KH
                    </a>
                    <a href="index.php?p=chi_tiet_don_hang&id=<?php echo urlencode($o['id']); ?>" class="btn-edit">
                        <i class="fas fa-file-alt"></i> Chi tiết
                    </a>
                    <a href="admin/xoa_donhang.php?id=<?php echo urlencode($o['id']); ?>" class="btn-delete" onclick="return confirm('Xóa đơn hàng này? Hành động không thể hoàn tác.');">
                        <i class="fas fa-trash-alt"></i> Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
// Optional: summary
// You could compute totals here if needed
?>

<?php // Removed placeholder JS-based mock; using DB-rendered table above ?>
