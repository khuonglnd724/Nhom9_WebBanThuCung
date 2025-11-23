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
Nếu cần bổ sung bảng hoặc sửa thiết kế, cập nhật cả `dbwebthucung.sql` và file này. Mọi thắc mắc/đề xuất mở rộng: ghi chú lại để thảo luận nhóm.

## 14. Trường `pets.image_meta` (lưu metadata hình ảnh)

Mục đích: Lưu metadata ảnh của thú cưng dưới dạng JSON linh hoạt (không bắt buộc). Không có dữ liệu mẫu được thêm cho trường này.

- Kiểu dữ liệu: `JSON NULL` (MySQL ≥ 5.7 / MariaDB ≥ 10.2). Nếu môi trường không hỗ trợ tốt JSON, có thể chuyển sang `TEXT` và lưu chuỗi JSON.
- Vị trí trong bảng: sau `description` của bảng `pets`.

### Cấu trúc JSON gợi ý
Bạn có thể tùy biến theo nhu cầu. Ví dụ khuyến nghị:

```json
{
	"main": {
		"url": "https://cdn.example.com/pets/abc.jpg",
		"alt": "Cún Poodle trắng",
		"width": 1200,
		"height": 800,
		"mime": "image/jpeg",
		"size_kb": 220
	},
	"gallery": [
		{ "url": "https://cdn.example.com/pets/abc-1.jpg", "alt": "Góc nghiêng" },
		{ "url": "https://cdn.example.com/pets/abc-2.jpg", "alt": "Đứng cạnh đồ chơi" }
	]
}
```

### Cách ghi dữ liệu
- Thêm mới cùng lúc khi tạo pet:
```sql
INSERT INTO pets (category_id, name, breed, gender, age_months, price, stock, description, image_meta)
VALUES (1, 'Poodle Trắng', 'Poodle', 'FEMALE', 8, 3500000, 1, 'Mô tả...',
	JSON_OBJECT(
		'main', JSON_OBJECT(
			'url', 'https://cdn.example.com/pets/abc.jpg',
			'alt', 'Cún Poodle trắng',
			'width', 1200, 'height', 800, 'mime', 'image/jpeg', 'size_kb', 220
		),
		'gallery', JSON_ARRAY(
			JSON_OBJECT('url','https://cdn.example.com/pets/abc-1.jpg','alt','Góc nghiêng'),
			JSON_OBJECT('url','https://cdn.example.com/pets/abc-2.jpg','alt','Đứng cạnh đồ chơi')
		)
	)
);
```

- Cập nhật ảnh chính:
```sql
UPDATE pets
SET image_meta = JSON_SET(
	COALESCE(image_meta, JSON_OBJECT()),
	'$.main', JSON_OBJECT(
		'url','https://cdn.example.com/pets/new-main.jpg',
		'alt','Ảnh chính mới'
	)
)
WHERE id = 123;
```

- Thêm một ảnh vào gallery (append):
```sql
UPDATE pets
SET image_meta = JSON_ARRAY_APPEND(
	COALESCE(image_meta, JSON_OBJECT('gallery', JSON_ARRAY())),
	'$.gallery', JSON_OBJECT('url','https://cdn.example.com/pets/new-1.jpg','alt','Góc cận')
)
WHERE id = 123;
```

- Xóa metadata ảnh (đưa về NULL):
```sql
UPDATE pets SET image_meta = NULL WHERE id = 123;
```

### Truy vấn đọc nhanh
- Lấy URL ảnh chính:
```sql
SELECT id,
			 JSON_UNQUOTE(JSON_EXTRACT(image_meta, '$.main.url')) AS main_url
FROM pets;
```

- Lọc thú cưng có ảnh chính:
```sql
SELECT * FROM pets
WHERE JSON_EXTRACT(image_meta, '$.main.url') IS NOT NULL;
```

- Lấy số lượng ảnh trong gallery (MySQL 8+):
```sql
SELECT id,
			 JSON_LENGTH(JSON_EXTRACT(image_meta, '$.gallery')) AS gallery_count
FROM pets;
```

Ghi chú: MySQL hỗ trợ toán tử `->` và `->>`; ví dụ `image_meta->'$.main.url'`. Tuy nhiên dùng `JSON_EXTRACT` + `JSON_UNQUOTE` sẽ phổ biến và tương thích hơn giữa MySQL/MariaDB.

