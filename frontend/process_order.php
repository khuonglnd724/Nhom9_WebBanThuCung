<?php
session_start();
require_once("../connect.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

// Lấy dữ liệu JSON từ request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'debug' => $input]);
    exit();
}

// Validate dữ liệu
$required = ['fullName', 'email', 'phone', 'address', 'city', 'payment', 'items'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Thiếu thông tin: $field"]);
        exit();
    }
}

if (empty($data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
    exit();
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    $user_id = $_SESSION['user_id'];
    $order_code = 'ORD' . time() . rand(100, 999);
    
    // Map payment method từ frontend sang database enum
    $payment_map = [
        'cod' => 'COD',
        'transfer' => 'BANK',
        'card' => 'BANK'
    ];
    $payment_method = $payment_map[$data['payment']] ?? 'COD';
    
    // Tạo shipping address string
    $shipping_address = $data['address'] . ', ' . $data['city'];
    
    $notes = isset($data['notes']) ? $data['notes'] : '';
    
    // Tính tổng tiền từ items + phí ship
    $shipping_fee = 30000;
    $subtotal = 0;
    
    foreach ($data['items'] as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }
    
    $total_amount = $subtotal + $shipping_fee;
    
    // Insert vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_code, total_amount, payment_method, shipping_address, phone, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssss", $user_id, $order_code, $total_amount, $payment_method, $shipping_address, $data['phone'], $notes);
    
    if (!$stmt->execute()) {
        throw new Exception("Lỗi tạo đơn hàng: " . $stmt->error);
    }
    
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // Insert vào bảng order_details
    $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, item_type, item_id, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($data['items'] as $item) {
        // Parse item id: "pet-1" hoặc "acc-1"
        $id_parts = explode('-', $item['id']);
        if (count($id_parts) !== 2) {
            throw new Exception("ID sản phẩm không hợp lệ: " . $item['id']);
        }
        
        $item_type = ($id_parts[0] === 'pet') ? 'PET' : 'ACCESSORY';
        $item_id = (int)$id_parts[1];
        $quantity = (int)$item['qty'];
        $unit_price = (float)$item['price'];
        $line_total = $unit_price * $quantity;
        
        $stmt_detail->bind_param("isiidd", $order_id, $item_type, $item_id, $quantity, $unit_price, $line_total);
        
        if (!$stmt_detail->execute()) {
            throw new Exception("Lỗi thêm chi tiết đơn hàng: " . $stmt_detail->error);
        }
        
        // Giảm stock của sản phẩm
        if ($item_type === 'PET') {
            $update_stock = $conn->prepare("UPDATE pets SET stock = stock - ? WHERE id = ? AND stock >= ?");
        } else {
            $update_stock = $conn->prepare("UPDATE accessories SET stock = stock - ? WHERE id = ? AND stock >= ?");
        }
        
        $update_stock->bind_param("iii", $quantity, $item_id, $quantity);
        
        if (!$update_stock->execute()) {
            throw new Exception("Lỗi cập nhật tồn kho");
        }
        
        if ($update_stock->affected_rows === 0) {
            throw new Exception("Không đủ tồn kho cho sản phẩm " . $item['name']);
        }
        
        $update_stock->close();
    }
    
    $stmt_detail->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đặt hàng thành công',
        'order_code' => $order_code,
        'order_id' => $order_id
    ]);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}

$conn->close();
?>
