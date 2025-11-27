<?php
require_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=thucung');
    exit;
}

// Lấy dữ liệu từ form
$ten = trim($_POST['ten'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$breed_id = !empty($_POST['breed_id']) ? intval($_POST['breed_id']) : null;
$gioi_tinh = $_POST['gioi_tinh'] ?? 'UNKNOWN';
$age_months = intval($_POST['age_months'] ?? 0);
$color = trim($_POST['color'] ?? '');
$size = $_POST['size'] ?? '';
$gia = floatval($_POST['gia'] ?? 0);
$stock = intval($_POST['stock'] ?? 1);
$status = $_POST['status'] ?? 'AVAILABLE';
$mota = trim($_POST['mota'] ?? '');

// Validate
if (empty($ten) || $category_id <= 0 || $gia <= 0) {
    die('Vui lòng điền đầy đủ thông tin bắt buộc!');
}

// Xử lý upload ảnh
$image_url = null;
if (isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_ext = strtolower(pathinfo($_FILES['anh']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
        die('Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif, webp');
    }
    
    if ($_FILES['anh']['size'] > 2 * 1024 * 1024) {
        die('Kích thước file không được vượt quá 2MB');
    }
    
    // Chọn thư mục con theo category: 1=dog, 2=cat (mặc định dog)
    $subdir = ($category_id === 2) ? 'cat' : 'dog';
    $upload_dir = __DIR__ . '/../assets/images/pets/' . $subdir . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['anh']['tmp_name'], $upload_path)) {
        $image_url = '/assets/images/pets/' . $subdir . '/' . $filename;
    }
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Insert vào bảng pets
    $sql = "INSERT INTO pets (category_id, name, breed_id, gender, age_months, color, size, description, price, stock, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isisssssdis', $category_id, $ten, $breed_id, $gioi_tinh, $age_months, $color, $size, $mota, $gia, $stock, $status);
    
    if (!$stmt->execute()) {
        throw new Exception('Lỗi khi thêm thú cưng: ' . $stmt->error);
    }
    
    $pet_id = $conn->insert_id;
    $stmt->close();
    
    // Insert ảnh vào bảng images nếu có
    if ($image_url) {
        $img_sql = "INSERT INTO images (item_type, item_id, image_url, display_order, is_primary) VALUES ('pet', ?, ?, 1, 1)";
        $img_stmt = $conn->prepare($img_sql);
        $img_stmt->bind_param('is', $pet_id, $image_url);
        
        if (!$img_stmt->execute()) {
            throw new Exception('Lỗi khi lưu ảnh: ' . $img_stmt->error);
        }
        
        $img_stmt->close();
    }
    
    $conn->commit();
    
    // Chuyển về trang danh sách
    header('Location: index.php?p=thucung&msg=add_success');
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    die('Lỗi: ' . $e->getMessage());
}
?>
