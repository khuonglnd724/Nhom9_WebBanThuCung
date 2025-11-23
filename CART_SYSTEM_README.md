# Hệ thống Giỏ Hàng & Đặt Hàng - StarryPets

## Tính Năng Chính

### 1. **Giỏ Hàng (Add, Remove, Update)**
- **Thêm sản phẩm**: Click nút "Mua hàng" trên trang sản phẩm
- **Xem giỏ hàng**: Click icon giỏ hàng ở header hoặc vào trang `/frontend/cart.html`
- **Cập nhật số lượng**: 
  - Click nút "+" để tăng số lượng
  - Click nút "−" để giảm số lượng
  - Số lượng tự động xóa sản phẩm nếu giảm về 0
- **Xóa sản phẩm**: Click nút "X" để xóa sản phẩm khỏi giỏ hàng

**Lưu trữ**: Giỏ hàng được lưu trong `localStorage` nên sẽ được giữ lại khi làm mới trang

### 2. **Đặt Hàng (Checkout)**
Quy trình đặt hàng:
1. Click vào giỏ hàng
2. Click nút "Thanh toán" (hoặc vào `/frontend/thanhtoan.html`)
3. Điền thông tin:
   - Họ và tên
   - Email
   - Số điện thoại
   - Địa chỉ
   - Thành phố/Tỉnh
   - Ghi chú (tùy chọn)
4. Chọn phương thức thanh toán:
   - Thanh toán khi nhận hàng (COD)
   - Chuyển khoản ngân hàng
   - Thẻ tín dụng/Ghi nợ
5. Click "ĐẶT HÀNG"
6. Nhận mã đơn hàng (ORD + timestamp)

**Lưu trữ**: Đơn hàng được lưu trong `localStorage` dưới key `"orders"`

### 3. **Theo dõi Đơn Hàng**
- Truy cập `/frontend/order-tracking.html`
- Tìm kiếm đơn hàng bằng mã (ORD...)
- Xem trạng thái và chi tiết đơn hàng
- Trạng thái đơn hàng:
  - Chờ xác nhận (cam)
  - Đã xác nhận (xanh dương)
  - Đang giao (tím)
  - Đã giao (xanh lá)
  - Hủy (đỏ)

### 4. **Quản lý Đơn Hàng (Admin)**
- Truy cập `/admin/donhang.php`
- Xem danh sách tất cả đơn hàng
- Xem thống kê:
  - Tổng số đơn hàng
  - Số đơn chờ xác nhận
  - Số đơn đã giao
  - Doanh thu từ các đơn đã giao
- Xuất danh sách đơn hàng ra CSV

## Các File Liên Quan

### Frontend (Khách hàng)
- `frontend/index.html` - Trang chủ với sản phẩm
- `frontend/cart.html` - Trang giỏ hàng
- `frontend/thanhtoan.html` - Trang thanh toán & đặt hàng
- `frontend/order-tracking.html` - Trang theo dõi đơn hàng

### Backend Assets
- `assets/js/cart.js` - Logic giỏ hàng (add, remove, update)
- `assets/js/orders.js` - Logic quản lý đơn hàng
- `assets/js/script.js` - Script chung
- `assets/css/styles.css` - CSS cho giỏ hàng và checkout

### Admin
- `admin/donhang.php` - Trang quản lý đơn hàng

## Dữ Liệu Lưu Trữ

### localStorage Keys
1. **`cart`** - Giỏ hàng hiện tại
   ```json
   [
     {
       "id": "product-id",
       "name": "Tên sản phẩm",
       "price": 1000000,
       "img": "https://...",
       "qty": 2
     }
   ]
   ```

2. **`orders`** - Danh sách đơn hàng
   ```json
   [
     {
       "id": "ORD1234567890",
       "date": "24/11/2025 10:30:45",
       "customer": {
         "fullName": "Nguyen Van A",
         "email": "user@example.com",
         "phone": "0912345678",
         "address": "123 Nguyen Hue",
         "city": "Ho Chi Minh",
         "notes": "Ghi chú thêm"
       },
       "items": [...],
       "total": 50030000,
       "payment": "cod",
       "status": "Chờ xác nhận"
     }
   ]
   ```

## Sản Phẩm Mẫu

Hệ thống đã có 6 sản phẩm mẫu:
1. GOLDEN ĐẸP TRAI - 15.000.000₫
2. SAMOYED XINH - 14.000.000₫
3. ALASKA XÁM CƯNG - 24.000.000₫
4. BẮC KINH SIÊU BÉO - 7.000.000₫
5. BICHON TRẮNG XINH XINH - 30.000.000₫
6. PHỐC SÓC BÉ XÍU CƯNG XĨU - 20.000.000₫

Có thể thêm sản phẩm mới bằng cách cập nhật `productData` object trong `assets/js/cart.js`

## Cách Sử Dụng (Demo)

### Cho Khách Hàng:
1. Vào trang chủ (index.html)
2. Click "Mua hàng" trên sản phẩm
3. Xem giỏ hàng tại cart.html
4. Đặt hàng tại thanhtoan.html
5. Theo dõi đơn hàng tại order-tracking.html

### Cho Admin:
1. Vào /admin/donhang.php
2. Xem danh sách đơn hàng và thống kê
3. Xuất CSV nếu cần

## Ghi Chú

- Hệ thống sử dụng `localStorage` nên dữ liệu sẽ được giữ lại khi đóng/mở lại trình duyệt
- Phí vận chuyển cố định: 30.000₫
- Chế độ demo: có thể tạo đơn hàng mà không cần xác thực
- Trong tương lai, nên kết nối với database thực thay vì localStorage

## Phát Triển Tiếp Theo

- [ ] Kết nối database MySQL
- [ ] Xác thực đăng nhập/đăng ký
- [ ] Thanh toán online (VNPay, Stripe)
- [ ] Gửi email xác nhận đơn hàng
- [ ] Cập nhật trạng thái đơn hàng tự động
- [ ] Tích hợp tracking vận chuyển
- [ ] Mã khuyến mãi & Voucher
- [ ] Đánh giá sản phẩm
