<?php
require_once __DIR__ . '/auth.php';
// Ensure DB connection is available
require_once __DIR__ . '/../connect.php';

// Validate id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<p style="color:red">ID không hợp lệ.</p>';
    return;
}
$id = (int) $_GET['id'];

if (!isset($conn) || $conn === null) {
    echo '<p style="color:red">Không có kết nối tới cơ sở dữ liệu.</p>';
    return;
}

// Lấy thông tin phụ kiện với JOIN categories và images
$sql = "SELECT a.*, c.name AS category_name, img.image_url 
        FROM accessories a 
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN images img ON img.item_id = a.id AND img.item_type = 'accessory' AND img.is_primary = 1
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    echo '<p>Không tìm thấy phụ kiện với ID này.</p>';
    return;
}

// Lấy danh sách categories cho dropdown
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
$categories = [];
if ($cat_result) {
    while ($cat = $cat_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}
?>

<h2 class="page-title">Sửa Phụ Kiện</h2>

<form action="capnhat_phukien.php" method="POST" enctype="multipart/form-data" class="pet-form">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div>
        <label>Danh mục:</label>
        <select name="category_id" id="category_id" onchange="toggleNewCategory()" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($data['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
            <option value="new">+ Thêm danh mục mới</option>
        </select>
    </div>

    <div id="new_category_field" style="display: none;">
        <label>Tên danh mục mới:</label>
        <input type="text" name="new_category_name" id="new_category_name" placeholder="Nhập tên danh mục mới">
    </div>

    <div>
        <label>Tên phụ kiện:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
    </div>

    <div>
        <label>Thương hiệu:</label>
        <input type="text" name="brand" value="<?= htmlspecialchars($data['brand']) ?>">
    </div>

    <div>
        <label>Chất liệu:</label>
        <input type="text" name="material" value="<?= htmlspecialchars($data['material']) ?>">
    </div>

    <div>
        <label>Kích thước:</label>
        <input type="text" name="size" value="<?= htmlspecialchars($data['size']) ?>">
    </div>

    <div>
        <label>Mô tả:</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($data['description']) ?></textarea>
    </div>

    <div>
        <label>Giá:</label>
        <input type="number" name="price" value="<?= $data['price'] ?>" step="0.01" required>
    </div>

    <div>
        <label>Tồn kho:</label>
        <input type="number" name="stock" value="<?= $data['stock'] ?>" required>
    </div>

    <div>
        <label>Trạng thái:</label>
        <select name="status">
            <option value="AVAILABLE" <?= ($data['status'] === 'AVAILABLE') ? 'selected' : '' ?>>AVAILABLE</option>
            <option value="OUT_OF_STOCK" <?= ($data['status'] === 'OUT_OF_STOCK') ? 'selected' : '' ?>>OUT_OF_STOCK</option>
            <option value="HIDDEN" <?= ($data['status'] === 'HIDDEN') ? 'selected' : '' ?>>HIDDEN</option>
        </select>
    </div>

    <div>
        <label>Ảnh hiện tại:</label><br>
        <?php if (!empty($data['image_url'])): ?>
            <img src="<?= htmlspecialchars($data['image_url']) ?>" class="pet-img" alt="">
        <?php else: ?>
            <p>Chưa có ảnh</p>
        <?php endif; ?>
    </div>

    <div>
        <label>Đổi ảnh mới (nếu có):</label>
        <input type="file" name="image" accept="image/*">
    </div>

    <button type="submit" class="btn-save">Cập Nhật</button>
</form>

<script>
function toggleNewCategory() {
    const categorySelect = document.getElementById('category_id');
    const newCategoryField = document.getElementById('new_category_field');
    const newCategoryInput = document.getElementById('new_category_name');
    
    if (categorySelect.value === 'new') {
        newCategoryField.style.display = 'block';
        newCategoryInput.required = true;
    } else {
        newCategoryField.style.display = 'none';
        newCategoryInput.required = false;
        newCategoryInput.value = '';
    }
}
</script>
