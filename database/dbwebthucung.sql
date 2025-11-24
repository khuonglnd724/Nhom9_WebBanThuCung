-- =============================================================
-- 1) Tables
-- =============================================================

-- Users table (customers + admin)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role ENUM('ADMIN','CUSTOMER') NOT NULL DEFAULT 'CUSTOMER',
  status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories for both pets & accessories (type helps filtering)
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  type ENUM('PET','ACCESSORY','BOTH') NOT NULL DEFAULT 'BOTH',
  parent_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_categories_parent FOREIGN KEY(parent_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Pets table (individual animals for sale)
CREATE TABLE pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  breed VARCHAR(100) NULL,
  gender ENUM('MALE','FEMALE','UNKNOWN') DEFAULT 'UNKNOWN',
  age_months INT DEFAULT 0,
  color VARCHAR(60),
  size VARCHAR(60),
  description TEXT,
  URLImage VARCHAR(255) NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 1,
  status ENUM('AVAILABLE','SOLD','HIDDEN') NOT NULL DEFAULT 'AVAILABLE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pets_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_pets_category (category_id),
  INDEX idx_pets_status (status)
) ENGINE=InnoDB;

-- Accessories table (general products)
CREATE TABLE accessories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  brand VARCHAR(80),
  material VARCHAR(80),
  size VARCHAR(60),
  description TEXT,
  URLImage VARCHAR(255) NULL
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  status ENUM('ACTIVE','INACTIVE','OUT_OF_STOCK') NOT NULL DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_accessories_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_accessories_category (category_id),
  INDEX idx_accessories_status (status)
) ENGINE=InnoDB;

-- Orders master table
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_code VARCHAR(20) NOT NULL UNIQUE,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  status ENUM('PENDING','PAID','SHIPPED','COMPLETED','CANCELED') NOT NULL DEFAULT 'PENDING',
  payment_method ENUM('COD','BANK','VNPAY','MOMO') NOT NULL DEFAULT 'COD',
  shipping_address VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (status)
) ENGINE=InnoDB;

-- Order details referencing either pets or accessories
CREATE TABLE order_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  item_type ENUM('PET','ACCESSORY') NOT NULL,
  item_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL,
  line_total DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_order_details_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_order_details_order (order_id),
  INDEX idx_order_details_item (item_type, item_id)
) ENGINE=InnoDB;

-- =============================================================
-- 2) Helper routines (optional)
-- =============================================================

-- Calculate order total after insert/update/delete on order_details
DELIMITER $$
CREATE TRIGGER trg_order_details_after_change
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
  UPDATE orders SET total_amount = (
    SELECT IFNULL(SUM(line_total),0) FROM order_details WHERE order_id = NEW.order_id
  ) WHERE id = NEW.order_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER trg_order_details_after_update
AFTER UPDATE ON order_details
FOR EACH ROW
BEGIN
  UPDATE orders SET total_amount = (
    SELECT IFNULL(SUM(line_total),0) FROM order_details WHERE order_id = NEW.order_id
  ) WHERE id = NEW.order_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER trg_order_details_after_delete
AFTER DELETE ON order_details
FOR EACH ROW
BEGIN
  UPDATE orders SET total_amount = (
    SELECT IFNULL(SUM(line_total),0) FROM order_details WHERE order_id = OLD.order_id
  ) WHERE id = OLD.order_id;
END$$
DELIMITER ;

-- =============================================================
-- 3) Seed Data (Sample) - Adjust as needed
-- =============================================================

-- Users (passwords should be hashed by application; using placeholder hashes)
INSERT INTO users (full_name, email, password_hash, phone, role) VALUES
 ('Admin User','admin@petshop.test','$2y$10$exampleadminhash','0900000000','ADMIN'),
 ('Nguyen Van A','a.customer@petshop.test','$2y$10$examplehashA','0911111111','CUSTOMER'),
 ('Tran Thi B','b.customer@petshop.test','$2y$10$examplehashB','0922222222','CUSTOMER');

-- Categories (PET)
INSERT INTO categories (name, slug, type) VALUES
 ('Chó','cho','PET'),
 ('Mèo','meo','PET');

-- Categories (ACCESSORY)
INSERT INTO categories (name, slug, type) VALUES
 ('Thức ăn cho chó','thuc-an-cho-cho','ACCESSORY'),
 ('Thức ăn cho mèo','thuc-an-cho-meo','ACCESSORY'),
 ('Đồ chơi','do-choi','ACCESSORY'),
 ('Phụ kiện vệ sinh','phu-kien-ve-sinh','ACCESSORY'),
 ('Chuồng / Lồng','chuong-long','ACCESSORY');

-- Pets (approx 12-15)
INSERT INTO pets (category_id, name, breed, gender, age_months, color, size, description, price, stock) VALUES
 (1,'Lucky','Poodle','MALE',8,'Trắng','Nhỏ','Chó Poodle lông xoăn khỏe mạnh',3500000,1),
 (1,'Bống','Phốc Sóc','FEMALE',6,'Vàng','Nhỏ','Phốc sóc năng động',4200000,1),
 (1,'Milo','Golden Retriever','MALE',10,'Vàng','Lớn','Golden thân thiện',8000000,1),
 (2,'Mướp','Ta','FEMALE',12,'Xám vằn','Trung bình','Mèo ta khỏe mạnh',900000,1),
 (2,'Snow','Anh lông dài','MALE',7,'Trắng','Trung bình','Lông dài mượt',4500000,1),
 (2,'Cookie','Scottish Fold','FEMALE',5,'Kem','Nhỏ','Tai cụp dễ thương',5000000,1),
 (1,'Coco','Corgi','FEMALE',9,'Vàng trắng','Trung bình','Chân ngắn đáng yêu',9500000,1),
 (2,'Leo','Bengal','MALE',8,'Đốm nâu','Trung bình','Hoa văn độc đáo',7000000,1);

