<?php
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index để load layout và CSS chung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=them_thucung');
    exit;
}
?>

<h2 class="page-title">Thêm Thú Cưng Mới</h2>

<form action="index.php?p=luu_thucung" method="POST" enctype="multipart/form-data" class="pet-form">

    <div>
        <label for="ten">Tên thú cưng:</label>
        <input type="text" id="ten" name="ten" required>
    </div>

    <div>
        <label for="loai">Loại:</label>
        <select id="loai" name="loai" required>
            <option value="Chó">Chó</option>
            <option value="Mèo">Mèo</option>
        </select>
    </div>

    <div>
        <label for="giong">Giống:</label>
        <input type="text" id="giong" name="giong" required>
    </div>

    <div>
        <label for="gioi_tinh">Giới tính:</label>
        <select id="gioi_tinh" name="gioi_tinh" required>
            <option value="MALE">Nam</option>
            <option value="FEMALE">Nữ</option>
            <option value="UNKNOWN">Không rõ</option>
        </select>
    </div>

    <div>
        <label for="age_months">Tuổi (tháng):</label>
        <input type="number" id="age_months" name="age_months" min="0" value="0">
    </div>

    <div>
        <label for="color">Màu:</label>
        <input type="text" id="color" name="color">
    </div>

    <div>
        <label for="size">Kích thước:</label>
        <select id="size" name="size">
            <option value="Nhỏ">Nhỏ</option>
            <option value="Trung bình">Trung bình</option>
            <option value="Lớn">Lớn</option>
        </select>
    </div>

    <div>
        <label for="gia">Giá:</label>
        <input type="number" id="gia" name="gia" required step="0.01">
    </div>

    <div>
        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" min="0" value="1">
    </div>

    <div>
        <label for="status">Trạng thái:</label>
        <select id="status" name="status">
            <option value="AVAILABLE">AVAILABLE</option>
            <option value="SOLD">SOLD</option>
            <option value="HIDDEN">HIDDEN</option>
        </select>
    </div>

    <div>
        <label for="anh">Ảnh:</label>
        <input type="file" id="anh" name="anh" accept="image/*" required>
    </div>

    <div>
        <label for="mota">Mô tả:</label>
        <textarea id="mota" name="mota" rows="5" placeholder="Nhập mô tả về thú cưng..."></textarea>
    </div>

    <button type="submit" class="btn-save">Lưu Thú Cưng</button>

</form>
