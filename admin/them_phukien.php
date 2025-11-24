<h2 class="page-title">Thêm Phụ Kiện Mới</h2>

<form action="index.php?p=luu_phukien" method="POST" enctype="multipart/form-data" class="pet-form">

    <div>
        <label>Danh mục (category_id):</label>
        <input type="number" name="category_id" required>
    </div>

    <div>
        <label>Tên phụ kiện:</label>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>Thương hiệu:</label>
        <input type="text" name="brand" required>
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
        <input type="number" name="price" required>
    </div>

    <div>
        <label>Số lượng:</label>
        <input type="number" name="stock" required>
    </div>

    <div>
        <label>Ảnh:</label>
        <input type="file" name="image" accept="image/*">
    </div>

    <button type="submit" class="btn-save">Lưu Phụ Kiện</button>
</form>
