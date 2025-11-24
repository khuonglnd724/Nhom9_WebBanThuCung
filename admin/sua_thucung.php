<?php
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index để load layout và CSS chung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=sua_thucung&id=' . urlencode(isset($_GET['id'])?$_GET['id']:'') );
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';

// Giả lập dữ liệu (sẽ thay bằng truy vấn DB khi cần)
$tc = [
    'id' => $id,
    'ten' => 'Milu',
    'loai' => 'Chó',
    'giong' => 'Poodle',
    'anh' => 'poodle.jpg',
    'mota' => 'Thú cưng dễ thương'
];
?>

<h2 class="page-title">Sửa Thú Cưng</h2>

<form action="xuly_sua_thucung.php" method="POST" enctype="multipart/form-data" class="pet-form">
    <input type="hidden" name="id" value="<?= htmlspecialchars($tc['id']) ?>">

    <div>
        <label for="ten">Tên:</label>
        <input type="text" id="ten" name="ten" value="<?= htmlspecialchars($tc['ten']) ?>" required>
    </div>

    <div>
        <label for="loai">Loại:</label>
        <select id="loai" name="loai" required>
            <option value="Chó"<?= $tc['loai']==='Chó' ? ' selected' : '' ?>>Chó</option>
            <option value="Mèo"<?= $tc['loai']==='Mèo' ? ' selected' : '' ?>>Mèo</option>
        </select>
    </div>

    <div>
        <label for="giong">Giống:</label>
        <input type="text" id="giong" name="giong" value="<?= htmlspecialchars($tc['giong']) ?>" required>
    </div>

    <div>
        <label>Ảnh hiện tại:</label>
        <br>
        <img src="../assets/images/<?= htmlspecialchars($tc['anh']) ?>" class="pet-img" alt="">
    </div>

    <div>
        <label for="gioi_tinh">Giới tính:</label>
        <select id="gioi_tinh" name="gioi_tinh" required>
            <option value="MALE"<?= (isset($tc['gender']) && $tc['gender']==='MALE')||($tc['gioi_tinh']==='MALE')? ' selected' : '' ?>>Nam</option>
            <option value="FEMALE"<?= (isset($tc['gender']) && $tc['gender']==='FEMALE')||($tc['gioi_tinh']==='FEMALE')? ' selected' : '' ?>>Nữ</option>
            <option value="UNKNOWN"<?= (isset($tc['gender']) && $tc['gender']==='UNKNOWN')||($tc['gioi_tinh']==='UNKNOWN')? ' selected' : '' ?>>Không rõ</option>
        </select>
    </div>

    <div>
        <label for="age_months">Tuổi (tháng):</label>
        <input type="number" id="age_months" name="age_months" min="0" value="<?= isset($tc['age_months'])?htmlspecialchars($tc['age_months']):0 ?>">
    </div>

    <div>
        <label for="color">Màu:</label>
        <input type="text" id="color" name="color" value="<?= isset($tc['color'])?htmlspecialchars($tc['color']):'' ?>">
    </div>

    <div>
        <label for="size">Kích thước:</label>
        <select id="size" name="size">
            <option value="Nhỏ"<?= (isset($tc['size']) && $tc['size']==='Nhỏ')? ' selected' : '' ?>>Nhỏ</option>
            <option value="Trung bình"<?= (isset($tc['size']) && $tc['size']==='Trung bình')? ' selected' : '' ?>>Trung bình</option>
            <option value="Lớn"<?= (isset($tc['size']) && $tc['size']==='Lớn')? ' selected' : '' ?>>Lớn</option>
        </select>
    </div>

    <div>
        <label for="gia">Giá:</label>
        <input type="number" id="gia" name="gia" value="<?= isset($tc['price'])?htmlspecialchars($tc['price']):'' ?>" step="0.01">
    </div>

    <div>
        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" min="0" value="<?= isset($tc['stock'])?htmlspecialchars($tc['stock']):1 ?>">
    </div>

    <div>
        <label for="status">Trạng thái:</label>
        <select id="status" name="status">
            <option value="AVAILABLE"<?= (isset($tc['status']) && $tc['status']==='AVAILABLE')? ' selected' : '' ?>>AVAILABLE</option>
            <option value="SOLD"<?= (isset($tc['status']) && $tc['status']==='SOLD')? ' selected' : '' ?>>SOLD</option>
            <option value="HIDDEN"<?= (isset($tc['status']) && $tc['status']==='HIDDEN')? ' selected' : '' ?>>HIDDEN</option>
        </select>
    </div>

    <div>
        <label for="anh">Đổi ảnh mới (nếu có):</label>
        <input type="file" id="anh" name="anh" accept="image/*">
    </div>

    <div>
        <label for="mota">Mô tả:</label>
        <textarea id="mota" name="mota" rows="5"><?= htmlspecialchars($tc['mota']) ?></textarea>
    </div>

    <button type="submit" class="btn-save">Cập nhật</button>
</form>
