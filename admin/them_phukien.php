<?php
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=them_phukien');
    exit;
}

// Kết nối database để lấy danh sách categories
require_once __DIR__ . '/../connect.php';

// Lấy danh sách categories
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
$categories = [];
if ($cat_result) {
    while ($cat = $cat_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}
?>

<h2 class="page-title">Thêm Phụ Kiện Mới</h2>

<form action="luu_phukien.php" method="POST" enctype="multipart/form-data" class="pet-form">

    <input type="hidden" name="type" value="ACCESSORY">

    <div>
        <label>Danh mục:</label>
        <select name="category_id" id="category_id" onchange="toggleNewCategory()" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>">
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
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Thương hiệu:</label>
        <input type="text" name="brand">
    </div>

    <div>
        <label>Chất liệu:</label>
        <input type="text" name="material">
    </div>

    <div>
        <label>Kích thước:</label>
        <input type="text" name="size">
    </div>

    <div>
        <label>Mô tả:</label>
        <textarea name="description" rows="4"></textarea>
    </div>

    <div>
        <label>Giá bán:</label>
        <input type="number" name="price" step="0.01" required>
    </div>

    <div>
        <label>Số lượng:</label>
        <input type="number" name="stock" value="1" required>
    </div>

    <div>
        <label>Trạng thái:</label>
        <select name="status">
            <option value="AVAILABLE">AVAILABLE</option>
            <option value="OUT_OF_STOCK">OUT_OF_STOCK</option>
            <option value="HIDDEN">HIDDEN</option>
        </select>
    </div>

    <div>
        <label>Ảnh:</label>
        <input type="file" name="image" accept="image/*">
    </div>

    <button type="submit" class="btn-save">Lưu Phụ Kiện</button>
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
