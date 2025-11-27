<?php
// Update order status handler
require_once __DIR__ . '/../connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Validate status
$allowed = ['PENDING', 'PAID', 'SHIPPED', 'COMPLETED', 'CANCELED'];
if ($id <= 0 || !in_array($status, $allowed)) {
    header('Location: index.php?p=order_history&user_id=' . $user_id . '&msg=invalid');
    exit;
}

$stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('si', $status, $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected <= 0) {
    if ($user_id > 0) {
        header('Location: index.php?p=order_history&user_id=' . $user_id . '&msg=not_found');
    } else {
        header('Location: index.php?p=donhang&msg=not_found');
    }
    exit;
}

if ($user_id > 0) {
    header('Location: index.php?p=order_history&user_id=' . $user_id . '&msg=updated');
} else {
    header('Location: index.php?p=donhang&msg=updated');
}
exit;
