<?php
require_once __DIR__ . '/auth.php';
// Update accessory handler: use connect.php (project uses connect.php),
// validate input, use prepared statements and safe image upload.
require_once __DIR__ . '/../connect.php';

// Helper: create URL-friendly slug from a name
function make_slug($str) {
    $str = trim($str);
    // Try transliteration
    $slug = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
    if ($slug === false || $slug === null) {
        $slug = $str; // fallback
    }
    $slug = strtolower($slug);
    // Replace non-alphanumeric with hyphens
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    // Trim hyphens
    $slug = trim($slug, '-');
    // Fallback if empty
    if ($slug === '') {
        $slug = 'cat-' . time();
    }
    return $slug;
}

// Basic checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?p=phukien');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    die('ID không hợp lệ');
}

if (!isset($conn) || $conn === null) {
    die('Database connection not available');
}

// Xử lý danh mục mới nếu được chọn
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
$new_category_name = isset($_POST['new_category_name']) ? trim($_POST['new_category_name']) : '';

if ($category_id === 'new' && !empty($new_category_name)) {
    // Tạo slug và thêm danh mục mới vào database với các cột bắt buộc
    $slug = make_slug($new_category_name);
    $insert_cat_sql = "INSERT INTO categories (name, slug, type, parent_id, created_at) VALUES (?, ?, 'ACCESSORY', NULL, NOW())";
    $cat_stmt = $conn->prepare($insert_cat_sql);
    if (!$cat_stmt) {
        die('Lỗi khởi tạo truy vấn danh mục: ' . $conn->error);
    }
    $cat_stmt->bind_param('ss', $new_category_name, $slug);
    if (!$cat_stmt->execute()) {
        // Nếu trùng slug, thử lấy lại id danh mục đã tồn tại
        $cat_stmt->close();
        $find_sql = "SELECT id FROM categories WHERE slug = ? LIMIT 1";
        $find_stmt = $conn->prepare($find_sql);
        if ($find_stmt) {
            $find_stmt->bind_param('s', $slug);
            $find_stmt->execute();
            $res = $find_stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $category_id = $row['id'] ?? 0;
            $find_stmt->close();
        } else {
            $category_id = 0;
        }
        if ($category_id <= 0) {
            die('Không thể tạo hoặc tìm danh mục. Slug có thể đã tồn tại.');
        }
    } else {
        $category_id = $conn->insert_id;
        $cat_stmt->close();
    }
} else {
    $category_id = (int)$category_id;
}

// Validate category_id exists
if ($category_id <= 0) {
    die('Vui lòng chọn danh mục hợp lệ');
}

// Collect and validate POST data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
$material = isset($_POST['material']) ? trim($_POST['material']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
$stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : 'AVAILABLE';
$is_visible = isset($_POST['is_visible']) ? (int)$_POST['is_visible'] : 1;

// Validate minimal requirements
if ($name === '' || $price < 0 || $stock < 0) {
    die('Dữ liệu không hợp lệ');
}

// Xử lý upload ảnh mới (nếu có)
$new_image_url = null;
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
        $new_image_url = '/assets/images/phukien/' . $filename;
    }
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Update bảng accessories
    $sql = "UPDATE accessories 
            SET category_id = ?, 
                name = ?, 
                brand = ?, 
                material = ?, 
                size = ?, 
                description = ?, 
                price = ?, 
                stock = ?,
                status = ?,
                is_visible = ?,
                updated_at = NOW()
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssssdsii', $category_id, $name, $brand, $material, $size, $description, $price, $stock, $status, $is_visible, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Lỗi khi cập nhật phụ kiện: ' . $stmt->error);
    }
    $stmt->close();
    
    // Cập nhật ảnh nếu có ảnh mới
    if ($new_image_url) {
        // Lấy ảnh cũ để xóa
        $old_img_sql = "SELECT image_url FROM images WHERE item_type = 'accessory' AND item_id = ? AND is_primary = 1";
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
            $update_img_sql = "UPDATE images SET image_url = ? WHERE item_type = 'accessory' AND item_id = ? AND is_primary = 1";
            $update_img_stmt = $conn->prepare($update_img_sql);
            $update_img_stmt->bind_param('si', $new_image_url, $id);
            $update_img_stmt->execute();
            $update_img_stmt->close();
        } else {
            // Insert ảnh mới
            $insert_img_sql = "INSERT INTO images (item_type, item_id, image_url, display_order, is_primary) VALUES ('accessory', ?, ?, 1, 1)";
            $insert_img_stmt = $conn->prepare($insert_img_sql);
            $insert_img_stmt->bind_param('is', $id, $new_image_url);
            $insert_img_stmt->execute();
            $insert_img_stmt->close();
        }
        
        $old_img_stmt->close();
    }
    
    $conn->commit();
    
    // Chuyển về trang danh sách
    header('Location: index.php?p=phukien&msg=update_success');
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    die('Lỗi: ' . $e->getMessage());
}
?>
