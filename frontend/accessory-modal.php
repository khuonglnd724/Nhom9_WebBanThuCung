<?php
// File: accessory-modal.php
// Xử lý yêu cầu hiển thị modal phụ kiện

// Kết nối cơ sở dữ liệu
require_once '../connect.php';

// Hàm lấy thông tin phụ kiện từ database
function getAccessoryDetails($accessoryId, $conn) {
    $conn->set_charset('utf8mb4');
    $sql = "SELECT a.id, a.name, a.price, a.stock, a.description, 
                   a.brand, a.material, a.size,
                   (SELECT image_url FROM images i 
                    WHERE i.item_type='ACCESSORY' AND i.item_id=a.id 
                    ORDER BY is_primary DESC, display_order ASC, id ASC 
                    LIMIT 1) AS image_url
            FROM accessories a
            WHERE a.id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $accessoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Kiểm tra nếu có yêu cầu lấy sản phẩm
if (isset($_GET['accessory_id'])) {
    // Xử lý accessory_id: có thể là "acc-123" hoặc chỉ "123"
    $accessoryId = $_GET['accessory_id'];
    
    // Nếu dạng "acc-123", lấy số từ sau dấu "-"
    if (strpos($accessoryId, 'acc-') === 0) {
        $accId = intval(substr($accessoryId, 4));
    } else {
        $accId = intval($accessoryId);
    }
    
    $accessory = getAccessoryDetails($accId, $conn);

    if ($accessory) {
        $imageUrl = $accessory['image_url'] ? ('../' . $accessory['image_url']) : ('https://placehold.co/600x500?text=' . rawurlencode($accessory['name']));
        $price = number_format((float)$accessory['price'], 0, ',', '.') . '₫';
        $statusText = 'Hiển thị';
        ?>
        <div id="accessoryModal" class="product-modal" style="display: flex;">
            <div class="product-modal-content">
                <button class="modal-close" aria-label="Close">&times;</button>
                <div class="modal-body">
                    <div class="modal-image">
                        <img id="accModalImage" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($accessory['name']); ?>" />
                    </div>
                    <div class="modal-details">
                        <h2 id="accModalName"><?php echo htmlspecialchars($accessory['name']); ?></h2>
                        <div class="modal-price">
                            <span class="current-price" id="accModalPrice"><?php echo $price; ?></span>
                        </div>
                        <div class="modal-section">
                            <h3>Mô tả</h3>
                            <p id="accModalDescription"><?php echo htmlspecialchars($accessory['description'] ?: 'Chưa có thông tin chi tiết.'); ?></p>
                        </div>
                        <div class="modal-section">
                            <h3>Thông tin chi tiết</h3>
                            <table class="modal-table">
                                <tr>
                                    <td>Thương hiệu:</td>
                                    <td id="accModalBrand"><?php echo htmlspecialchars($accessory['brand'] ?: 'Chưa rõ'); ?></td>
                                </tr>
                                <tr>
                                    <td>Chất liệu:</td>
                                    <td id="accModalMaterial"><?php echo htmlspecialchars($accessory['material'] ?: 'Chưa rõ'); ?></td>
                                </tr>
                                <tr>
                                    <td>Kích cỡ:</td>
                                    <td id="accModalSize"><?php echo htmlspecialchars($accessory['size'] ?: '—'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-actions">
                            <button class="btn btn-primary add-to-cart" data-id="acc-<?php echo (int)$accessory['id']; ?>" data-stock="<?php echo (int)$accessory['stock']; ?>">Thêm vào giỏ</button>
                            <button class="btn btn-outline buy-now" data-id="acc-<?php echo (int)$accessory['id']; ?>" data-stock="<?php echo (int)$accessory['stock']; ?>">Mua ngay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<p>Không tìm thấy phụ kiện này.</p>";
    }
} else {
    echo "<p>Yêu cầu không hợp lệ.</p>";
}
?>