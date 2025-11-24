<h2 class="page-title">Danh Sách Phụ Kiện</h2>

<div class="top-bar">
    <a href="index.php?p=them_phukien" class="btn-add">➕ Thêm Phụ Kiện</a>
</div>

<table class="pet-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category ID</th>
            <th>Tên PK</th>
            <th>Thương hiệu</th>
            <th>Chất liệu</th>
            <th>Kích thước</th>
            <th>Mô tả</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Cập nhật</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
    <?php
        $sql = "SELECT * FROM accessories ORDER BY id DESC";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['category_id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['brand'] ?></td>
            <td><?= $row['material'] ?></td>
            <td><?= $row['size'] ?></td>
            <td><?= $row['description'] ?></td>
            <td><?= number_format($row['price'], 0, ',', '.') ?> đ</td>
            <td><?= $row['stock'] ?></td>

            <td>
                <?php if ($row['status'] == "ACTIVE"): ?>
                    <span class="status-active">ACTIVE</span>
                <?php else: ?>
                    <span class="status-inactive">INACTIVE</span>
                <?php endif; ?>
            </td>

            <td><?= $row['created_at'] ?></td>
            <td><?= $row['updated_at'] ?></td>

            <td>
                <a href="index.php?p=sua_phukien&id=<?= $row['id'] ?>" class="btn-edit">Sửa</a>
                <a href="index.php?p=them_phukien&copy_id=<?= $row['id'] ?>" class="btn-copy">Chép</a>
                <a onclick="return confirm('Bạn có chắc muốn xóa?')" 
                href="xoa_phukien.php?id=<?= $row['id'] ?>" 
                class="btn-delete">Xóa</a>
            </td>

        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
