# 📋 Backend TODO List - Những gì cần sửa/hoàn thiện

## ✅ Hiện tại đã có (Frontend):
- ✅ Giao diện đẹp với banner slider, modal product
- ✅ Shopping cart (localStorage)
- ✅ Checkout page (form)
- ✅ Order tracking page
- ✅ Login/Register page (UI đẹp)
- ✅ Database schema + sample data
- ✅ 3 users sample trong DB

---

## 🔴 BACKEND CẦN SỬA/HOÀN THIỆN:

### 1. **Authentication & Sessions** 
- [ ] Implement login logic đúng với form POST
- [ ] Lưu session sau khi login thành công
- [ ] Logout functionality
- [ ] Kiểm tra session trước khi access page
- [ ] Redirect nếu chưa login (redirect sang login.php)
- [ ] Hạn chế thời gian session (30 min inactive)
- [ ] Remember me (optional)

### 2. **Product Management** 
- [ ] API endpoint: GET `/api/products` (lấy danh sách sản phẩm)
- [ ] API endpoint: GET `/api/products/{id}` (lấy chi tiết 1 sản phẩm)
- [ ] API endpoint: GET `/api/pets` (lấy danh sách thú cưng)
- [ ] API endpoint: GET `/api/pets/{id}` (chi tiết thú cưng - cho pet.php)
- [ ] Populate bảng `pets` và `accessories` với đúng dữ liệu
- [ ] Filter by category
- [ ] Search products

### 3. **Shopping Cart** 
- [ ] Backend API: POST `/api/cart/add` (thêm vào cart)
- [ ] Backend API: PUT `/api/cart/update/{item_id}` (cập nhật số lượng)
- [ ] Backend API: DELETE `/api/cart/remove/{item_id}` (xóa khỏi cart)
- [ ] Backend API: GET `/api/cart` (lấy danh sách cart)
- [ ] Lưu cart vào database (nếu user đã login)
- [ ] Restore cart khi user login

### 4. **Checkout & Orders** 
- [ ] Form validation phía backend
- [ ] Tạo order trong database
- [ ] Generate order code duy nhất
- [ ] Lưu order_details (từng sản phẩm trong order)
- [ ] Tính toán total_amount (cart items + shipping fee)
- [ ] Integration với payment gateway (COD, Bank Transfer, VNPAY, MOMO)
- [ ] Payment confirmation logic

### 5. **Order Tracking** 
- [ ] Backend API: GET `/api/orders` (danh sách order của user)
- [ ] Backend API: GET `/api/orders/{order_id}` (chi tiết 1 order)
- [ ] Update order status (PENDING → PAID → SHIPPED → COMPLETED)
- [ ] Gửi email notification cho customer khi order status thay đổi

### 6. **Admin Panel** 
- [ ] Admin dashboard - hiển thị statistics
- [ ] Quản lý orders (view, update status, delete)
- [ ] Quản lý products (CRUD)
- [ ] Quản lý users/customers
- [ ] Export reports (CSV, PDF)

### 7. **Email Notifications** 
- [ ] Welcome email khi user register
- [ ] Order confirmation email
- [ ] Shipping notification email
- [ ] Forgot password - reset link via email
- [ ] Setup SMTP server

### 8. **Security** 
- [ ] Input validation (sanitize all inputs)
- [ ] SQL Injection prevention (use prepared statements - ✅ already done)
- [ ] XSS prevention (htmlspecialchars)
- [ ] CSRF token implementation
- [ ] Password reset functionality
- [ ] Rate limiting (login attempts)
- [ ] HTTPS support

### 9. **Payment Integration** 
- [ ] VNPAY integration
- [ ] MOMO integration
- [ ] Bank transfer webhook handler
- [ ] Payment callback handler

### 10. **Dynamic Pet Page** 
- [ ] pet.php - lấy dữ liệu từ database qua query parameter `?id=X`
- [ ] Hiển thị breed info, price, age, weight, color, status
- [ ] Add to cart button
- [ ] Related products

### 11. **API Endpoints Summary**
```
POST   /api/auth/login
POST   /api/auth/register
POST   /api/auth/logout
GET    /api/auth/me (get current user)

GET    /api/products
GET    /api/products/{id}
GET    /api/categories
GET    /api/categories/{id}/products

GET    /api/pets
GET    /api/pets/{id}

POST   /api/cart/add
PUT    /api/cart/update/{item_id}
DELETE /api/cart/remove/{item_id}
GET    /api/cart

POST   /api/orders
GET    /api/orders
GET    /api/orders/{id}
PUT    /api/orders/{id}/status

POST   /api/payment/confirm
GET    /api/payment/vnpay-return
POST   /api/payment/momo-callback
```

---

## 📊 Database Schema - Cần populate:

### ✅ Đã có:
- users (3 sample)
- categories (structure)
- breeds (structure)

### ❌ Cần thêm dữ liệu:
- **pets** - Danh sách 12 loài thú cưng (alaska, beagle, corgi, golden, husky, pomeranian, poodle, pug, samoyed, mèo anh, mèo chân ngắn, mèo tai cụp)
- **accessories** - Danh sách phụ kiện (thức ăn, đồ chơi, chuồng, v.v.)

---

## 🔗 File cần sửa:

1. **frontend/login.php** - ✅ UI done, backend logic cần hoàn thiện
2. **frontend/register.php** - ✅ UI redirect to login, backend logic cần hoàn thiện
3. **frontend/pet.php** - ✅ Template ready, cần fetch data từ DB
4. **frontend/index.php** - ✅ UI done, cần load products từ API
5. **frontend/cart.php** - ✅ UI done, cần sync với backend cart
6. **frontend/thanhtoan.php** - ✅ UI done, cần handle order creation
7. **frontend/order-tracking.php** - ✅ UI done, cần load orders từ API
8. **admin/** - ✅ Basic structure, cần hoàn thiện logic

---

## 🎯 Priority (ưu tiên):
1. **HIGH** - Authentication (login/register/session)
2. **HIGH** - Product API (fetch products từ DB)
3. **HIGH** - Cart API (add/remove/update)
4. **HIGH** - Order creation & management
5. **MEDIUM** - Order tracking
6. **MEDIUM** - Admin panel
7. **LOW** - Payment integration
8. **LOW** - Email notifications

---

## 📝 Notes:
- Database name: `pet`
- MySQL host: `127.0.0.1` (NOT localhost - socket issue)
- Root password: (empty)
- All queries use prepared statements (✅ already configured)
- Charset: utf8mb4
- Frontend hoàn toàn xong, chỉ cần backend logic
