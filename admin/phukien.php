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
                <option value="thuc-an-cho-cho">Thức ăn cho chó</option>
                <option value="thuc-an-cho-meo">Thức ăn cho mèo</option>
                <option value="do-choi">Đồ chơi</option>
                <option value="phu-kien-ve-sinh">Phụ kiện vệ sinh</option>
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
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
        <?php
        // Sample placeholder row — you will import real data from DB
        $samples = [
            [
                'id'=>1,
                'img'=>'../assets/images/sample-accessory.jpg',
                'category'=>'Thức ăn cho chó',
                'name'=>'Hạt khô vị bò',
                'brand'=>'PetFood',
                'material'=>'Ngũ cốc',
                'size'=>'2kg',
                'description'=>'Dinh dưỡng cao, phù hợp cho chó mọi lứa tuổi',
                'price'=>220000,
                'stock'=>30,
                'status'=>'ACTIVE'
            ]
        ];

        foreach ($samples as $row):
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php if(!empty($row['img'])): ?><img src="<?php echo htmlspecialchars($row['img']); ?>" class="pet-img" alt=""><?php endif; ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['brand']); ?></td>
            <td><?php echo htmlspecialchars($row['material']); ?></td>
            <td><?php echo htmlspecialchars($row['size']); ?></td>
            <td><?php echo nl2br(htmlspecialchars(substr($row['description'],0,120))); ?></td>
            <td><?php echo number_format($row['price'],0,',','.'); ?> đ</td>
            <td><?php echo htmlspecialchars($row['stock']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <a href="index.php?p=sua_phukien&id=<?php echo urlencode($row['id']); ?>" class="btn-edit">Sửa</a>
                <a href="#" class="btn-copy">Chép</a>
                <a href="#" class="btn-delete">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
