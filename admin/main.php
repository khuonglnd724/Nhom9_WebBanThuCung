<?php
// Load DB to compute low-stock accessories
require_once __DIR__ . '/../connect.php';

$threshold = isset($_GET['low_stock_threshold']) ? (int)$_GET['low_stock_threshold'] : 5;
$lowStockAccessories = [];

$stmt = $conn->prepare("SELECT id, name, stock FROM accessories WHERE stock IS NOT NULL AND stock <= ? ORDER BY stock ASC, name ASC LIMIT 20");
if ($stmt) {
    $stmt->bind_param('i', $threshold);
    $stmt->execute();
    $res = $stmt->get_result();
    $lowStockAccessories = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$lowStockCount = count($lowStockAccessories);

    // Totals for pets, accessories, orders
    $totalPets = 0; $totalAccessories = 0; $totalOrders = 0;
    if ($st = $conn->prepare("SELECT COUNT(*) AS c FROM pets")) { $st->execute(); $totalPets = (int)($st->get_result()->fetch_assoc()['c'] ?? 0); $st->close(); }
    if ($st = $conn->prepare("SELECT COUNT(*) AS c FROM accessories")) { $st->execute(); $totalAccessories = (int)($st->get_result()->fetch_assoc()['c'] ?? 0); $st->close(); }
    if ($st = $conn->prepare("SELECT COUNT(*) AS c FROM orders")) { $st->execute(); $totalOrders = (int)($st->get_result()->fetch_assoc()['c'] ?? 0); $st->close(); }

    // Today's orders
    $todayOrders = [];
    if ($st = $conn->prepare("SELECT o.id, o.order_code, o.total_amount, o.status, o.created_at, u.full_name AS user_name 
                              FROM orders o 
                              LEFT JOIN users u ON u.id = o.user_id 
                              WHERE DATE(o.created_at) = CURDATE() 
                              ORDER BY o.created_at DESC LIMIT 20")) {
        $st->execute();
        $todayOrders = $st->get_result()->fetch_all(MYSQLI_ASSOC);
        $st->close();
    }
?>
<h2>Chào mừng đến với StarryPets</h2>
<p>Đây là trang chủ hiển thị danh sách thú cưng và phụ kiện.</p>

<div id="stats-row">
    <div class="stat-box green">
            <span class="value"><?php echo number_format($totalPets, 0, ',', '.'); ?></span>
        <span class="label">Thú Cưng</span>
    </div>
    <div class="stat-box blue">
            <span class="value"><?php echo number_format($totalAccessories, 0, ',', '.'); ?></span>
        <span class="label">Phụ Kiện</span>
    </div>
    <div class="stat-box yellow">
            <span class="value"><?php echo number_format($totalOrders, 0, ',', '.'); ?></span>
        <span class="label">Đơn Hàng</span>
    </div>
    <div class="stat-box purple">
        <span class="value"><?php echo number_format($lowStockCount, 0, ',', '.'); ?></span>
        <span class="label">Sản Phẩm Sắp Hết Hàng</span>
    </div>
</div>

    <div class="dashboard-area">
        <!-- Bỏ biểu đồ doanh thu theo yêu cầu -->
        <div class="chart-box small"><p>Tình trạng đơn hàng</p></div>
    
    
    </div>

<div id="low-stock-list" style="margin-top:20px;">
    <h3>Sản Phẩm Sắp Hết Hàng (≤ <?php echo htmlspecialchars($threshold); ?>)</h3>
    <table class="pet-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên phụ kiện</th>
                <th>Tồn kho</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lowStockAccessories)): ?>
                <tr><td colspan="3">Không có sản phẩm sắp hết hàng.</td></tr>
            <?php else: ?>
                <?php foreach ($lowStockAccessories as $acc): ?>
                <tr>
                    <td><?php echo htmlspecialchars($acc['id']); ?></td>
                    <td><?php echo htmlspecialchars($acc['name']); ?></td>
                    <td><?php echo htmlspecialchars($acc['stock']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div style="margin-top:8px; font-size:12px; color:#666;">
        Ngưỡng cảnh báo có thể chỉnh bằng tham số URL: ?low_stock_threshold=5
    </div>
    
</div>

<div id="latest-orders">
    <h3>Đơn Hàng Mới Nhất</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Khách Hàng</th>
                <th>Người Nhận</th>
                <th>Tổng Tiền</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($todayOrders)): ?>
                <tr><td colspan="5">Hôm nay chưa có đơn hàng.</td></tr>
            <?php else: ?>
                <?php foreach ($todayOrders as $ord): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ord['order_code'] ?: ('#'.$ord['id'])); ?></td>
                    <td><?php echo htmlspecialchars($ord['user_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($ord['user_name'] ?? ''); ?></td>
                    <td><?php echo number_format((float)$ord['total_amount'], 0, ',', '.'); ?> đ</td>
                    <td><?php echo htmlspecialchars($ord['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