### Tương thích phiên bản
- MySQL ≥ 5.7: hỗ trợ kiểu `JSON` và các hàm JSON (`JSON_OBJECT`, `JSON_SET`, `JSON_ARRAY_APPEND`, ...).
- MariaDB ≥ 10.2: hỗ trợ hàm JSON, nhưng `JSON` có thể chỉ là alias của `LONGTEXT`. Vẫn dùng được các hàm trên. Nếu lỗi do phiên bản quá cũ, đổi cột về `TEXT`.

#### Phương án fallback (nếu cần)
- Đổi cột:
```sql
ALTER TABLE pets MODIFY image_meta TEXT NULL;
```
- Ứng dụng vẫn lưu/đọc chuỗi JSON. Nên validate JSON ở tầng ứng dụng trước khi ghi DB.

### Gợi ý tối ưu (không bắt buộc)
- Nếu cần tìm kiếm/ lọc theo `main.url` nhanh, có thể tạo cột sinh (generated column) và index:
```sql
ALTER TABLE pets
	ADD COLUMN image_main_url VARCHAR(255)
		GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(image_meta, '$.main.url'))) STORED,
	ADD INDEX idx_pets_image_main_url (image_main_url);
```

## 15. Link ảnh nội bộ dự án (XAMPP)

Khi lưu ảnh chó/mèo trực tiếp trong dự án, nên đặt trong thư mục web-accessible để có thể truy cập qua HTTP.

- Thư mục đề xuất (tại gốc dự án):
	- `uploads/pets/dogs/`
	- `uploads/pets/cats/`
	- Đường dẫn hệ thống: `d:\xampp\htdocs\Nhom9_WebBanThuCung\uploads\pets\dogs\...`
	- URL công khai (Apache/XAMPP): `http://localhost/Nhom9_WebBanThuCung/uploads/pets/dogs/abc.jpg`

- Cách ghi đường dẫn vào `pets.image_meta` (khuyến nghị dùng đường dẫn tương đối để dễ deploy):
	- Tùy chọn A – Relative từ gốc web dự án (có prefix tên dự án): `'/Nhom9_WebBanThuCung/uploads/pets/dogs/abc.jpg'`
	- Tùy chọn B – Relative không prefix (chỉ phần trong dự án): `'uploads/pets/dogs/abc.jpg'`
	- Tùy backend, bạn ghép `BASE_URL` khi trả ra client.

Ví dụ lưu URL tương đối vào ảnh chính:
```sql
UPDATE pets
SET image_meta = JSON_SET(
	COALESCE(image_meta, JSON_OBJECT()),
	'$.main.url', '/Nhom9_WebBanThuCung/uploads/pets/dogs/abc.jpg',
	'$.main.alt', 'Cún Poodle trắng'
)
WHERE id = 1;
```

Ví dụ lưu chỉ subpath và ghép ở ứng dụng:
```sql
UPDATE pets
SET image_meta = JSON_SET(
	COALESCE(image_meta, JSON_OBJECT()),
	'$.main.url', 'uploads/pets/cats/meo-001.jpg'
)
WHERE id = 2;
```

Truy vấn lấy URL ảnh chính:
```sql
SELECT id,
			 JSON_UNQUOTE(JSON_EXTRACT(image_meta, '$.main.url')) AS image_url
FROM pets;
```

Gợi ý xử lý ở ứng dụng (mô tả ngắn):
- Nếu `image_url` bắt đầu bằng `http` → dùng trực tiếp.
- Nếu là relative (bắt đầu bằng `/` hoặc không có `http`) → ghép `BASE_URL` (ví dụ `http://localhost/Nhom9_WebBanThuCung/`) rồi chuẩn hóa dấu `/`.

Lưu ý thực tế:
- Trong URL luôn dùng dấu `/`, không dùng `\` của Windows.
- Đảm bảo thư mục `uploads/` có quyền ghi cho quá trình upload.
- Sanitize tên file, kiểm tra MIME type ảnh, giới hạn kích thước.
- Khi đổi domain hoặc deploy production, chỉ cần cập nhật `BASE_URL` hoặc cấu hình VirtualHost để project hoạt động ở root; cách lưu relative sẽ giúp ít phải sửa dữ liệu.

