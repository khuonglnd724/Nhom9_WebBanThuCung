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

-- Breeds table (dog and cat breeds)
CREATE TABLE breeds (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  pet_type ENUM('DOG','CAT') NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_breeds_type (pet_type)
) ENGINE=InnoDB;

-- Pets table (individual animals for sale)
CREATE TABLE pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  breed_id INT NULL,
  gender ENUM('MALE','FEMALE','UNKNOWN') DEFAULT 'UNKNOWN',
  age_months INT DEFAULT 0,
  color VARCHAR(60),
  size VARCHAR(60),
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 1,
  status ENUM('AVAILABLE','SOLD','HIDDEN') NOT NULL DEFAULT 'AVAILABLE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pets_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_pets_breed FOREIGN KEY(breed_id) REFERENCES breeds(id) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX idx_pets_category (category_id),
  INDEX idx_pets_breed (breed_id),
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
  recipient_name VARCHAR(100) NOT NULL,
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

-- Cart table to persist user's shopping cart
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  item_type ENUM('PET','ACCESSORY') NOT NULL,
  item_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cart_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY unique_cart_item (user_id, item_type, item_id),
  INDEX idx_cart_user (user_id)
) ENGINE=InnoDB;

-- Images table for both pets and accessories
CREATE TABLE images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_type ENUM('PET','ACCESSORY') NOT NULL,
  item_id INT NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  display_order INT NOT NULL DEFAULT 1,
  is_primary BOOLEAN NOT NULL DEFAULT FALSE,
  alt_text VARCHAR(200),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_images_item (item_type, item_id),
  INDEX idx_images_primary (item_type, item_id, is_primary)
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

-- Users (passwords should be hashed by application; using valid bcrypt hashes)
-- Credentials: admin@petshop.test / admin123, a.customer@petshop.test / customer123A, b.customer@petshop.test / customer123B
INSERT INTO users (full_name, email, password_hash, phone, role) VALUES
 ('Admin User','admin@petshop.test','$2y$10$oLLx8xmkMWVlpUrtD3zSmeeo/Kb4OjQBmOXRDk.JhiVRAWuL8rH5i','0900000000','ADMIN'),
 ('Nguyen Van A','a.customer@petshop.test','$2y$10$3KejE8fMKqzzPsdRGPpoButXg.gYvggYJGOC6RO4rq.p4oZNVNJnq','0911111111','CUSTOMER'),
 ('Tran Thi B','b.customer@petshop.test','$2y$10$c5xA/34VHBHrGA98PhhOL.8JCEk1VZuLzPSzdQLAQyD2KE05cS4Z6','0922222222','CUSTOMER');

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

-- Breeds (dog breeds)
INSERT INTO breeds (name, pet_type, description) VALUES
 ('Poodle','DOG','Chó lông xoăn thông minh, dễ huấn luyện'),
 ('Phốc Sóc','DOG','Chó nhỏ năng động, lông dày'),
 ('Golden Retriever','DOG','Chó vàng thân thiện, trung thành'),
 ('Corgi','DOG','Chó chân ngắn, tai dài đặc trưng');

-- Breeds (cat breeds)
INSERT INTO breeds (name, pet_type, description) VALUES
 ('Mèo Ta','CAT','Mèo bản địa Việt Nam khỏe mạnh'),
 ('Anh lông dài','CAT','Mèo Anh lông dài mượt mà'),
 ('Scottish Fold','CAT','Mèo tai cụp đáng yêu'),
 ('Bengal','CAT','Mèo hoa văn đốm độc đáo');

-- More dog breeds
INSERT INTO breeds (name, pet_type, description) VALUES
 ('Chihuahua','DOG','Chó siêu nhỏ, sống lâu'),
 ('Husky','DOG','Chó kéo xe tuyết, mắt xanh đẹp'),
 ('Shiba Inu','DOG','Chó Nhật Bản trung thành'),
 ('Beagle','DOG','Chó săn nhỏ, hiền lành'),
 ('Pug','DOG','Chó mặt xệ đáng yêu'),
 ('Labrador','DOG','Chó nghiệp vụ thông minh'),
 ('Bulldog','DOG','Chó mặt xệ, chân ngắn'),
 ('Doberman','DOG','Chó canh gác dũng mãnh'),
 ('Chow Chow','DOG','Chó lưỡi tím đặc biệt'),
 ('Akita','DOG','Chó Nhật Bản lớn, trung thành'),
 ('Samoyed','DOG','Chó Bắc Cực lông trắng'),
 ('Dalmatian','DOG','Chó đốm trắng đen nổi tiếng'),
 ('Border Collie','DOG','Chó chăn cừu thông minh nhất');

