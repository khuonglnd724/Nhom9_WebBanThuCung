<?php
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index để load layout và CSS chung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=thucung');
    exit;
}

// Kết nối database
require_once __DIR__ . '/../connect.php';

// Lấy tham số tìm kiếm từ URL
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$loai_filter = isset($_GET['loai']) ? $_GET['loai'] : 'all';

// Xây dựng câu query với JOIN để lấy tên giống từ bảng breeds và ảnh từ bảng images
$sql = "SELECT 
            p.id, 
            p.category_id, 
            p.name, 
            p.breed_id,
            b.name AS breed_name,
            p.gender, 
            p.age_months, 
            p.color, 
            p.size, 
            p.description, 
            p.price, 
            p.stock, 
            p.status,
            img.image_url,
            p.created_at, 
            p.updated_at
        FROM pets p
        LEFT JOIN breeds b ON p.breed_id = b.id
        LEFT JOIN images img ON img.item_id = p.id AND img.item_type = 'pet' AND img.is_primary = 1
        WHERE 1=1";

$params = [];
$types = '';

// Tìm kiếm theo tên
if (!empty($q)) {
    $sql .= " AND p.name LIKE ?";
    $params[] = '%' . $q . '%';
    $types .= 's';
}

// Lọc theo category_id (1=Chó, 2=Mèo)
if ($loai_filter !== 'all') {
    if ($loai_filter === 'Chó') {
        $sql .= " AND p.category_id = 1";
    } elseif ($loai_filter === 'Mèo') {
        $sql .= " AND p.category_id = 2";
    }
}

$sql .= " ORDER BY p.created_at DESC";

// Thực thi query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi prepare statement: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    die("Lỗi execute: " . $stmt->error);
}

$result = $stmt->get_result();
$filtered = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h2 class="page-title">Danh Sách Thú Cưng</h2>

<div class="top-bar">
    <form method="get" action="index.php" class="search-filter-form">
        <input type="hidden" name="p" value="thucung">
        
        <div class="input-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Tìm theo tên..." class="search-input" />
            <i class="fas fa-search search-icon"></i>
        </div>
        
        <select name="loai" class="filter-select">
            <option value="all"<?php echo $loai_filter==='all' ? ' selected' : ''; ?>>Tất cả</option>
            <option value="Chó"<?php echo $loai_filter==='Chó' ? ' selected' : ''; ?>>Chó</option>
            <option value="Mèo"<?php echo $loai_filter==='Mèo' ? ' selected' : ''; ?>>Mèo</option>
        </select>
        
        <button type="submit" class="btn-submit btn-filter-standalone">
            <i class="fas fa-filter" aria-hidden="true"></i>
            <span style="margin-left:6px;">Lọc</span>
        </button>
    </form>
    
    <a href="index.php?p=them_thucung" class="btn-add">
        <i class="fas fa-plus-circle"></i> Thêm Thú Cưng
    </a>
</div>

<table class="pet-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Category ID</th>
            <th>Tên</th>
            <th>Giống</th>
            <th>Giới tính</th>
            <th>Tuổi (tháng)</th>
            <th>Màu</th>
            <th>Kích thước</th>
            <th>Mô tả</th>
            <th>Giá</th>
            <th>Stock</th>
            <th>Trạng thái</th>
            <th>Tạo lúc</th>
            <th>Cập nhật</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($filtered)): ?>
            <tr><td colspan="16">Không tìm thấy kết quả.</td></tr>
        <?php else: ?>
            <?php foreach ($filtered as $pet): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pet['id']); ?></td>
                    <td>
                        <?php 
                        if (!empty($pet['image_url'])): 
                            // Tạo đường dẫn ảnh từ thư mục admin
                            $img_path = $pet['image_url'];
                            // Nếu path bắt đầu với /, loại bỏ nó
                            if (substr($img_path, 0, 1) === '/') {
                                $img_path = substr($img_path, 1);
                            }
                            // Tạo path tương đối từ admin lên root
                            $img_src = '../' . $img_path;
                        ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" class="pet-img" alt="" onerror="this.style.display='none'">
                        <?php else: ?>
                            <span style="color: #999;">Chưa có ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($pet['category_id']); ?></td>
                    <td><?php echo htmlspecialchars($pet['name']); ?></td>
                    <td><?php echo htmlspecialchars($pet['breed_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($pet['gender']); ?></td>
                    <td><?php echo htmlspecialchars($pet['age_months']); ?></td>
                    <td><?php echo htmlspecialchars($pet['color']); ?></td>
                    <td><?php echo htmlspecialchars($pet['size']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars(substr($pet['description'], 0, 120))); ?></td>
                    <td><?php echo number_format($pet['price'], 0, ',', '.'); ?> đ</td>
                    <td><?php echo htmlspecialchars($pet['stock']); ?></td>
                    <td><?php echo htmlspecialchars($pet['status']); ?></td>
                    <td><?php echo htmlspecialchars($pet['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($pet['updated_at']); ?></td>
                    <td>
                        <a href="index.php?p=sua_thucung&id=<?php echo urlencode($pet['id']); ?>" class="btn-edit">Sửa</a>
                        <a href="xoa_thucung.php?id=<?php echo urlencode($pet['id']); ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa thú cưng này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
