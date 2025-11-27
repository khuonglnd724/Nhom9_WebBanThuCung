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
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  status ENUM('PENDING','CONFIRMED','SHIPPED','COMPLETED','CANCELED') NOT NULL DEFAULT 'PENDING',
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
 ('Admin User','admin@petshop.test','$2y$10$oLLx8xmkMWVlpUrtD3zSmeeo/Kb4OjQBmOXRDk.JhiVRAWuL8rH5i','0900000000','ADMIN');

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
