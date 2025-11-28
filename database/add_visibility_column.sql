-- Migration: Thêm cột is_visible vào bảng pets và accessories
-- Chạy script này để cập nhật database hiện có

-- Thêm cột is_visible vào bảng pets
ALTER TABLE pets 
ADD COLUMN is_visible BOOLEAN NOT NULL DEFAULT TRUE AFTER status,
ADD INDEX idx_pets_visible (is_visible);

-- Thêm cột is_visible vào bảng accessories
ALTER TABLE accessories 
ADD COLUMN is_visible BOOLEAN NOT NULL DEFAULT TRUE AFTER status,
ADD INDEX idx_accessories_visible (is_visible);

-- Cập nhật tất cả sản phẩm hiện có thành visible (TRUE)
UPDATE pets SET is_visible = TRUE WHERE is_visible IS NULL;
UPDATE accessories SET is_visible = TRUE WHERE is_visible IS NULL;

-- Kiểm tra kết quả
SELECT 'Pets table:' as info, 
       COUNT(*) as total, 
       SUM(is_visible) as visible, 
       COUNT(*) - SUM(is_visible) as hidden 
FROM pets;

SELECT 'Accessories table:' as info, 
       COUNT(*) as total, 
       SUM(is_visible) as visible, 
       COUNT(*) - SUM(is_visible) as hidden 
FROM accessories;

SELECT 'Migration completed successfully!' AS message;
