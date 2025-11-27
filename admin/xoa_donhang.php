<?php
// Redirect guard: allow only via index routing? Here we permit direct as action
require_once __DIR__ . '/../connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php?p=donhang&msg=invalid');
    exit;
}

$conn->begin_transaction();
try {
    // Delete order_details first
    $stmt = $conn->prepare('DELETE FROM order_details WHERE order_id = ?');
    if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Delete the order itself
    $stmt2 = $conn->prepare('DELETE FROM orders WHERE id = ?');
    if (!$stmt2) throw new Exception('Prepare failed: ' . $conn->error);
    $stmt2->bind_param('i', $id);
    $stmt2->execute();
    $affected = $stmt2->affected_rows;
    $stmt2->close();

    if ($affected <= 0) {
        throw new Exception('No order deleted');
    }

    $conn->commit();
    header('Location: index.php?p=donhang&msg=deleted');
    exit;
} catch (Exception $e) {
    $conn->rollback();
    // Optionally log $e->getMessage()
    header('Location: index.php?p=donhang&msg=error');
    exit;
}
