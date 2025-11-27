<?php
require_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=phukien');
    exit;
}

// Xử lý danh mục mới nếu được chọn
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
$new_category_name = isset($_POST['new_category_name']) ? trim($_POST['new_category_name']) : '';

if ($category_id === 'new' && !empty($new_category_name)) {
    // Thêm danh mục mới vào database
    $insert_cat_sql = "INSERT INTO categories (name, created_at) VALUES (?, NOW())";
    $cat_stmt = $conn->prepare($insert_cat_sql);
    $cat_stmt->bind_param('s', $new_category_name);
    $cat_stmt->execute();
    $category_id = $conn->insert_id;
    $cat_stmt->close();
} else {
    $category_id = (int)$category_id;
}

// Lấy dữ liệu từ form
$name = trim($_POST['name'] ?? '');
$type = trim($_POST['type'] ?? 'ACCESSORY');
$brand = trim($_POST['brand'] ?? '');
$material = trim($_POST['material'] ?? '');
$size = trim($_POST['size'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$stock = intval($_POST['stock'] ?? 1);
$status = $_POST['status'] ?? 'AVAILABLE';

// Validate
if (empty($name) || $category_id <= 0 || $price <= 0) {
    die('Vui lòng điền đầy đủ thông tin bắt buộc!');
}

// Xử lý upload ảnh
$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
        die('Chỉ chấp nhận file ảnh: jpg, jpeg, png, gif, webp');
    }
    
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        die('Kích thước file không được vượt quá 2MB');
    }
    
    $upload_dir = __DIR__ . '/../assets/images/phukien/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = time() . '_' . uniqid() . '.' . $file_ext;  
    $upload_path = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        $image_url = '/assets/images/phukien/' . $filename;
    } else {
        die('Lỗi khi upload file ảnh');
    }
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Insert vào bảng accessories
    $sql = "INSERT INTO accessories (category_id, name, type, brand, material, size, description, price, stock, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssssdis', $category_id, $name, $type, $brand, $material, $size, $description, $price, $stock, $status);
    
    if (!$stmt->execute()) {
        throw new Exception('Lỗi khi thêm phụ kiện: ' . $stmt->error);
    }
    
    $accessory_id = $conn->insert_id;
    $stmt->close();
    
    // Insert ảnh vào bảng images nếu có
    if ($image_url) {
        $img_sql = "INSERT INTO images (item_type, item_id, image_url, display_order, is_primary) VALUES ('accessory', ?, ?, 1, 1)";
        $img_stmt = $conn->prepare($img_sql);
        $img_stmt->bind_param('is', $accessory_id, $image_url);
        
        if (!$img_stmt->execute()) {
            throw new Exception('Lỗi khi lưu ảnh: ' . $img_stmt->error);
        }
        
        $img_stmt->close();
    }
    
    $conn->commit();
    
    // Chuyển về trang danh sách
    header('Location: index.php?p=phukien&msg=add_success');
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    die('Lỗi: ' . $e->getMessage());
}
?>