-- More cat breeds
INSERT INTO breeds (name, pet_type, description) VALUES
 ('British Shorthair','CAT','Mèo Anh lông ngắn béo tròn'),
 ('Persian','CAT','Mèo Ba Tư mặt tịt'),
 ('Ragdoll','CAT','Mèo ôm bồng mềm mại');

-- Additional dog breeds to reach 20 total
INSERT INTO breeds (name, pet_type, description) VALUES
 ('Bichon Frise','DOG','Chó lông xoăn trắng nhỏ');

-- Now we have 20 breeds: 17 dogs + 3 cats (including the 4 original dogs + 4 original cats = 8, + 13 + 3 = 24 total, let me recalculate...)
-- Actually let me verify: 4 original dogs (Poodle, Phốc Sóc, Golden, Corgi) + 4 original cats (Mèo Ta, Anh lông dài, Scottish, Bengal) = 8
-- Adding: 13 more dogs + 3 more cats = 16 more breeds
-- Total: 17 dogs + 7 cats = 24 breeds

-- Pets (approx 12-15)
INSERT INTO pets (category_id, name, breed_id, gender, age_months, color, size, description, price, stock) VALUES
 (1,'Lucky',1,'MALE',8,'Trắng','Nhỏ','Chó Poodle lông xoăn khỏe mạnh',3500000,1),
 (1,'Bống',2,'FEMALE',6,'Vàng','Nhỏ','Phốc sóc năng động',4200000,1),
 (1,'Milo',3,'MALE',10,'Vàng','Lớn','Golden thân thiện',8000000,1),
 (2,'Mướp',5,'FEMALE',12,'Xám vằn','Trung bình','Mèo ta khỏe mạnh',900000,1),
 (2,'Snow',6,'MALE',7,'Trắng','Trung bình','Lông dài mượt',4500000,1),
 (2,'Cookie',7,'FEMALE',5,'Kem','Nhỏ','Tai cụp dễ thương',5000000,1),
 (1,'Coco',4,'FEMALE',9,'Vàng trắng','Trung bình','Chân ngắn đáng yêu',9500000,1),
 (2,'Leo',8,'MALE',8,'Đốm nâu','Trung bình','Hoa văn độc đáo',7000000,1);

-- Accessories (20–30 items)
INSERT INTO accessories (category_id, name, brand, material, size, description, price, stock) VALUES
 (3,'Hạt khô cho chó vị bò', 'PetFood', 'Ngũ cốc', '2kg','Dinh dưỡng cao',220000,30),
 (4,'Pate cho mèo vị gà', 'CatCare', 'Thịt', '400g','Dễ tiêu hóa',65000,50),
 (5,'Bóng cao su phát sáng','PlayPet','Cao su','Nhỏ','Đồ chơi ban đêm',35000,100),
 (5,'Xương gặm sạch răng','PlayPet','Nhựa an toàn','Trung bình','Giúp sạch răng',55000,60),
 (5,'Chuột giả kêu','PlayPet','Vải','Nhỏ','Mèo thích đuổi bắt',28000,80),
 (6,'Cát vệ sinh cho mèo', 'CleanPet','Khoáng','10L','Hút mùi tốt',120000,40),
 (6,'Bàn chải lông', 'CleanPet','Nhựa + Inox','Nhỏ','Chải lông rụng',45000,35),
 (6,'Dầu tắm khử mùi cho chó', 'CleanPet','Dung dịch','250ml','Mùi dễ chịu',90000,25),
 (7,'Chuồng chó kích thước trung', 'SafeHome','Kim loại','Trung','Thoáng khí',650000,10),
 (3,'Snack thưởng huấn luyện', 'PetFood','Thịt','200g','Thưởng khi nghe lệnh',75000,45),
 (4,'Vitamin tổng hợp cho mèo', 'CatCare','Viên','Hộp','Tăng đề kháng',150000,18),
 (5,'Dây dắt chó phản quang', 'PlayPet','Nylon','M','An toàn ban đêm',95000,22),
 (5,'Vòng cổ tên khắc', 'PlayPet','Da','S','Khắc tên pet',110000,30),
 (6,'Bình nước tự động', 'CleanPet','Nhựa','500ml','Giữ sạch nước',60000,33),
 (6,'Khay vệ sinh cho mèo', 'CleanPet','Nhựa','Trung','Dễ vệ sinh',85000,27),
 (7,'Nhà gỗ cho mèo', 'SafeHome','Gỗ','Trung','ấm áp, bền',520000,9),
 (7,'Chuồng gấp cho chó', 'SafeHome','Kim loại','Lớn','Gấp gọn tiện',780000,6),
 (6,'Tông đơ cắt lông', 'CleanPet','Nhựa + Kim loại','--','Êm, ít ồn',230000,11);

 -- Update total manually (trigger also covers it)
