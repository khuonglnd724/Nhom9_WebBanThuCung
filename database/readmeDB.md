# Hướng Dẫn Cơ Sở Dữ Liệu PetShop

Ngày tạo: 22-11-2025  
Thành viên phụ trách: Backend / Database / API (Thành viên 3)

## 1. Mục tiêu
Cung cấp cấu trúc CSDL cho website bán thú cưng và phụ kiện: quản lý người dùng, danh mục, thú cưng, phụ kiện, đơn hàng và chi tiết đơn hàng. File triển khai chính: `dbwebthucung.sql`.

## 2. Phiên bản & Môi trường
- MySQL (XAMPP)

## 3. Danh sách bảng
| Bảng | Mô tả | Ghi chú |
|------|-------|--------|
| `users` | Tài khoản người dùng (admin + khách) | Lưu mật khẩu dạng hash (bcrypt) |
| `categories` | Danh mục cho thú cưng & phụ kiện | Trường `type` xác định loại |
| `pets` | Sản phẩm là thú cưng | stock = số lượng hiện có |
| `accessories` | Sản phẩm phụ kiện | Quản lý tồn kho |
| `orders` | Đơn hàng (header) | Tổng tiền cập nhật qua trigger |
| `order_details` | Dòng chi tiết đơn hàng | Liên kết đến pet hoặc accessory qua `item_type` + `item_id` |
| View `v_all_products` | Tổng hợp thú cưng + phụ kiện | Phục vụ API sản phẩm chung |
| Trigger `trg_order_details_*` | Cập nhật `total_amount` | Sau insert/update/delete chi tiết |

## 4. Quan hệ chính
- `pets.category_id` → `categories.id`
- `accessories.category_id` → `categories.id`
- `orders.user_id` → `users.id`
- `order_details.order_id` → `orders.id`
- `order_details.item_id` → (tùy thuộc `item_type`: bảng `pets` hoặc `accessories`) xử lý ở tầng ứng dụng.

## 5. Dữ liệu mẫu
- Đã thêm: 3 user (1 Admin, 2 Customer)
- Danh mục: PET & ACCESSORY (~10+)
- Thú cưng: 12+ bản ghi
- Phụ kiện: 20+ bản ghi
- 1 đơn hàng + chi tiết mẫu (test trigger)

## 6. Quy ước đặt tên
- Bảng: số nhiều tiếng Anh (`users`, `orders`)
- Khóa chính: `id` INT AUTO_INCREMENT
- Mốc thời gian: `created_at`, `updated_at` (TIMESTAMP)
- Trạng thái dùng ENUM (dễ đọc, kiểm soát giá trị)

## 7. Bảo mật & Toàn vẹn
- Mật khẩu: bắt buộc hash (bcrypt / password_hash PHP).
- Không lưu plain text.
- Dùng chuẩn hóa dữ liệu đầu vào trước khi ghi: email, số điện thoại.
- Sử dụng Prepared Statements hoặc ORM để chống SQL Injection.
- Kiểm tra ràng buộc (price >= 0, stock >= 0).

## 8. Mở rộng (Tùy chọn)
| Hạng mục | Mô tả |
|----------|------|
| `product_images` | Lưu nhiều ảnh cho mỗi pet / accessory |
| `reviews` | Đánh giá sản phẩm (user_id, rating, content) |
| `wishlists` | Danh sách yêu thích người dùng |
| `payments` | Giao dịch thanh toán (mã giao dịch, trạng thái) |
| `shipments` | Theo dõi vận chuyển |
| `audit_logs` | Lịch sử thay đổi, ai sửa gì |

## 9. Gợi ý API backend
| Nhóm | Endpoint mẫu | Chức năng |
|------|--------------|----------|
| Auth | POST /api/auth/register | Đăng ký |
| Auth | POST /api/auth/login | Đăng nhập (JWT) |
| Users | GET /api/users/me | Thông tin cá nhân |
| Products | GET /api/products | Danh sách chung (JOIN view) |
| Pets | GET /api/pets/:id | Chi tiết thú cưng |
| Accessories | GET /api/accessories/:id | Chi tiết phụ kiện |
| Filter | GET /api/products?type=PET&category=... | Lọc theo loại / danh mục |
| Cart | POST /api/cart/add | Thêm item (tạm thời lưu session hoặc bảng phụ) |
| Orders | POST /api/orders | Tạo đơn hàng mới |
| Orders | GET /api/orders/:id | Chi tiết đơn hàng |
| Admin | CRUD /api/admin/pets | Quản trị thú cưng |
| Admin | CRUD /api/admin/accessories | Quản trị phụ kiện |

## 10. Trigger & View
- Trigger tự tính tổng: giúp đảm bảo `orders.total_amount` luôn đồng bộ khi thay đổi chi tiết.
- View `v_all_products`: đơn giản hóa truy vấn danh sách sản phẩm cho frontend (union 2 bảng).

## 11. Kiểm thử gợi ý
| Bước | Kiểm tra |
|------|----------|
| 1 | Import thành công không lỗi |
| 2 | SELECT từ từng bảng trả dữ liệu mẫu |
| 3 | Thêm order_details mới → total_amount cập nhật |
| 4 | Update số lượng dòng chi tiết → total_amount cập nhật |
| 5 | Xóa dòng chi tiết → total_amount giảm đúng |

## 12. Lưu ý triển khai
- Môi trường dev: XAMPP local.
- Prod: Khuyến khích dùng dịch vụ MySQL managed (Railway, PlanetScale...).
- Đặt biến môi trường cho thông tin kết nối (host, user, pass, database).

## 13. Kiểm tra nhanh sau import
```sql
SHOW TABLES;
SELECT COUNT(*) FROM users;
SELECT * FROM orders LIMIT 5;
SELECT * FROM v_all_products LIMIT 10;
```

---
Nếu cần bổ sung bảng hoặc sửa thiết kế, cập nhật cả `schema.sql` và file này. Mọi thắc mắc/đề xuất mở rộng: ghi chú lại để thảo luận nhóm.
