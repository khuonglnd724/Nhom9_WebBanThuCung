<?php
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM accessories WHERE id = $id")->fetch_assoc();
?>

<h2 class="page-title">Sửa Phụ Kiện</h2>

<form action="index.php?p=capnhat_phukien&id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="pet-form">

    <div>
        <label>Danh mục:</label>
        <input type="number" name="category_id" value="<?= $data['category_id'] ?>" required>
    </div>

    <div>
        <label>Tên phụ kiện:</label>
        <input type="text" name="name" value="<?= $data['name'] ?>" required>
    </div>

    <div>
        <label>Thương hiệu:</label>
        <input type="text" name="brand" value="<?= $data['brand'] ?>" required>
    </div>

    <div>
        <label>Chất liệu:</label>
        <input type="text" name="material" value="<?= $data['material'] ?>">
    </div>

    <div>
        <label>Kích thước:</label>
        <input type="text" name="size" value="<?= $data['size'] ?>">
    </div>

    <div>
        <label>Mô tả:</label>
        <textarea name="description" rows="4"><?= $data['description'] ?></textarea>
    </div>

    <div>
        <label>Giá:</label>
        <input type="number" name="price" value="<?= $data['price'] ?>" required>
    </div>

    <div>
        <label>Tồn kho:</label>
        <input type="number" name="stock" value="<?= $data['stock'] ?>" required>
    </div>

    <div>
        <label>Ảnh hiện tại:</label><br>
        <img src="../images/<?= $data['image'] ?>" width="120">
    </div>

    <div>
        <label>Đổi ảnh mới (nếu có):</label>
        <input type="file" name="image" accept="image/*">
    </div>

    <button type="submit" class="btn-save">Cập Nhật</button>
</form>