UPDATE orders SET total_amount = (SELECT SUM(line_total) FROM order_details WHERE order_id = orders.id) WHERE id = 1;

-- Images for pets (sample data - 4 images per pet representing different angles)
INSERT INTO images (item_type, item_id, image_url, display_order, is_primary, alt_text) VALUES
-- Lucky (Poodle) - pet id 1
('PET', 1, 'assets/images/dog/poodle/1.png', 1, TRUE, 'Lucky - Chó Poodle trắng'),
('PET', 1, 'assets/images/dog/poodle/2.png', 2, FALSE, 'Lucky - góc nghiêng'),
('PET', 1, 'assets/images/dog/poodle/3.png', 3, FALSE, 'Lucky - toàn thân'),
('PET', 1, 'assets/images/dog/poodle/4.png', 4, FALSE, 'Lucky - chân dung'),

-- Bống (Phốc Sóc) - pet id 2
('PET', 2, 'assets/images/dog/phoc-soc/1.png', 1, TRUE, 'Bống - Phốc Sóc vàng'),
('PET', 2, 'assets/images/dog/phoc-soc/2.png', 2, FALSE, 'Bống - góc nghiêng'),
('PET', 2, 'assets/images/dog/phoc-soc/3.png', 3, FALSE, 'Bống - toàn thân'),
('PET', 2, 'assets/images/dog/phoc-soc/4.png', 4, FALSE, 'Bống - chân dung'),

-- Milo (Golden Retriever) - pet id 3
('PET', 3, 'assets/images/dog/golden-retriever/1.png', 1, TRUE, 'Milo - Golden Retriever'),
('PET', 3, 'assets/images/dog/golden-retriever/2.png', 2, FALSE, 'Milo - góc nghiêng'),
('PET', 3, 'assets/images/dog/golden-retriever/3.png', 3, FALSE, 'Milo - toàn thân'),
('PET', 3, 'assets/images/dog/golden-retriever/4.png', 4, FALSE, 'Milo - chân dung'),

-- Mướp (Mèo Ta) - pet id 4
('PET', 4, 'assets/images/cat/meo-ta/1.png', 1, TRUE, 'Mướp - Mèo ta xám vằn'),
('PET', 4, 'assets/images/cat/meo-ta/2.png', 2, FALSE, 'Mướp - góc nghiêng'),
('PET', 4, 'assets/images/cat/meo-ta/3.png', 3, FALSE, 'Mướp - toàn thân'),
('PET', 4, 'assets/images/cat/meo-ta/4.png', 4, FALSE, 'Mướp - chân dung'),

-- Snow (Anh lông dài) - pet id 5
('PET', 5, 'assets/images/cat/anh-long-dai/1.png', 1, TRUE, 'Snow - Mèo Anh lông dài trắng'),
('PET', 5, 'assets/images/cat/anh-long-dai/2.png', 2, FALSE, 'Snow - góc nghiêng'),
('PET', 5, 'assets/images/cat/anh-long-dai/3.png', 3, FALSE, 'Snow - toàn thân'),
('PET', 5, 'assets/images/cat/anh-long-dai/4.png', 4, FALSE, 'Snow - chân dung'),

-- Cookie (Scottish Fold) - pet id 6
('PET', 6, 'assets/images/cat/scottish-fold/1.png', 1, TRUE, 'Cookie - Scottish Fold tai cụp'),
('PET', 6, 'assets/images/cat/scottish-fold/2.png', 2, FALSE, 'Cookie - góc nghiêng'),
('PET', 6, 'assets/images/cat/scottish-fold/3.png', 3, FALSE, 'Cookie - toàn thân'),
('PET', 6, 'assets/images/cat/scottish-fold/4.png', 4, FALSE, 'Cookie - chân dung'),

-- Coco (Corgi) - pet id 7
('PET', 7, 'assets/images/dog/corgi/1.png', 1, TRUE, 'Coco - Corgi chân ngắn'),
('PET', 7, 'assets/images/dog/corgi/2.png', 2, FALSE, 'Coco - góc nghiêng'),
-- =============================================================
-- 5) Notes
-- - password_hash values are valid bcrypt hashes for testing.
-- - item_id in order_details points to pets.id or accessories.id depending on item_type.
-- - images table stores multiple images per item (pets or accessories).
-- - URLImage in pets/accessories can store primary image path for backward compatibility.
-- - is_primary flag in images table indicates the main display image.
-- - display_order determines image gallery sequence.
-- - Consider adding payment table & shipment table later for expansion.
-- =============================================================

