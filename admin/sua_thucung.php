<?php
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index để load layout và CSS chung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=sua_thucung&id=' . urlencode(isset($_GET['id'])?$_GET['id']:'') );
    exit;
}

// Kết nối database
require_once __DIR__ . '/../connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die('ID không hợp lệ');
}

// Lấy thông tin thú cưng
$sql = "SELECT p.*, b.name AS breed_name, img.image_url 
        FROM pets p 
        LEFT JOIN breeds b ON p.breed_id = b.id
        LEFT JOIN images img ON img.item_id = p.id AND img.item_type = 'pet' AND img.is_primary = 1
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$tc = $result->fetch_assoc();
$stmt->close();

if (!$tc) {
    die('Không tìm thấy thú cưng');
}

// Lấy danh sách giống
$breeds_query = "SELECT id, name, pet_type, description FROM breeds ORDER BY name ASC";
$breeds_result = $conn->query($breeds_query);
$breeds = [];
if ($breeds_result) {
    while ($row = $breeds_result->fetch_assoc()) {
        $breeds[] = $row;
    }
}
?>

<h2 class="page-title">Sửa Thú Cưng</h2>

<form action="xuly_sua_thucung.php" method="POST" enctype="multipart/form-data" class="pet-form">
    <input type="hidden" name="id" value="<?= htmlspecialchars($tc['id']) ?>">

    <div>
        <label for="ten">Tên:</label>
        <input type="text" id="ten" name="ten" value="<?= htmlspecialchars($tc['name']) ?>" required>
    </div>

    <div>
        <label for="category_id">Loại:</label>
        <select id="category_id" name="category_id" required onchange="filterBreeds(this.value)">
            <option value="">-- Chọn loại --</option>
            <option value="1"<?= $tc['category_id']==1 ? ' selected' : '' ?>>Chó</option>
            <option value="2"<?= $tc['category_id']==2 ? ' selected' : '' ?>>Mèo</option>
        </select>
    </div>

    <div>
        <label for="breed_id">Giống:</label>
        <select id="breed_id" name="breed_id">
            <option value="">-- Chọn giống (không bắt buộc) --</option>
            <?php foreach ($breeds as $breed): ?>
                <option value="<?php echo $breed['id']; ?>" 
                        data-pet-type="<?php echo strtolower($breed['pet_type']); ?>"
                        title="<?php echo htmlspecialchars($breed['description'] ?? ''); ?>"
                        <?php echo ($tc['breed_id'] == $breed['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($breed['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label>Ảnh hiện tại:</label>
        <br>
        <?php if (!empty($tc['image_url'])): ?>
            <img src="<?= htmlspecialchars($tc['image_url']) ?>" class="pet-img" alt="">
        <?php else: ?>
            <p>Chưa có ảnh</p>
        <?php endif; ?>
    </div>

    <div>
        <label for="gioi_tinh">Giới tính:</label>
        <select id="gioi_tinh" name="gioi_tinh" required>
            <option value="MALE"<?= $tc['gender']==='MALE' ? ' selected' : '' ?>>Nam</option>
            <option value="FEMALE"<?= $tc['gender']==='FEMALE' ? ' selected' : '' ?>>Nữ</option>
            <option value="UNKNOWN"<?= $tc['gender']==='UNKNOWN' ? ' selected' : '' ?>>Không rõ</option>
        </select>
    </div>

    <div>
        <label for="age_months">Tuổi (tháng):</label>
        <input type="number" id="age_months" name="age_months" min="0" value="<?= htmlspecialchars($tc['age_months']) ?>">
    </div>

    <div>
        <label for="color">Màu:</label>
        <input type="text" id="color" name="color" value="<?= htmlspecialchars($tc['color']) ?>">
    </div>

    <div>
        <label for="size">Kích thước:</label>
        <select id="size" name="size">
            <option value="Nhỏ"<?= $tc['size']==='Nhỏ' ? ' selected' : '' ?>>Nhỏ</option>
            <option value="Trung bình"<?= $tc['size']==='Trung bình' ? ' selected' : '' ?>>Trung bình</option>
            <option value="Lớn"<?= $tc['size']==='Lớn' ? ' selected' : '' ?>>Lớn</option>
        </select>
    </div>

    <div>
        <label for="gia">Giá:</label>
        <input type="number" id="gia" name="gia" value="<?= htmlspecialchars($tc['price']) ?>" step="0.01" required>
    </div>

    <div>
        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($tc['stock']) ?>">
    </div>

    <div>
        <label for="status">Trạng thái:</label>
        <select id="status" name="status">
            <option value="AVAILABLE"<?= $tc['status']==='AVAILABLE' ? ' selected' : '' ?>>AVAILABLE</option>
            <option value="SOLD"<?= $tc['status']==='SOLD' ? ' selected' : '' ?>>SOLD</option>
            <option value="HIDDEN"<?= $tc['status']==='HIDDEN' ? ' selected' : '' ?>>HIDDEN</option>
        </select>
    </div>

    <div>
        <label for="anh">Đổi ảnh mới (nếu có):</label>
        <input type="file" id="anh" name="anh" accept="image/*">
    </div>

    <div>
        <label for="mota">Mô tả:</label>
        <textarea id="mota" name="mota" rows="5"><?= htmlspecialchars($tc['description']) ?></textarea>
    </div>

    <button type="submit" class="btn-save">Cập nhật</button>
</form>

<script>
function filterBreeds(categoryId) {
    const breedSelect = document.getElementById('breed_id');
    const options = breedSelect.querySelectorAll('option');
    
    // Map category_id to pet_type
    const petTypeMap = {
        '1': 'dog',
        '2': 'cat'
    };
    const petType = petTypeMap[categoryId];
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const optionPetType = option.getAttribute('data-pet-type');
        if (!categoryId || !petType || optionPetType === petType) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Initialize filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect.value) {
        filterBreeds(categorySelect.value);
    }
});
</script>
