<?php
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

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare('SELECT * FROM accessories WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) {
    echo '<p>Không tìm thấy phụ kiện với ID này.</p>';
    return;
}
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

<!-- Sample accessories table for import preview -->
<h3 style="margin-top:30px">Bảng mẫu Phụ Kiện (dùng để import dữ liệu)</h3>
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
        </tr>
    </thead>
    <tbody>
        <?php
        $sample_rows = [
            [
                'id' => 1,
                'img' => '../assets/images/sample-accessory.jpg',
                'category' => 'Thức ăn cho chó',
                'name' => 'Hạt khô vị bò',
                'brand' => 'PetFood',
                'material' => 'Ngũ cốc',
                'size' => '2kg',
                'description' => 'Dinh dưỡng cao, phù hợp cho chó mọi lứa tuổi',
                'price' => 220000,
                'stock' => 30,
                'status' => 'ACTIVE'
            ]
        ];

        foreach ($sample_rows as $r):
        ?>
        <tr>
            <td><?php echo htmlspecialchars($r['id']); ?></td>
            <td><?php if(!empty($r['img'])): ?><img src="<?php echo htmlspecialchars($r['img']); ?>" class="pet-img" alt=""><?php endif; ?></td>
            <td><?php echo htmlspecialchars($r['category']); ?></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td><?php echo htmlspecialchars($r['brand']); ?></td>
            <td><?php echo htmlspecialchars($r['material']); ?></td>
            <td><?php echo htmlspecialchars($r['size']); ?></td>
            <td><?php echo nl2br(htmlspecialchars(substr($r['description'],0,120))); ?></td>
            <td><?php echo number_format($r['price'],0,',','.'); ?> đ</td>
            <td><?php echo htmlspecialchars($r['stock']); ?></td>
            <td><?php echo htmlspecialchars($r['status']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
