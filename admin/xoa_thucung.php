<?php
require_once __DIR__ . '/../connect.php';

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die('ID không hợp lệ!');
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

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Lấy thông tin ảnh để xóa file
    $img_sql = "SELECT image_url FROM images WHERE item_type = 'pet' AND item_id = ?";
    $img_stmt = $conn->prepare($img_sql);
    $img_stmt->bind_param('i', $id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    
    // Xóa các file ảnh
    while ($img = $img_result->fetch_assoc()) {
        if (!empty($img['image_url'])) {
            $img_path = __DIR__ . '/..' . $img['image_url'];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
        }
    }
    $img_stmt->close();
    
    // Xóa các ảnh trong bảng images
    $delete_img_sql = "DELETE FROM images WHERE item_type = 'pet' AND item_id = ?";
    $delete_img_stmt = $conn->prepare($delete_img_sql);
    $delete_img_stmt->bind_param('i', $id);
    
    if (!$delete_img_stmt->execute()) {
        throw new Exception('Lỗi khi xóa ảnh: ' . $delete_img_stmt->error);
    }
    $delete_img_stmt->close();
    
    // Xóa thú cưng trong bảng pets
    $delete_pet_sql = "DELETE FROM pets WHERE id = ?";
    $delete_pet_stmt = $conn->prepare($delete_pet_sql);
    $delete_pet_stmt->bind_param('i', $id);
    
    if (!$delete_pet_stmt->execute()) {
        throw new Exception('Lỗi khi xóa thú cưng: ' . $delete_pet_stmt->error);
    }
    $delete_pet_stmt->close();
    
    $conn->commit();
    
    // Chuyển về trang danh sách
    header('Location: index.php?p=thucung&msg=delete_success');
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    die('Lỗi: ' . $e->getMessage());
}
?>
