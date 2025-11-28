<?php
require_once __DIR__ . '/auth.php';
// Update order status handler
require_once __DIR__ . '/../connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Validate status
$allowed = ['PENDING', 'CONFIRMED', 'SHIPPED', 'COMPLETED', 'CANCELED'];
if ($id <= 0 || !in_array($status, $allowed)) {
    header('Location: index.php?p=order_history&user_id=' . $user_id . '&msg=invalid');
    exit;
}

// Nếu chuyển sang CANCELED, cần hoàn lại số lượng sản phẩm vào kho
if ($status === 'CANCELED') {
    // Lấy trạng thái hiện tại của đơn hàng
    $stmtCheck = $conn->prepare('SELECT status FROM orders WHERE id = ?');
    if ($stmtCheck) {
        $stmtCheck->bind_param('i', $id);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $currentOrder = $resultCheck->fetch_assoc();
        $stmtCheck->close();
        
        // Chỉ hoàn lại hàng nếu đơn hàng chưa bị hủy trước đó
        if ($currentOrder && $currentOrder['status'] !== 'CANCELED') {
            // Lấy chi tiết đơn hàng
            $sqlItems = "SELECT item_type, item_id, quantity FROM order_details WHERE order_id = ?";
            $stmtItems = $conn->prepare($sqlItems);
            
            if ($stmtItems) {
                $stmtItems->bind_param('i', $id);
                $stmtItems->execute();
                $resultItems = $stmtItems->get_result();
                
                // Hoàn lại số lượng vào kho
                while ($item = $resultItems->fetch_assoc()) {
                    $quantity = (int)$item['quantity'];
                    $itemId = (int)$item['item_id'];
                    
                    if ($item['item_type'] === 'PET' || $item['item_type'] === 'pet') {
                        $sqlRestorePet = "UPDATE pets SET stock = stock + ? WHERE id = ?";
                        $stmtRestore = $conn->prepare($sqlRestorePet);
                        if ($stmtRestore) {
                            $stmtRestore->bind_param('ii', $quantity, $itemId);
                            $stmtRestore->execute();
                            $stmtRestore->close();
                        }
                    } elseif ($item['item_type'] === 'ACCESSORY' || $item['item_type'] === 'accessory') {
                        $sqlRestoreAcc = "UPDATE accessories SET stock = stock + ? WHERE id = ?";
                        $stmtRestore = $conn->prepare($sqlRestoreAcc);
                        if ($stmtRestore) {
                            $stmtRestore->bind_param('ii', $quantity, $itemId);
                            $stmtRestore->execute();
                            $stmtRestore->close();
                        }
                    }
                }
                
                $stmtItems->close();
            }
        }
    }
}

// Cập nhật trạng thái đơn hàng
$stmt = $conn->prepare('UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
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
