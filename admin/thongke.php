<?php
// Redirect if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=thongke');
    exit;
}

require_once __DIR__ . '/../connect.php';

// Get current month and year
$currentMonth = date('Y-m');
$currentYear = date('Y');

// Total revenue (all time)
$stmt = $conn->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE status != 'CANCELLED'");
$stmt->execute();
$totalRevenue = $stmt->get_result()->fetch_assoc()['total_revenue'] ?? 0;
$stmt->close();

// Monthly revenue (current month)
$stmt = $conn->prepare("SELECT SUM(total_amount) as monthly_revenue FROM orders WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status != 'CANCELLED'");
$stmt->bind_param('s', $currentMonth);
$stmt->execute();
$monthlyRevenue = $stmt->get_result()->fetch_assoc()['monthly_revenue'] ?? 0;
$stmt->close();

// Total orders
$stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders");
$stmt->execute();
$totalOrders = $stmt->get_result()->fetch_assoc()['total_orders'] ?? 0;
$stmt->close();

// Monthly orders
$stmt = $conn->prepare("SELECT COUNT(*) as monthly_orders FROM orders WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
$stmt->bind_param('s', $currentMonth);
$stmt->execute();
$monthlyOrders = $stmt->get_result()->fetch_assoc()['monthly_orders'] ?? 0;
$stmt->close();

// Orders by status
$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$stmt->execute();
$ordersByStatus = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Total products (pets + accessories) with stock > 0
$stmt = $conn->prepare("SELECT 
    (SELECT COUNT(*) FROM pets WHERE stock > 0) as total_pets,
    (SELECT COUNT(*) FROM accessories WHERE stock > 0) as total_accessories");
$stmt->execute();
$products = $stmt->get_result()->fetch_assoc();
$totalPets = $products['total_pets'] ?? 0;
$totalAccessories = $products['total_accessories'] ?? 0;
$stmt->close();

// Total users
$stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
$stmt->execute();
$totalUsers = $stmt->get_result()->fetch_assoc()['total_users'] ?? 0;
$stmt->close();

// Top selling items (last 3 months)
$stmt = $conn->prepare("SELECT 
    od.item_type,
    od.item_id,
    CASE od.item_type 
        WHEN 'pet' THEN (SELECT p.name FROM pets p WHERE p.id = od.item_id)
        WHEN 'accessory' THEN (SELECT a.name FROM accessories a WHERE a.id = od.item_id)
    END as item_name,
    CASE od.item_type 
        WHEN 'pet' THEN (SELECT c.name FROM pets p JOIN categories c ON p.category_id = c.id WHERE p.id = od.item_id)
        WHEN 'accessory' THEN (SELECT c.name FROM accessories a JOIN categories c ON a.category_id = c.id WHERE a.id = od.item_id)
    END as category_name,
    COUNT(*) as sold_count,
    SUM(od.quantity) as total_quantity
FROM order_details od
JOIN orders o ON o.id = od.order_id
WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH) AND o.status != 'CANCELLED'
GROUP BY od.item_type, od.item_id
ORDER BY sold_count DESC
LIMIT 10");
$stmt->execute();
$topItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Revenue by month (last 6 months)
$stmt = $conn->prepare("SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND status != 'CANCELLED'
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC");
$stmt->execute();
$monthlyTrends = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$statusLabels = [
    'PENDING' => 'Đang chờ xử lý',
    'CONFIRMED' => 'Đã xác nhận',
    'SHIPPED' => 'Đang giao',
    'DELIVERED' => 'Đã hoàn thành',
    'CANCELLED' => 'Đã hủy'
];
?>

<h2 class="page-title">Thống Kê</h2>

<!-- Summary Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin:20px 0;">
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-dollar-sign" style="font-size:28px; color:#28a745;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Tổng doanh thu</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-chart-line" style="font-size:28px; color:#17a2b8;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Doanh thu tháng <?php echo date('m/Y'); ?></div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($monthlyRevenue, 0, ',', '.'); ?> đ</div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-shopping-cart" style="font-size:28px; color:#ffc107;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Tổng đơn hàng</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($totalOrders, 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-calendar-check" style="font-size:28px; color:#6c757d;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Đơn hàng tháng này</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($monthlyOrders, 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-users" style="font-size:28px; color:#007bff;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Tổng khách hàng</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($totalUsers, 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-box" style="font-size:28px; color:#dc3545;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Sản phẩm</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($totalPets + $totalAccessories, 0, ',', '.'); ?></div>
                <div style="font-size:11px; color:#999;">Thú cưng: <?php echo $totalPets; ?> | Phụ kiện: <?php echo $totalAccessories; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(400px, 1fr)); gap:20px; margin-top:30px;">
    
    <!-- Orders by Status -->
    <div style="border:1px solid #ddd; border-radius:8px; padding:20px; background:#fff;">
        <h3 style="margin:0 0 16px 0; font-size:16px;">Đơn hàng theo trạng thái</h3>
        <table class="pet-table">
            <thead>
                <tr>
                    <th>Trạng thái</th>
                    <th>Số lượng</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ordersByStatus)): ?>
                    <tr><td colspan="3">Không có dữ liệu</td></tr>
                <?php else: ?>
                    <?php foreach ($ordersByStatus as $item): 
                        $percent = $totalOrders > 0 ? ($item['count'] / $totalOrders * 100) : 0;
                    ?>
                    <tr>
                        <td><?php echo $statusLabels[$item['status']] ?? $item['status']; ?></td>
                        <td><?php echo number_format($item['count'], 0, ',', '.'); ?></td>
                        <td><?php echo number_format($percent, 1); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Top Selling Items -->
    <div style="border:1px solid #ddd; border-radius:8px; padding:20px; background:#fff;">
        <h3 style="margin:0 0 16px 0; font-size:16px;">Sản phẩm bán chạy (3 tháng gần đây)</h3>
        <table class="pet-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Loại</th>
                    <th>Đã bán</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($topItems)): ?>
                    <tr><td colspan="3">Không có dữ liệu</td></tr>
                <?php else: ?>
                    <?php foreach ($topItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($item['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo number_format($item['total_quantity'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
</div>

<!-- Monthly Revenue Trend -->
<div style="border:1px solid #ddd; border-radius:8px; padding:20px; background:#fff; margin-top:20px;">
    <h3 style="margin:0 0 16px 0; font-size:16px;">Xu hướng doanh thu (6 tháng gần đây)</h3>
    <table class="pet-table">
        <thead>
            <tr>
                <th>Tháng</th>
                <th>Doanh thu</th>
                <th>Số đơn hàng</th>
                <th>Giá trị TB/đơn</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($monthlyTrends)): ?>
                <tr><td colspan="4">Không có dữ liệu</td></tr>
            <?php else: ?>
                <?php foreach ($monthlyTrends as $trend): 
                    $avgOrderValue = $trend['order_count'] > 0 ? ($trend['revenue'] / $trend['order_count']) : 0;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($trend['month']); ?></td>
                    <td><?php echo number_format($trend['revenue'], 0, ',', '.'); ?> đ</td>
                    <td><?php echo number_format($trend['order_count'], 0, ',', '.'); ?></td>
                    <td><?php echo number_format($avgOrderValue, 0, ',', '.'); ?> đ</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>