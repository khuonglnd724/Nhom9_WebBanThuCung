<?php
session_start();
require_once '../connect.php';

// Kiểm tra xem user đã đăng nhập hay không
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

// Lấy order_id từ URL
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId <= 0) {
    $_SESSION['error_message'] = 'Mã đơn hàng không hợp lệ.';
    header('Location: order-history.php');
    exit;
}

// Kiểm tra đơn hàng có thuộc về user này không và trạng thái có phải PENDING không
$conn->set_charset('utf8mb4');
$sqlCheck = "SELECT id, status FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sqlCheck);

if (!$stmt) {
    $_SESSION['error_message'] = 'Lỗi hệ thống. Vui lòng thử lại sau.';
    header('Location: order-history.php');
    exit;
}

$stmt->bind_param('ii', $orderId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['error_message'] = 'Không tìm thấy đơn hàng hoặc bạn không có quyền hủy đơn hàng này.';
    header('Location: order-history.php');
    exit;
}

if ($order['status'] !== 'PENDING') {
    $_SESSION['error_message'] = 'Chỉ có thể hủy đơn hàng đang ở trạng thái "Chờ xác nhận".';
    header('Location: order-history.php');
    exit;
}

// Cập nhật trạng thái đơn hàng thành CANCELED
$sqlUpdate = "UPDATE orders SET status = 'CANCELED', updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);

if (!$stmtUpdate) {
    $_SESSION['error_message'] = 'Lỗi hệ thống. Vui lòng thử lại sau.';
    header('Location: order-history.php');
    exit;
}

$stmtUpdate->bind_param('i', $orderId);

if ($stmtUpdate->execute()) {
    // Lấy chi tiết đơn hàng để hoàn lại số lượng sản phẩm vào kho
    $sqlItems = "SELECT item_type, item_id, quantity FROM order_details WHERE order_id = ?";
    $stmtItems = $conn->prepare($sqlItems);
    
    if ($stmtItems) {
        $stmtItems->bind_param('i', $orderId);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();
        
        // Hoàn lại số lượng vào kho
        while ($item = $resultItems->fetch_assoc()) {
            $quantity = (int)$item['quantity'];
            
            if ($item['item_type'] === 'PET') {
                $sqlRestorePet = "UPDATE pets SET stock = stock + ? WHERE id = ?";
                $stmtRestore = $conn->prepare($sqlRestorePet);
                if ($stmtRestore) {
                    $stmtRestore->bind_param('ii', $quantity, $item['item_id']);
                    $stmtRestore->execute();
                    $stmtRestore->close();
                }
            } elseif ($item['item_type'] === 'ACCESSORY') {
                $sqlRestoreAcc = "UPDATE accessories SET stock = stock + ? WHERE id = ?";
                $stmtRestore = $conn->prepare($sqlRestoreAcc);
                if ($stmtRestore) {
                    $stmtRestore->bind_param('ii', $quantity, $item['item_id']);
                    $stmtRestore->execute();
                    $stmtRestore->close();
                }
            }
        }
        
        $stmtItems->close();
    }
    
    $_SESSION['success_message'] = 'Đơn hàng đã được hủy thành công. Số lượng sản phẩm đã được hoàn lại vào kho.';
} else {
    $_SESSION['error_message'] = 'Có lỗi xảy ra khi hủy đơn hàng. Vui lòng thử lại sau.';
}

$stmtUpdate->close();
$conn->close();

header('Location: order-history.php');
exit;
?>
