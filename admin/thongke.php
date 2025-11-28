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
$filterType = isset($_GET['filter_type']) && in_array($_GET['filter_type'], ['month', 'year']) ? $_GET['filter_type'] : 'month';
$selectedMonth = isset($_GET['month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month']) ? $_GET['month'] : $currentMonth;
$selectedYear = isset($_GET['year']) && preg_match('/^\d{4}$/', $_GET['year']) ? $_GET['year'] : $currentYear;

// Build filter label and conditions based on filter type
if ($filterType === 'month') {
    $filterLabel = date('m/Y', strtotime($selectedMonth . '-01'));
    $revenueCondition = "DATE_FORMAT(created_at, '%Y-%m') = ?";
    $revenueParam = $selectedMonth;
    $orderCondition = "DATE_FORMAT(created_at, '%Y-%m') = ?";
    $orderParam = $selectedMonth;
} else { // year
    $filterLabel = $selectedYear;
    $revenueCondition = "YEAR(created_at) = ?";
    $revenueParam = $selectedYear;
    $orderCondition = "YEAR(created_at) = ?";
    $orderParam = $selectedYear;
}

// Total revenue (all time) - only COMPLETED
$stmt = $conn->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'COMPLETED'");
$stmt->execute();
$totalRevenue = $stmt->get_result()->fetch_assoc()['total_revenue'] ?? 0;
$stmt->close();

// Filtered revenue - only COMPLETED
$stmt = $conn->prepare("SELECT SUM(total_amount) as filtered_revenue FROM orders WHERE " . $revenueCondition . " AND status = 'COMPLETED'");
$stmt->bind_param('s', $revenueParam);
$stmt->execute();
$filteredRevenue = $stmt->get_result()->fetch_assoc()['filtered_revenue'] ?? 0;
$stmt->close();

// Total orders
$stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders");
$stmt->execute();
$totalOrders = $stmt->get_result()->fetch_assoc()['total_orders'] ?? 0;
$stmt->close();

// Filtered orders (all statuses)
$stmt = $conn->prepare("SELECT COUNT(*) as filtered_orders FROM orders WHERE " . $orderCondition);
$stmt->bind_param('s', $orderParam);
$stmt->execute();
$filteredOrders = $stmt->get_result()->fetch_assoc()['filtered_orders'] ?? 0;
$stmt->close();

// Orders by status (all time)
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

// Revenue by month (last 6 months) - only COMPLETED
$stmt = $conn->prepare("SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND status = 'COMPLETED'
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

<!-- Filter Form -->
<form method="get" action="index.php" style="margin:18px 0 24px; display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap;">
    <input type="hidden" name="p" value="thongke" />
    
    <!-- Filter Type Selection -->
    <div style="background:#f8f9fa; border-radius:8px; padding:12px 18px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="font-size:14px; color:#333; font-weight:600; margin-bottom:10px;">Loại thống kê</div>
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                <input type="radio" name="filter_type" value="month" <?php echo $filterType === 'month' ? 'checked' : ''; ?> onchange="toggleFilterInputs(this.value)" />
                <span style="font-size:14px;">Theo tháng</span>
            </label>
            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                <input type="radio" name="filter_type" value="year" <?php echo $filterType === 'year' ? 'checked' : ''; ?> onchange="toggleFilterInputs(this.value)" />
                <span style="font-size:14px;">Theo năm</span>
            </label>
        </div>
    </div>
    
    <!-- Month Input -->
    <div id="monthInput" style="display:<?php echo $filterType === 'month' ? 'flex' : 'none'; ?>; align-items:center; gap:10px; background:#f8f9fa; border-radius:8px; padding:10px 18px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <label for="month" style="font-size:15px; color:#333; font-weight:500; margin-right:4px;">Chọn tháng</label>
        <input type="month" id="month" name="month" value="<?php echo htmlspecialchars($selectedMonth); ?>" style="padding:6px 12px; border:1px solid #ccc; border-radius:6px; font-size:15px; background:#fff; color:#222; outline:none;" />
    </div>
    
    <!-- Year Input -->
    <div id="yearInput" style="display:<?php echo $filterType === 'year' ? 'flex' : 'none'; ?>; align-items:center; gap:10px; background:#f8f9fa; border-radius:8px; padding:10px 18px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <label for="year" style="font-size:15px; color:#333; font-weight:500; margin-right:4px;">Chọn năm</label>
        <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($selectedYear); ?>" min="2020" max="2099" style="padding:6px 12px; border:1px solid #ccc; border-radius:6px; font-size:15px; background:#fff; color:#222; outline:none; width:100px;" />
    </div>
    
    <button type="submit" class="btn-submit" style="display:flex; align-items:center; gap:6px; background:linear-gradient(90deg,#007bff 60%,#17a2b8 100%); color:#fff; border:none; border-radius:7px; padding:8px 20px; font-size:14px; font-weight:500; box-shadow:0 1px 4px rgba(0,0,0,0.07); cursor:pointer; transition:background 0.2s;">
        <i class="fas fa-filter" aria-hidden="true" style="font-size:15px;"></i>
        <span>Áp dụng</span>
    </button>
</form>

<script>
function toggleFilterInputs(type) {
    document.getElementById('monthInput').style.display = type === 'month' ? 'flex' : 'none';
    document.getElementById('yearInput').style.display = type === 'year' ? 'flex' : 'none';
}
</script>

<!-- Summary Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin:20px 0;">
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-dollar-sign" style="font-size:28px; color:#28a745;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Tổng doanh thu (COMPLETED)</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-chart-line" style="font-size:28px; color:#17a2b8;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Doanh thu <?php echo $filterType === 'month' ? 'tháng' : ($filterType === 'year' ? 'năm' : ''); ?> <?php echo htmlspecialchars($filterLabel); ?> (COMPLETED)</div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($filteredRevenue, 0, ',', '.'); ?> đ</div>
            </div>
        </div>
    </div>
    
    <div style="border:1px solid #ddd; border-radius:8px; padding:16px; background:#fff;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-shopping-cart" style="font-size:28px; color:#ffc107;"></i>
            <div>
                <div style="font-size:12px; color:#666;">Đơn hàng <?php echo $filterType === 'month' ? 'tháng' : ($filterType === 'year' ? 'năm' : ''); ?> <?php echo htmlspecialchars($filterLabel); ?></div>
                <div style="font-size:20px; font-weight:600;"><?php echo number_format($filteredOrders, 0, ',', '.'); ?></div>
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