# 📋 Hệ thống Xác nhận Đơn hàng - StarryPets

## 🎯 Tính năng chính

### 1. **Trang Xác nhận Đơn hàng** (`order-confirmation.php`)
- Hiển thị thông tin đơn hàng sau khi đặt hàng thành công
- Lấy dữ liệu từ bảng `orders` và `order_details` trong database
- Hiển thị:
  - Mã đơn hàng (Order Code)
  - Trạng thái đơn hàng (Status Badge)
  - Thông tin khách hàng (Họ tên, Email, SĐT, Địa chỉ)
  - Phương thức thanh toán
  - Ngày đặt hàng
  - Danh sách sản phẩm (với hình ảnh, tên, số lượng, giá)
  - Tóm tắt tài chính (Tổng số lượng, Tổng tiền)
  - Ghi chú của khách hàng

### 2. **Quy trình sau khi đặt hàng**

#### Bước 1: Điền form thanh toán (`thanhtoan.php`)
- Nhập Họ tên, SĐT, Địa chỉ, Thành phố
- Chọn phương thức thanh toán (COD, Bank Transfer, Credit Card, MOMO)
- Nhập ghi chú (tuỳ chọn)

#### Bước 2: Tạo đơn hàng (`process_order.php`)
- Gửi dữ liệu đơn hàng lên server qua AJAX
- Server xử lý:
  - Tạo order record trong bảng `orders`
  - Tạo order_details cho từng sản phẩm
  - Trả về `order_id` và `order_code`

#### Bước 3: Xác nhận đơn hàng (`order-confirmation.php`)
- Redirect tới: `order-confirmation.php?order_id={id}`
- Hiển thị thông tin đơn hàng vừa tạo
- Cho phép tiếp tục mua sắm hoặc theo dõi đơn hàng

---

## 🗄️ Cấu trúc Database

### Bảng `orders`
```sql
CREATE TABLE orders (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_code VARCHAR(50) UNIQUE,
  user_id INT,
  customer_name VARCHAR(255),
  customer_email VARCHAR(255),
  customer_phone VARCHAR(20),
  total_amount DECIMAL(12,2),
  status ENUM('PENDING','CONFIRMED','SHIPPED','DELIVERED','CANCELLED'),
  payment_method VARCHAR(50),
  shipping_address TEXT,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Bảng `order_details`
```sql
CREATE TABLE order_details (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id INT,
  item_type VARCHAR(20),
  item_id INT,
  quantity INT,
  unit_price DECIMAL(12,2),
  FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

---

## 📝 Cách sử dụng

### Truy cập trang xác nhận đơn hàng
```
http://localhost/wedthucung/Nhom9_WebBanThuCung/frontend/order-confirmation.php?order_id=1
```

### Trạng thái đơn hàng
| Trạng thái | Màu sắc | Mô tả |
|-----------|---------|-------|
| PENDING | Orange (#FF9800) | Chờ xác nhận |
| CONFIRMED | Blue (#2196F3) | Đã xác nhận |
| SHIPPED | Purple (#9C27B0) | Đang giao |
| DELIVERED | Green (#4CAF50) | Đã giao |
| CANCELLED | Red (#F44336) | Đã hủy |

### Phương thức thanh toán
- `COD`: Thanh toán khi nhận hàng
- `BANK`: Chuyển khoản ngân hàng
- `CREDIT`: Thẻ tín dụng/Ghi nợ
- `MOMO`: Ví điện tử MOMO

---

## 🔄 Luồng dữ liệu

```
1. Khách hàng điền form thanh toán (thanhtoan.php)
   ↓
2. Gửi AJAX POST đến process_order.php
   ↓
3. Process_order.php lưu vào database
   ↓
4. Trả về order_id + order_code
   ↓
5. Redirect đến order-confirmation.php?order_id={id}
   ↓
6. Hiển thị thông tin đơn hàng từ database
```

---

## 🎨 Giao diện

### Header
- Thông báo xác nhận ("✓ Đơn hàng được xác nhận!")
- Mã đơn hàng
- Status Badge

### Body
- **Thông tin khách hàng** (2 cột)
  - Họ tên, Email, SĐT
  - Địa chỉ, Phương thức TT, Ngày đặt

- **Chi tiết sản phẩm** (Card layout)
  - Hình ảnh sản phẩm
  - Tên, Số lượng, Giá tiền

- **Tóm tắt tài chính** (Gradient background)
  - Tổng số lượng
  - Tổng tiền
  - Thành tiền

- **Ghi chú** (nếu có)

- **Nút hành động**
  - Tiếp tục mua sắm
  - Theo dõi đơn hàng

---

## 🚀 Tính năng mở rộng

Có thể thêm trong tương lai:
- [ ] Gửi email xác nhận đơn hàng
- [ ] In hoá đơn PDF
- [ ] Hỗ trợ thanh toán online (VNPay, ZaloPay)
- [ ] Theo dõi vận chuyển theo thời gian thực
- [ ] Hủy/Sửa đơn hàng
- [ ] Đánh giá & bình luận sản phẩm

---

## 📞 Hỗ trợ

Nếu có vấn đề, hãy kiểm tra:
1. Database có chứa dữ liệu `orders` và `order_details` không?
2. `order_id` được truyền chính xác trong URL không?
3. File `connect.php` kết nối đúng database không?

