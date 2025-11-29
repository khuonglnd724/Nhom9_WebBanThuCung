<?php
require_once __DIR__ . '/auth.php';
// Nếu người dùng truy cập trực tiếp file này, chuyển hướng về index để load layout và CSS chung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Location: index.php?p=them_thucung');
    exit;
}

// Kết nối database để lấy danh sách giống
require_once __DIR__ . '/../connect.php';

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

<h2 class="page-title">Thêm Thú Cưng Mới</h2>

<form action="xuly_them_thucung.php" method="POST" enctype="multipart/form-data" class="pet-form">

    <div>
        <label for="ten">Tên thú cưng:</label>
        <input type="text" id="ten" name="ten" required>
    </div>

    <div>
        <label for="category_id">Loại:</label>
        <select id="category_id" name="category_id" required onchange="filterBreeds(this.value)">
            <option value="">-- Chọn loại --</option>
            <option value="1">Chó</option>
            <option value="2">Mèo</option>
        </select>
    </div>

    <div>
        <label for="breed_id">Giống:</label>
        <select id="breed_id" name="breed_id">
            <option value="">-- Chọn giống (không bắt buộc) --</option>
            <?php foreach ($breeds as $breed): ?>
                <option value="<?php echo $breed['id']; ?>" data-pet-type="<?php echo strtolower($breed['pet_type']); ?>" title="<?php echo htmlspecialchars($breed['description'] ?? ''); ?>">
                    <?php echo htmlspecialchars($breed['name']); ?>
                </option>
            <?php endforeach; ?>
            <option value="new">+ Thêm giống mới</option>
        </select>
    </div>

    <div id="new-breed-section" style="display:none; padding:12px; border:1px solid #ddd; border-radius:6px; background:#f8f9fa; margin-bottom:16px;">
        <h4 style="margin:0 0 12px 0;">Thông tin giống mới</h4>
        <div style="margin-bottom:12px;">
            <label for="new_breed_name">Tên giống mới:</label>
            <input type="text" id="new_breed_name" name="new_breed_name" placeholder="Ví dụ: Golden Retriever">
        </div>
        <div>
            <label for="new_breed_description">Mô tả giống:</label>
            <textarea id="new_breed_description" name="new_breed_description" rows="3" placeholder="Mô tả ngắn về giống này..."></textarea>
        </div>
    </div>

    <div>
        <label for="gioi_tinh">Giới tính:</label>
        <select id="gioi_tinh" name="gioi_tinh" required>
            <option value="MALE">Đực</option>
            <option value="FEMALE">Cái</option>
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
        <label>
            <input type="checkbox" name="is_invisible" value="1">
            Ẩn sản phẩm sau khi tạo
        </label>
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
        if (option.value === '' || option.value === 'new') {
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
    
    breedSelect.value = '';
    toggleNewBreedSection();
}

function toggleNewBreedSection() {
    const breedSelect = document.getElementById('breed_id');
    const newBreedSection = document.getElementById('new-breed-section');
    
    if (breedSelect.value === 'new') {
        newBreedSection.style.display = 'block';
    } else {
        newBreedSection.style.display = 'none';
    }
}

// Initialize filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const breedSelect = document.getElementById('breed_id');
    
    breedSelect.addEventListener('change', toggleNewBreedSection);
    
    if (categorySelect.value) {
        filterBreeds(categorySelect.value);
    }
});
</script>