-- Accessories (20–30 items)
INSERT INTO accessories (category_id, name, brand, material, size, description, price, stock) VALUES
 (3,'Hạt khô cho chó vị bò', 'PetFood', 'Ngũ cốc', '2kg','Dinh dưỡng cao',220000,30),
 (3,'Hạt khô cho chó vị gà', 'PetFood', 'Ngũ cốc', '2kg','Thơm ngon dễ ăn',210000,25),
 (4,'Thức ăn cho mèo vị cá biển', 'CatCare', 'Ngũ cốc', '1.5kg','Giàu Omega-3',240000,20),
 (4,'Pate cho mèo vị gà', 'CatCare', 'Thịt', '400g','Dễ tiêu hóa',65000,50),
 (5,'Bóng cao su phát sáng','PlayPet','Cao su','Nhỏ','Đồ chơi ban đêm',35000,100),
 (5,'Xương gặm sạch răng','PlayPet','Nhựa an toàn','Trung bình','Giúp sạch răng',55000,60),
 (5,'Chuột giả kêu','PlayPet','Vải','Nhỏ','Mèo thích đuổi bắt',28000,80),
 (6,'Cát vệ sinh cho mèo', 'CleanPet','Khoáng','10L','Hút mùi tốt',120000,40),
 (6,'Bàn chải lông', 'CleanPet','Nhựa + Inox','Nhỏ','Chải lông rụng',45000,35),
 (6,'Dầu tắm khử mùi cho chó', 'CleanPet','Dung dịch','250ml','Mùi dễ chịu',90000,25),
 (7,'Chuồng chó kích thước trung', 'SafeHome','Kim loại','Trung','Thoáng khí',650000,10),
 (7,'Lồng chim cỡ nhỏ', 'SafeHome','Kim loại','Nhỏ','Phù hợp chim nhỏ',280000,12),
 (7,'Lồng hamster 2 tầng', 'SafeHome','Nhựa','Nhỏ','Kèm bánh xe',320000,15),
 (3,'Snack thưởng huấn luyện', 'PetFood','Thịt','200g','Thưởng khi nghe lệnh',75000,45),
 (4,'Vitamin tổng hợp cho mèo', 'CatCare','Viên','Hộp','Tăng đề kháng',150000,18),
 (5,'Dây dắt chó phản quang', 'PlayPet','Nylon','M','An toàn ban đêm',95000,22),
 (5,'Vòng cổ tên khắc', 'PlayPet','Da','S','Khắc tên pet',110000,30),
 (6,'Bình nước tự động', 'CleanPet','Nhựa','500ml','Giữ sạch nước',60000,33),
 (6,'Khay vệ sinh cho mèo', 'CleanPet','Nhựa','Trung','Dễ vệ sinh',85000,27),
 (7,'Nhà gỗ cho mèo', 'SafeHome','Gỗ','Trung','ấm áp, bền',520000,9),
 (7,'Chuồng gấp cho chó', 'SafeHome','Kim loại','Lớn','Gấp gọn tiện',780000,6),
 (6,'Tông đơ cắt lông', 'CleanPet','Nhựa + Kim loại','--','Êm, ít ồn',230000,11);

-- Sample order (empty details first)
INSERT INTO orders (user_id, order_code, shipping_address, phone, payment_method, status) VALUES
 (2,'ORD0001','123 Đường A, Quận 1, TP.HCM','0911111111','COD','PENDING');

-- Sample order details (mix pet + accessory)
INSERT INTO order_details (order_id, item_type, item_id, quantity, unit_price, line_total) VALUES
 (1,'PET', 1, 1, 3500000, 3500000),
 (1,'ACCESSORY', 5, 2, 35000, 70000);

-- Update total manually (trigger also covers it)
UPDATE orders SET total_amount = (SELECT SUM(line_total) FROM order_details WHERE order_id = orders.id) WHERE id = 1;

-- =============================================================
-- 4) Useful Views (optional)
-- =============================================================
CREATE OR REPLACE VIEW v_all_products AS
SELECT 'PET' AS product_type, p.id AS product_id, p.name, p.price, p.stock, p.status, c.name AS category
FROM pets p JOIN categories c ON p.category_id = c.id
UNION ALL
SELECT 'ACCESSORY' AS product_type, a.id AS product_id, a.name, a.price, a.stock, a.status, c.name AS category
FROM accessories a JOIN categories c ON a.category_id = c.id;

-- =============================================================
-- 5) Notes
-- - password_hash values are placeholders; application must hash real passwords.
-- - item_id in order_details points to pets.id or accessories.id depending on item_type.
-- - Consider adding payment table & shipment table later for expansion.
-- - Adjust prices, stock, and sample data as needed.
-- =============================================================
