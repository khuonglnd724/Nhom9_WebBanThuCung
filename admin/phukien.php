<?php
require_once __DIR__ . '/auth.php';
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=phukien');
    exit;
}

// Kết nối database
require_once __DIR__ . '/../connect.php';

// Xử lý toggle visibility
if (isset($_GET['toggle_visibility']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("UPDATE accessories SET is_visible = NOT is_visible WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect về trang hiện tại với các filter giữ nguyên
    $redirect = 'index.php?p=phukien';
    if (isset($_GET['q'])) $redirect .= '&q=' . urlencode($_GET['q']);
    if (isset($_GET['category'])) $redirect .= '&category=' . urlencode($_GET['category']);
    header('Location: ' . $redirect);
    exit;
}

// Tạo base path cho ảnh (từ thư mục admin lên root)
$base_path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '\\/');

// Lấy tham số tìm kiếm từ URL
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Xây dựng câu query với JOIN để lấy tên category và ảnh
$sql = "SELECT 
            a.id, 
            a.category_id, 
            c.name AS category_name,
            a.name, 
            a.brand, 
            a.material, 
            a.size, 
            a.description, 
            a.price, 
            a.stock, 
            a.status,
            a.is_visible,
            img.image_url,
            img.item_id as has_image
        FROM accessories a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN images img ON img.item_id = a.id AND img.item_type = 'accessory' AND img.is_primary = 1
        WHERE 1=1";

$params = [];
$types = '';

// Tìm kiếm theo tên hoặc thương hiệu
if (!empty($q)) {
    $sql .= " AND (a.name LIKE ? OR a.brand LIKE ?)";
    $search_param = '%' . $q . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

// Lọc theo category_id
if ($category_filter !== 'all') {
    $sql .= " AND a.category_id = ?";
    $params[] = intval($category_filter);
    $types .= 'i';
}

$sql .= " ORDER BY a.id DESC";

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
$accessories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Debug: Kiểm tra số lượng ảnh trong bảng images
$debug_sql = "SELECT COUNT(*) as total FROM images WHERE item_type = 'accessory'";
$debug_result = $conn->query($debug_sql);
$debug_row = $debug_result->fetch_assoc();
// echo "<!-- DEBUG: Total accessory images in DB: " . $debug_row['total'] . " -->";

// Lấy danh sách categories để hiển thị trong dropdown (chỉ lấy ACCESSORY và BOTH)
$cat_sql = "SELECT id, name FROM categories WHERE type IN ('ACCESSORY', 'BOTH') ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
$categories = [];
if ($cat_result) {
    while ($cat = $cat_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}
?>

<h2 class="page-title">Danh Sách Phụ Kiện</h2>

<div class="top-bar">
    <form method="get" action="index.php" class="search-filter-form">
        <input type="hidden" name="p" value="phukien">

        <div class="search-controls">
            <div class="input-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="q" value="<?php echo isset($_GET['q'])?htmlspecialchars($_GET['q']):''; ?>" placeholder="Tìm sản phẩm..." class="search-input" />
            </div>

            <select name="category" class="filter-select">
                <option value="all">Tất cả</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-submit btn-filter-standalone">
            <i class="fas fa-filter" aria-hidden="true"></i>
            <span style="margin-left:6px">Lọc</span>
        </button>
    </form>

    <a href="index.php?p=them_phukien" class="btn-add">
        <i class="fas fa-plus-circle"></i> Thêm Phụ Kiện
    </a>
</div>

<table class="pet-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Category</th>
            <th>Tên</th>
            <th>Thương hiệu</th>
            <th>Chất liệu</th>
            <th>Kích thước</th>
            <th>Mô tả</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Trạng thái</th>
            <th>Hiển thị</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($accessories)): ?>
            <tr><td colspan="13">Không tìm thấy phụ kiện.</td></tr>
        <?php else: ?>
            <?php foreach ($accessories as $row): 
                // Debug: In ra image_url để kiểm tra
                // echo "<!-- Row ID: " . $row['id'] . ", Image URL: '" . ($row['image_url'] ?? 'NULL') . "' -->";
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td>
                    <?php 
                    if (!empty($row['image_url'])): 
                        // Tạo đường dẫn ảnh từ thư mục admin
                        $img_path = $row['image_url'];
                        // Nếu path bắt đầu với /, loại bỏ nó
                        if (substr($img_path, 0, 1) === '/') {
                            $img_path = substr($img_path, 1);
                        }
                        // Tạo path tương đối từ admin lên root
                        $img_src = '../' . $img_path;
                    ?>
                        <img src="<?php echo htmlspecialchars($img_src); ?>" class="pet-img" alt="" onerror="console.error('Image not found:', this.src); this.parentElement.innerHTML='<span style=color:red>Lỗi: <?php echo htmlspecialchars($img_src); ?></span>';">
                    <?php else: ?>
                        <span style="color: #999;">Chưa có ảnh</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['brand']); ?></td>
                <td><?php echo htmlspecialchars($row['material']); ?></td>
                <td><?php echo htmlspecialchars($row['size']); ?></td>
                <td><?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 120))); ?></td>
                <td><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <button onclick="toggleVisibility(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($q); ?>', '<?php echo htmlspecialchars($category_filter); ?>')" 
                            style="border:none; background:none; cursor:pointer; padding:4px 8px; font-size:14px;"
                            title="Click để thay đổi">
                        <?php if ($row['is_visible']): ?>
                            <span style="color: #28a745; font-weight: 600;">
                                <i class="fas fa-eye"></i> Hiện
                            </span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: 600;">
                                <i class="fas fa-eye-slash"></i> Ẩn
                            </span>
                        <?php endif; ?>
                    </button>
                </td>
                <td>
                    <a href="index.php?p=sua_phukien&id=<?php echo urlencode($row['id']); ?>" class="btn-edit">Sửa</a>
                    <a href="xoa_phukien.php?id=<?php echo urlencode($row['id']); ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa phụ kiện này?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
function toggleVisibility(id, q, category) {
    if (confirm('Bạn có chắc muốn thay đổi trạng thái hiển thị?')) {
        let url = 'index.php?p=phukien&toggle_visibility=1&id=' + id;
        if (q) url += '&q=' + encodeURIComponent(q);
        if (category && category !== 'all') url += '&category=' + encodeURIComponent(category);
        window.location.href = url;
    }
}
</script>