-- =============================================================
-- 6) Query Examples for Images
-- =============================================================
-- Get all images for a specific pet:
-- SELECT * FROM images WHERE item_type = 'PET' AND item_id = 1 ORDER BY display_order;

-- Get primary image for a pet:
-- SELECT image_url FROM images WHERE item_type = 'PET' AND item_id = 1 AND is_primary = TRUE;

-- Get all images for accessories:
-- SELECT a.name, i.image_url, i.is_primary FROM accessories a 
-- JOIN images i ON i.item_type = 'ACCESSORY' AND i.item_id = a.id 
-- WHERE a.id = 1 ORDER BY i.display_order;
-- =============================================================àn thân'),
('PET', 8, 'assets/images/cat/bengal/4.png', 4, FALSE, 'Leo - chân dung');

-- Images for accessories (sample data - 1-2 images per accessory)
INSERT INTO images (item_type, item_id, image_url, display_order, is_primary, alt_text) VALUES
-- Accessory images
('ACCESSORY', 1, 'assets/images/accessories/dog-food-beef.png', 1, TRUE, 'Hạt khô vị bò cho chó'),
('ACCESSORY', 2, 'assets/images/accessories/dog-food-chicken.png', 1, TRUE, 'Hạt khô vị gà cho chó'),
('ACCESSORY', 3, 'assets/images/accessories/cat-food-seafood.png', 1, TRUE, 'Thức ăn vị cá biển cho mèo'),
('ACCESSORY', 4, 'assets/images/accessories/cat-pate-chicken.png', 1, TRUE, 'Pate vị gà cho mèo'),
('ACCESSORY', 5, 'assets/images/accessories/glowing-ball.png', 1, TRUE, 'Bóng cao su phát sáng'),
('ACCESSORY', 6, 'assets/images/accessories/teeth-cleaning-bone.png', 1, TRUE, 'Xương gặm sạch răng'),
('ACCESSORY', 7, 'assets/images/accessories/squeaky-mouse.png', 1, TRUE, 'Chuột giả kêu'),
('ACCESSORY', 8, 'assets/images/accessories/cat-litter.png', 1, TRUE, 'Cát vệ sinh cho mèo'),
('ACCESSORY', 9, 'assets/images/accessories/pet-brush.png', 1, TRUE, 'Bàn chải lông'),
('ACCESSORY', 10, 'assets/images/accessories/dog-shampoo.png', 1, TRUE, 'Dầu tắm khử mùi cho chó'),
('ACCESSORY', 11, 'assets/images/accessories/dog-cage-medium.png', 1, TRUE, 'Chuồng chó kích thước trung'),
('ACCESSORY', 12, 'assets/images/accessories/training-snack.png', 1, TRUE, 'Snack thưởng huấn luyện'),
('ACCESSORY', 13, 'assets/images/accessories/cat-vitamin.png', 1, TRUE, 'Vitamin tổng hợp cho mèo'),
('ACCESSORY', 14, 'assets/images/accessories/reflective-leash.png', 1, TRUE, 'Dây dắt chó phản quang'),
('ACCESSORY', 15, 'assets/images/accessories/engraved-collar.png', 1, TRUE, 'Vòng cổ tên khắc'),
('ACCESSORY', 16, 'assets/images/accessories/auto-water-bottle.png', 1, TRUE, 'Bình nước tự động'),
('ACCESSORY', 17, 'assets/images/accessories/cat-litter-tray.png', 1, TRUE, 'Khay vệ sinh cho mèo'),
('ACCESSORY', 18, 'assets/images/accessories/wooden-cat-house.png', 1, TRUE, 'Nhà gỗ cho mèo'),
('ACCESSORY', 19, 'assets/images/accessories/foldable-dog-cage.png', 1, TRUE, 'Chuồng gấp cho chó'),
('ACCESSORY', 20, 'assets/images/accessories/pet-clipper.png', 1, TRUE, 'Tông đơ cắt lông');

-- Sample order (empty details first)
INSERT INTO orders (user_id, order_code, recipient_name, shipping_address, phone, payment_method, status) VALUES
 (2,'ORD0001','Nguyen Van A','123 Đường A, Quận 1, TP.HCM','0911111111','COD','PENDING');

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
