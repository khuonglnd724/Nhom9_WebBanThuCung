<?php
// Update accessory handler: use connect.php (project uses connect.php),
// validate input, use prepared statements and safe image upload.
require_once __DIR__ . '/../connect.php';

// Basic checks
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?p=phukien');
    exit;
}
$id = (int) $_GET['id'];

if (!isset($conn) || $conn === null) {
    die('Database connection not available');
}

// Collect and validate POST data
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
$material = isset($_POST['material']) ? trim($_POST['material']) : '';
$size = isset($_POST['size']) ? trim($_POST['size']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
$stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

// Validate minimal requirements
if ($name === '' || $price < 0 || $stock < 0) {
    // invalid input — redirect back or show error (simple redirect)
    header('Location: index.php?p=phukien');
    exit;
}

// Handle image upload safely if provided
$imgSql = '';
if (!empty($_FILES['image']['name'])) {
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    $uploadName = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($uploadName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt) || $_FILES['image']['size'] > $maxSize) {
        // invalid file — ignore upload
        $uploadName = '';
    } else {
        $newImg = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $uploadName);
        $target = __DIR__ . '/../assets/images/' . $newImg;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Optionally: remove old image from disk (not implemented)
            $imgSql = $newImg;
        }
    }
}

// Prepare update statement
if ($imgSql !== '') {
    $stmt = $conn->prepare("UPDATE accessories SET category_id=?, name=?, brand=?, material=?, size=?, description=?, price=?, stock=?, image=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param('issssssidi', $category_id, $name, $brand, $material, $size, $description, $price, $stock, $imgSql, $id);
} else {
    $stmt = $conn->prepare("UPDATE accessories SET category_id=?, name=?, brand=?, material=?, size=?, description=?, price=?, stock=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param('isssssidii', $category_id, $name, $brand, $material, $size, $description, $price, $stock, $id);
}

$ok = $stmt->execute();
$stmt->close();

// Redirect back to accessories list
header('Location: index.php?p=phukien');
exit;
?>
