-- Migration script: Chuyển đổi trạng thái đơn hàng từ PAID sang CONFIRMED
-- Chạy script này để cập nhật database hiện có

-- Bước 1: Cập nhật tất cả đơn hàng có status = 'PAID' thành 'CONFIRMED'
UPDATE orders SET status = 'CONFIRMED' WHERE status = 'PAID';

-- Bước 2: Thay đổi cấu trúc bảng orders - Loại bỏ 'PAID' và thêm 'CONFIRMED'
ALTER TABLE orders 
MODIFY COLUMN status ENUM('PENDING','CONFIRMED','SHIPPED','COMPLETED','CANCELED') NOT NULL DEFAULT 'PENDING';

-- Kiểm tra kết quả
SELECT status, COUNT(*) as count 
FROM orders 
GROUP BY status;

-- Thông báo hoàn thành
SELECT 'Migration completed: PAID → CONFIRMED' AS message;
