<?php
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index để load layout và CSS chung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=thucung');
    exit;
}

// Lấy tham số tìm kiếm từ URL
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$loai_filter = isset($_GET['loai']) ? $_GET['loai'] : 'all';

// Dữ liệu mẫu (sau này có thể lấy từ CSDL)
$pets = [
    ['id'=>1,'img'=>'../assets/images/poodle.jpg','name'=>'Milu','loai'=>'Chó','giong'=>'Poodle','gia'=>'2,500,000 đ'],
    ['id'=>2,'img'=>'../assets/images/aln.jpg','name'=>'MiuMiu','loai'=>'Mèo','giong'=>'Anh lông ngắn','gia'=>'1,500,000 đ'],
    // bạn có thể thêm mục mới ở đây hoặc thay bằng truy vấn DB
];

// Lọc theo tên và loại
$filtered = array_filter($pets, function($p) use ($q, $loai_filter) {
    if ($loai_filter !== 'all' && $p['loai'] !== $loai_filter) return false;
    if ($q !== '' && stripos($p['name'], $q) === false) return false;
    return true;
});
?>

<h2 class="page-title">Danh Sách Thú Cưng</h2>

<div class="top-bar">
    <form method="get" action="index.php" class="search-filter-form">
        <input type="hidden" name="p" value="thucung">
        
        <div class="input-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Tìm theo tên..." class="search-input" />
            <i class="fas fa-search search-icon"></i>
        </div>
        
        <select name="loai" class="filter-select">
            <option value="all"<?php echo $loai_filter==='all' ? ' selected' : ''; ?>>Tất cả</option>
            <option value="Chó"<?php echo $loai_filter==='Chó' ? ' selected' : ''; ?>>Chó</option>
            <option value="Mèo"<?php echo $loai_filter==='Mèo' ? ' selected' : ''; ?>>Mèo</option>
        </select>
        
        <button type="submit" class="btn-submit btn-filter-standalone">
            <i class="fas fa-filter" aria-hidden="true"></i>
            <span style="margin-left:6px;">Lọc</span>
        </button>
    </form>
    
    <a href="index.php?p=them_thucung" class="btn-add">
        <i class="fas fa-plus-circle"></i> Thêm Thú Cưng
    </a>
</div>

<table class="pet-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Category ID</th>
            <th>Tên</th>
            <th>Giống</th>
            <th>Giới tính</th>
            <th>Tuổi (tháng)</th>
            <th>Màu</th>
            <th>Kích thước</th>
            <th>Mô tả</th>
            <th>Giá</th>
            <th>Stock</th>
            <th>Trạng thái</th>
            <th>Tạo lúc</th>
            <th>Cập nhật</th>
            <th>Hành động</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($filtered)): ?>
            <tr><td colspan="16">Không tìm thấy kết quả.</td></tr>
        <?php else: ?>
            <?php foreach ($filtered as $pet): ?>
                <tr>
                    <td><?php echo isset($pet['id']) ? htmlspecialchars($pet['id']) : ''; ?></td>
                    <td>
                        <?php
                        $img = '';
                        if (!empty($pet['URLImage'])) $img = $pet['URLImage'];
                        elseif (!empty($pet['img'])) $img = $pet['img'];
                        if ($img):
                        ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="pet-img" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?php echo isset($pet['category_id']) ? htmlspecialchars($pet['category_id']) : (isset($pet['category_name'])?htmlspecialchars($pet['category_name']):''); ?></td>
                    <td><?php echo isset($pet['name']) ? htmlspecialchars($pet['name']) : ''; ?></td>
                    <td><?php echo isset($pet['breed']) ? htmlspecialchars($pet['breed']) : (isset($pet['breed_name'])?htmlspecialchars($pet['breed_name']):(isset($pet['giong'])?htmlspecialchars($pet['giong']):'')); ?></td>
                    <td><?php echo isset($pet['gender']) ? htmlspecialchars($pet['gender']) : (isset($pet['gioi_tinh'])?htmlspecialchars($pet['gioi_tinh']):''); ?></td>
                    <td><?php echo isset($pet['age_months']) ? htmlspecialchars($pet['age_months']) : (isset($pet['age'])?htmlspecialchars($pet['age']):''); ?></td>
                    <td><?php echo isset($pet['color']) ? htmlspecialchars($pet['color']) : ''; ?></td>
                    <td><?php echo isset($pet['size']) ? htmlspecialchars($pet['size']) : ''; ?></td>
                    <td><?php echo isset($pet['description']) ? nl2br(htmlspecialchars(substr($pet['description'],0,120))) : ''; ?></td>
                    <td>
                        <?php
                        if (isset($pet['price']) && is_numeric($pet['price'])) echo number_format($pet['price'],0,',','.');
                        else echo isset($pet['gia'])?htmlspecialchars($pet['gia']):'';
                        ?>
                    </td>
                    <td><?php echo isset($pet['stock']) ? htmlspecialchars($pet['stock']) : ''; ?></td>
                    <td><?php echo isset($pet['status']) ? htmlspecialchars($pet['status']) : ''; ?></td>
                    <td><?php echo isset($pet['created_at']) ? htmlspecialchars($pet['created_at']) : ''; ?></td>
                    <td><?php echo isset($pet['updated_at']) ? htmlspecialchars($pet['updated_at']) : ''; ?></td>
                    <td>
                        <a href="index.php?p=sua_thucung&id=<?php echo urlencode(isset($pet['id'])?$pet['id']:''); ?>" class="btn-edit">Sửa</a>
                        <a href="#" class="btn-delete">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
