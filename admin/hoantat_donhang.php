<?php
require_once __DIR__ . '/auth.php';
// Mark order as DELIVERED
require_once __DIR__ . '/../connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php?p=donhang&msg=invalid');
    exit;
}

$stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$status = 'DELIVERED';
$stmt->bind_param('si', $status, $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected <= 0) {
    header('Location: index.php?p=donhang&msg=not_found');
    exit;
}

// Redirect back to order history if user_id is passed, else to order list
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($user_id > 0) {
    header('Location: index.php?p=order_history&user_id=' . $user_id . '&msg=delivered');
} else {
    header('Location: index.php?p=donhang&msg=delivered');
}
exit;
