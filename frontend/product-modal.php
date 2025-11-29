<?php
// File: product-modal.php
// Xử lý yêu cầu hiển thị modal sản phẩm

// Kết nối cơ sở dữ liệu
require_once '../connect.php';

// Hàm lấy thông tin thú cưng từ database
function getPetDetails($petId, $conn) {
    $conn->set_charset('utf8mb4');
    $sql = "SELECT p.id, p.name, p.price, p.stock, p.description, 
                   p.breed_id, p.age_months, p.color, p.size, p.gender,
                   b.name AS breed_name,
                   (SELECT image_url FROM images i 
                    WHERE i.item_type='PET' AND i.item_id=p.id 
                    ORDER BY is_primary DESC, display_order ASC, id ASC 
                    LIMIT 1) AS image_url
            FROM pets p
            LEFT JOIN breeds b ON p.breed_id = b.id
            WHERE p.id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $petId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Kiểm tra nếu có yêu cầu lấy sản phẩm
if (isset($_GET['product_id'])) {
    // Xử lý product_id: có thể là "pet-123" hoặc chỉ "123"
    $productId = $_GET['product_id'];
    
    // Nếu dạng "pet-123", lấy số từ sau dấu "-"
    if (strpos($productId, 'pet-') === 0) {
        $petId = intval(substr($productId, 4));
    } else if (strpos($productId, 'acc-') === 0) {
        // Nếu là phụ kiện, xử lý riêng (để sau)
        echo "<p>Chức năng phụ kiện sẽ được cập nhật sớm.</p>";
        exit;
    } else {
        $petId = intval($productId);
    }
    
    $pet = getPetDetails($petId, $conn);

    if ($pet) {
        $imageUrl = $pet['image_url'] ? ('../' . $pet['image_url']) : ('https://placehold.co/600x500?text=' . rawurlencode($pet['name']));
        $price = number_format((float)$pet['price'], 0, ',', '.') . '₫';
            $statusText = 'Hiển thị';
        ?>
        <div id="productModal" class="product-modal" style="display: flex;">
            <div class="product-modal-content">
                <button class="modal-close" aria-label="Close">&times;</button>
                <div class="modal-body">
                    <div class="modal-image">
                        <img id="modalProductImage" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" />
                    </div>
                    <div class="modal-details">
                        <h2 id="modalProductName"><?php echo htmlspecialchars($pet['name']); ?></h2>
                        <div class="modal-price">
                            <span class="current-price" id="modalProductPrice"><?php echo $price; ?></span>
                        </div>
                        <div class="modal-section">
                            <h3>Mô tả sản phẩm</h3>
                            <p id="modalProductDescription"><?php echo htmlspecialchars($pet['description'] ?: 'Chưa có thông tin chi tiết.'); ?></p>
                        </div>
                        <div class="modal-section">
                            <h3>Thông tin chi tiết</h3>
                            <table class="modal-table">
                                <tr>
                                    <td>Giống loài:</td>
                                    <td id="modalProductBreed"><?php echo htmlspecialchars($pet['breed_name'] ?: 'Chưa rõ'); ?></td>
                                </tr>
                                <tr>
                                    <td>Tuổi:</td>
                                    <td id="modalProductAge"><?php echo ($pet['age_months'] ? $pet['age_months'] . ' tháng' : 'Chưa rõ'); ?></td>
                                </tr>
                                <tr>
                                    <td>Giới tính:</td>
                                    <td id="modalProductGender"><?php echo ($pet['gender'] === 'MALE' ? 'Đực' : ($pet['gender'] === 'FEMALE' ? 'Cái' : 'Chưa rõ')); ?></td>
                                </tr>
                                <tr>
                                    <td>Kích cỡ:</td>
                                    <td id="modalProductWeight"><?php echo htmlspecialchars($pet['size'] ?: 'Chưa rõ'); ?></td>
                                </tr>
                                <tr>
                                    <td>Màu sắc:</td>
                                    <td id="modalProductColor"><?php echo htmlspecialchars($pet['color'] ?: 'Chưa rõ'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-section">
                            <h3>Chính sách bán hàng</h3>
                            <ul class="modal-policy">
                                <li>✓ Đảm bảo sức khỏe 100%</li>
                                <li>✓ Giao hàng toàn quốc</li>
                                <li>✓ Hỗ trợ sau bán hàng 24/7</li>
                                <li>✓ Giấy chứng chỉ xuất xứ</li>
                            </ul>
                        </div>
                        <div class="modal-actions">
                            <button class="btn btn-primary add-to-cart" data-id="pet-<?php echo (int)$pet['id']; ?>" data-stock="<?php echo (int)$pet['stock']; ?>">Thêm vào giỏ</button>
                            <button class="btn btn-outline buy-now" data-id="pet-<?php echo (int)$pet['id']; ?>" data-stock="<?php echo (int)$pet['stock']; ?>">Mua ngay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<p>Không tìm thấy sản phẩm.</p>";
    }
} else {
    echo "<p>Yêu cầu không hợp lệ.</p>";
}
?>
