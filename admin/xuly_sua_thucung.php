<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=thucung');
    exit;
}

// Lấy dữ liệu từ form
$id = intval($_POST['id'] ?? 0);
$ten = trim($_POST['ten'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$breed_id = !empty($_POST['breed_id']) ? intval($_POST['breed_id']) : null;
$gioi_tinh = $_POST['gioi_tinh'] ?? 'UNKNOWN';
$age_months = intval($_POST['age_months'] ?? 0);
$color = trim($_POST['color'] ?? '');
$size = $_POST['size'] ?? '';
$gia = floatval($_POST['gia'] ?? 0);
$stock = intval($_POST['stock'] ?? 1);
$is_visible = isset($_POST['is_visible']) ? intval($_POST['is_visible']) : 1;
$mota = trim($_POST['mota'] ?? '');

// Validate
if ($id <= 0 || empty($ten) || $category_id <= 0 || $gia <= 0) {
    die('Vui lòng điền đầy đủ thông tin bắt buộc!');
}

// Kiểm tra pet có tồn tại không
$check_sql = "SELECT id FROM pets WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
if ($check_result->num_rows === 0) {
    die('Thú cưng không tồn tại!');
}
$check_stmt->close();

// Xử lý upload ảnh mới (nếu có)
$new_image_url = null;
if (isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_ext = strtolower(pathinfo($_FILES['anh']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
        die('Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif, webp');
    }
    
    if ($_FILES['anh']['size'] > 2 * 1024 * 1024) {
        die('Kích thước file không được vượt quá 2MB');
    }
    
    // Lấy category_id hiện tại của pet để xác định thư mục con
    $cat_stmt = $conn->prepare("SELECT category_id FROM pets WHERE id = ?");
    $cat_stmt->bind_param('i', $id);
    $cat_stmt->execute();
    $cat_res = $cat_stmt->get_result();
    $cat_row = $cat_res->fetch_assoc();
    $cat_stmt->close();

    $category_id_current = $cat_row ? (int)$cat_row['category_id'] : 1;
    $subdir = ($category_id_current === 2) ? 'cat' : 'dog';

    $upload_dir = __DIR__ . '/../assets/images/pets/' . $subdir . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = time() . '_' . uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['anh']['tmp_name'], $upload_path)) {
        $new_image_url = '/assets/images/pets/' . $subdir . '/' . $filename;
    }
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Update bảng pets
    $sql = "UPDATE pets 
            SET category_id = ?, 
                name = ?, 
                breed_id = ?, 
                gender = ?, 
                age_months = ?, 
                color = ?, 
                size = ?, 
                description = ?, 
                price = ?, 
                stock = ?, 
                is_visible = ?,
                updated_at = NOW()
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isisssssdiii', $category_id, $ten, $breed_id, $gioi_tinh, $age_months, $color, $size, $mota, $gia, $stock, $is_visible, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Lỗi khi cập nhật thú cưng: ' . $stmt->error);
    }
    $stmt->close();
    
    // Cập nhật ảnh nếu có ảnh mới
    if ($new_image_url) {
        // Lấy ảnh cũ để xóa
        $old_img_sql = "SELECT image_url FROM images WHERE item_type = 'pet' AND item_id = ? AND is_primary = 1";
        $old_img_stmt = $conn->prepare($old_img_sql);
        $old_img_stmt->bind_param('i', $id);
        $old_img_stmt->execute();
        $old_img_result = $old_img_stmt->get_result();
        
        if ($old_img_result->num_rows > 0) {
            $old_img = $old_img_result->fetch_assoc();
            $old_img_path = __DIR__ . '/..' . $old_img['image_url'];
            if (file_exists($old_img_path)) {
                unlink($old_img_path);
            }
            
            // Update ảnh
            $update_img_sql = "UPDATE images SET image_url = ? WHERE item_type = 'pet' AND item_id = ? AND is_primary = 1";
            $update_img_stmt = $conn->prepare($update_img_sql);
            $update_img_stmt->bind_param('si', $new_image_url, $id);
            $update_img_stmt->execute();
            $update_img_stmt->close();
        } else {
            // Insert ảnh mới
            $insert_img_sql = "INSERT INTO images (item_type, item_id, image_url, display_order, is_primary) VALUES ('pet', ?, ?, 1, 1)";
            $insert_img_stmt = $conn->prepare($insert_img_sql);
            $insert_img_stmt->bind_param('is', $id, $new_image_url);
            $insert_img_stmt->execute();
            $insert_img_stmt->close();
        }
        
        $old_img_stmt->close();
    }
    
    $conn->commit();
    
    // Chuyển về trang danh sách
    header('Location: index.php?p=thucung&msg=update_success');
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    die('Lỗi: ' . $e->getMessage());
}
?>
