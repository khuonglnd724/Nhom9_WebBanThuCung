<?php
session_start();
require_once("../../connect.php");

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// GET: Lấy giỏ hàng từ database
if ($method === 'GET') {
    try {
        $sql = "SELECT c.id, c.item_type, c.item_id, c.quantity,
                       CASE 
                         WHEN c.item_type = 'PET' THEN p.name
                         WHEN c.item_type = 'ACCESSORY' THEN a.name
                       END AS name,
                       CASE 
                         WHEN c.item_type = 'PET' THEN p.price
                         WHEN c.item_type = 'ACCESSORY' THEN a.price
                       END AS price,
                       CASE 
                         WHEN c.item_type = 'PET' THEN p.stock
                         WHEN c.item_type = 'ACCESSORY' THEN a.stock
                       END AS stock,
                       CASE 
                         WHEN c.item_type = 'PET' THEN (SELECT image_url FROM images WHERE item_type='PET' AND item_id=p.id ORDER BY is_primary DESC LIMIT 1)
                         WHEN c.item_type = 'ACCESSORY' THEN (SELECT image_url FROM images WHERE item_type='ACCESSORY' AND item_id=a.id ORDER BY is_primary DESC LIMIT 1)
                       END AS image_url
                FROM cart c
                LEFT JOIN pets p ON c.item_type = 'PET' AND c.item_id = p.id
                LEFT JOIN accessories a ON c.item_type = 'ACCESSORY' AND c.item_id = a.id
                WHERE c.user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cart = [];
        while ($row = $result->fetch_assoc()) {
            $cart[] = [
                'id' => ($row['item_type'] === 'PET' ? 'pet-' : 'acc-') . $row['item_id'],
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'qty' => (int)$row['quantity'],
                'stock' => (int)$row['stock'],
                'img' => $row['image_url'] ? ('../../' . $row['image_url']) : '',
                'maxStock' => (int)$row['stock']
            ];
        }
        
        echo json_encode(['success' => true, 'cart' => $cart]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// POST: Đồng bộ giỏ hàng từ localStorage lên database
elseif ($method === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['cart']) || !is_array($data['cart'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit();
    }
    
    try {
        $conn->begin_transaction();
        
        // Xóa giỏ hàng cũ
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Thêm giỏ hàng mới
        $stmt = $conn->prepare("INSERT INTO cart (user_id, item_type, item_id, quantity) VALUES (?, ?, ?, ?)");
        
        foreach ($data['cart'] as $item) {
            // Parse item id: "pet-1" hoặc "acc-1"
            $id_parts = explode('-', $item['id']);
            if (count($id_parts) !== 2) continue;
            
            $item_type = ($id_parts[0] === 'pet') ? 'PET' : 'ACCESSORY';
            $item_id = (int)$id_parts[1];
            $quantity = (int)$item['qty'];
            
            $stmt->bind_param("isii", $user_id, $item_type, $item_id, $quantity);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đồng bộ giỏ hàng thành công']);
        
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// DELETE: Xóa một item khỏi giỏ hàng
elseif ($method === 'DELETE') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
        exit();
    }
    
    try {
        // Parse item id
        $id_parts = explode('-', $data['id']);
        if (count($id_parts) !== 2) {
            throw new Exception('ID không hợp lệ');
        }
        
        $item_type = ($id_parts[0] === 'pet') ? 'PET' : 'ACCESSORY';
        $item_id = (int)$id_parts[1];
        
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND item_type = ? AND item_id = ?");
        $stmt->bind_param("isi", $user_id, $item_type, $item_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Đã xóa sản phẩm']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();
?>
