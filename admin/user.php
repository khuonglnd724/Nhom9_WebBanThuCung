<?php
require_once __DIR__ . '/auth.php';
// Redirect if accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=user');
    exit;
}

// Kết nối database
require_once __DIR__ . '/../connect.php';

// Lấy tham số tìm kiếm và lọc
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';

// Xây dựng câu query
$sql = "SELECT id, full_name, email, phone, role, created_at FROM users WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if ($role_filter !== 'all') {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC";

// Thực thi query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h2 class="page-title">Quản Lý Người Dùng</h2>

<div class="top-bar">
    <form method="get" action="index.php" class="search-filter-form">
        <input type="hidden" name="p" value="user">
        
        <div class="search-controls">
            <div class="input-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="q" value="<?php echo isset($_GET['q'])?htmlspecialchars($_GET['q']):''; ?>" placeholder="Tìm người dùng..." class="search-input" />
            </div>
            
            <select name="role" class="filter-select">
                <option value="all" <?php echo ($role_filter === 'all') ? 'selected' : ''; ?>>Tất cả vai trò</option>
                <option value="CUSTOMER" <?php echo ($role_filter === 'CUSTOMER') ? 'selected' : ''; ?>>Khách hàng</option>
                <option value="ADMIN" <?php echo ($role_filter === 'ADMIN') ? 'selected' : ''; ?>>Quản trị</option>
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
            <th>Tên đầy đủ</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Vai trò</th>
            <th>Ngày đăng ký</th>
            <th>Hành động</th>
        </tr>
    </thead>
    
    <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="6">Không tìm thấy người dùng.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
                    <td>
                        <?php if ($user['role'] === 'ADMIN'): ?>
                            <span class="status-active">ADMIN</span>
                        <?php else: ?>
                            <span>CUSTOMER</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td>
                        <a href="index.php?p=order_history&user_id=<?php echo urlencode($user['id']); ?>" class="btn-edit">
                            <i class="fas fa-history"></i> Lịch sử
                        </a>
                        <a href="#" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>