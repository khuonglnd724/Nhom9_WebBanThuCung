<?php
// Secure delete accessory handler
require_once __DIR__ . '/../connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?p=phukien');
    exit;
}
$id = (int) $_GET['id'];

if (!isset($conn) || $conn === null) {
    die('DB connection not available');
}

// Fetch current image name
$stmt = $conn->prepare('SELECT image FROM accessories WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if ($row && !empty($row['image'])) {
    $file = __DIR__ . '/../assets/images/' . $row['image'];
    if (file_exists($file)) {
        @unlink($file);
    }
}

// Delete record
$stmt2 = $conn->prepare('DELETE FROM accessories WHERE id = ?');
$stmt2->bind_param('i', $id);
$stmt2->execute();
$stmt2->close();

header('Location: index.php?p=phukien');
exit;
?>
