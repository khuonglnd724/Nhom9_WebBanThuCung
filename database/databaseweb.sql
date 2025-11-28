-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2025 lúc 05:22 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `databaseweb`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `accessories`
--

CREATE TABLE `accessories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `brand` varchar(80) DEFAULT NULL,
  `material` varchar(80) DEFAULT NULL,
  `size` varchar(60) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` enum('ACTIVE','INACTIVE','OUT_OF_STOCK') NOT NULL DEFAULT 'ACTIVE',
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `accessories`
--

INSERT INTO `accessories` (`id`, `category_id`, `name`, `brand`, `material`, `size`, `description`, `price`, `stock`, `status`, `is_visible`, `created_at`, `updated_at`) VALUES
(1, 3, 'Hạt khô cho chó vị bò', 'PetFood', 'Ngũ cốc', '2kg', 'Dinh dưỡng cao', 220000.00, 30, 'ACTIVE', 1, '2025-11-28 15:55:24', '2025-11-28 15:55:24'),
(2, 4, 'Pate cho mèo vị gà', 'CatCare', 'Thịt', '400g', 'Dễ tiêu hóa', 65000.00, 50, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:18:42'),
(3, 5, 'Bóng cao su phát sáng', 'PlayPet', 'Cao su', 'Nhỏ', 'Đồ chơi ban đêm', 35000.00, 100, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:18:34'),
(4, 5, 'Xương gặm sạch răng', 'PlayPet', 'Nhựa an toàn', 'Trung bình', 'Giúp sạch răng', 55000.00, 60, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:18:24'),
(5, 5, 'Chuột giả kêu', 'PlayPet', 'Vải', 'Nhỏ', 'Mèo thích đuổi bắt', 28000.00, 80, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:18:14'),
(6, 6, 'Cát vệ sinh cho mèo', 'CleanPet', 'Khoáng', '10L', 'Hút mùi tốt', 120000.00, 40, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:18:03'),
(7, 6, 'Bàn chải lông', 'CleanPet', 'Nhựa + Inox', 'Nhỏ', 'Chải lông rụng', 45000.00, 35, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:17:56'),
(8, 6, 'Dầu tắm khử mùi cho chó', 'CleanPet', 'Dung dịch', '250ml', 'Mùi dễ chịu', 90000.00, 25, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:17:34'),
(9, 7, 'Chuồng chó kích thước trung', 'SafeHome', 'Kim loại', 'Trung', 'Thoáng khí', 650000.00, 10, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:16:54'),
(10, 3, 'Snack thưởng huấn luyện', 'PetFood', 'Thịt', '200g', 'Thưởng khi nghe lệnh', 75000.00, 45, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:16:35'),
(11, 4, 'Vitamin tổng hợp cho mèo', 'CatCare', 'Viên', 'Hộp', 'Tăng đề kháng', 150000.00, 18, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:16:20'),
(12, 5, 'Dây dắt chó phản quang', 'PlayPet', 'Nylon', 'M', 'An toàn ban đêm', 95000.00, 22, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:15:40'),
(13, 5, 'Vòng cổ tên khắc', 'PlayPet', 'Da', 'S', 'Khắc tên pet', 110000.00, 30, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:15:30'),
(14, 6, 'Bình nước tự động', 'CleanPet', 'Nhựa', '500ml', 'Giữ sạch nước', 60000.00, 33, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:15:18'),
(15, 6, 'Khay vệ sinh cho mèo', 'CleanPet', 'Nhựa', 'Trung', 'Dễ vệ sinh', 85000.00, 27, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:15:02'),
(16, 7, 'Nhà gỗ cho mèo', 'SafeHome', 'Gỗ', 'Trung', 'ấm áp, bền', 520000.00, 9, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:14:51'),
(17, 7, 'Chuồng gấp cho chó', 'SafeHome', 'Kim loại', 'Lớn', 'Gấp gọn tiện', 780000.00, 6, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:14:36'),
(18, 6, 'Tông đơ cắt lông', 'CleanPet', 'Nhựa + Kim loại', '--', 'Êm, ít ồn', 230000.00, 11, '', 1, '2025-11-28 15:55:24', '2025-11-28 16:14:13'),
(19, 8, 'Bát', 'SafeHome', 'Nhựa', 'Trung', 'Bát ăn sạch đẹp', 30000.00, 30, 'ACTIVE', 1, '2025-11-28 15:57:48', '2025-11-28 15:57:48'),
(20, 8, 'Bát chống ăn nhanh siêu cấp', 'SafeHome', 'Nhựa', 'Trung', 'Chống cho thú cưng ăn nhanh dẫn đến vấn đề về tiêu hóa', 50000.00, 19, 'ACTIVE', 1, '2025-11-28 16:05:53', '2025-11-28 16:08:42'),
(21, 3, 'Pate siêu dinh dưỡng', 'PetFood', 'Gà, cá', '1 bịch', 'Pate cho chó', 30000.00, 2, 'ACTIVE', 1, '2025-11-28 16:12:28', '2025-11-28 16:12:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `breeds`
--

CREATE TABLE `breeds` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `pet_type` enum('DOG','CAT') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `breeds`
--

INSERT INTO `breeds` (`id`, `name`, `pet_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Poodle', 'DOG', 'Chó lông xoăn thông minh, dễ huấn luyện', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(2, 'Phốc Sóc', 'DOG', 'Chó nhỏ năng động, lông dày', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(3, 'Golden Retriever', 'DOG', 'Chó vàng thân thiện, trung thành', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(4, 'Corgi', 'DOG', 'Chó chân ngắn, tai dài đặc trưng', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(5, 'Mèo Ta', 'CAT', 'Mèo bản địa Việt Nam khỏe mạnh', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(6, 'Anh lông dài', 'CAT', 'Mèo Anh lông dài mượt mà', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(7, 'Scottish Fold', 'CAT', 'Mèo tai cụp đáng yêu', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(8, 'Bengal', 'CAT', 'Mèo hoa văn đốm độc đáo', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(9, 'Chihuahua', 'DOG', 'Chó siêu nhỏ, sống lâu', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(10, 'Husky', 'DOG', 'Chó kéo xe tuyết, mắt xanh đẹp', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(11, 'Shiba Inu', 'DOG', 'Chó Nhật Bản trung thành', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(12, 'Beagle', 'DOG', 'Chó săn nhỏ, hiền lành', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(13, 'Pug', 'DOG', 'Chó mặt xệ đáng yêu', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(14, 'Labrador', 'DOG', 'Chó nghiệp vụ thông minh', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(15, 'Bulldog', 'DOG', 'Chó mặt xệ, chân ngắn', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(16, 'Doberman', 'DOG', 'Chó canh gác dũng mãnh', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(17, 'Chow Chow', 'DOG', 'Chó lưỡi tím đặc biệt', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(18, 'Akita', 'DOG', 'Chó Nhật Bản lớn, trung thành', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(19, 'Samoyed', 'DOG', 'Chó Bắc Cực lông trắng', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(20, 'Dalmatian', 'DOG', 'Chó đốm trắng đen nổi tiếng', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(21, 'Border Collie', 'DOG', 'Chó chăn cừu thông minh nhất', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(22, 'British Shorthair', 'CAT', 'Mèo Anh lông ngắn béo tròn', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(23, 'Persian', 'CAT', 'Mèo Ba Tư mặt tịt', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(24, 'Ragdoll', 'CAT', 'Mèo ôm bồng mềm mại', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(25, 'Bichon Frise', 'DOG', 'Chó lông xoăn trắng nhỏ', '2025-11-28 15:55:23', '2025-11-28 15:55:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type` enum('PET','ACCESSORY') NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `type` enum('PET','ACCESSORY','BOTH') NOT NULL DEFAULT 'BOTH',
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Chó', 'cho', 'PET', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(2, 'Mèo', 'meo', 'PET', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(3, 'Thức ăn cho chó', 'thuc-an-cho-cho', 'ACCESSORY', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(4, 'Thức ăn cho mèo', 'thuc-an-cho-meo', 'ACCESSORY', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(5, 'Đồ chơi', 'do-choi', 'ACCESSORY', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(6, 'Phụ kiện vệ sinh', 'phu-kien-ve-sinh', 'ACCESSORY', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(7, 'Chuồng / Lồng', 'chuong-long', 'ACCESSORY', NULL, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(8, 'Dụng cụ ăn uống', 'dng-c-an-ung', 'ACCESSORY', NULL, '2025-11-28 15:57:48', '2025-11-28 15:57:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `item_type` enum('PET','ACCESSORY') NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `alt_text` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `images`
--

INSERT INTO `images` (`id`, `item_type`, `item_id`, `image_url`, `display_order`, `is_primary`, `alt_text`, `created_at`) VALUES
(1, 'PET', 1, 'assets/images/dog/poodle/1.png', 1, 1, 'Lucky - Chó Poodle trắng', '2025-11-28 15:55:24'),
(2, 'PET', 1, 'assets/images/dog/poodle/2.png', 2, 0, 'Lucky - góc nghiêng', '2025-11-28 15:55:24'),
(3, 'PET', 1, 'assets/images/dog/poodle/3.png', 3, 0, 'Lucky - toàn thân', '2025-11-28 15:55:24'),
(4, 'PET', 1, 'assets/images/dog/poodle/4.png', 4, 0, 'Lucky - chân dung', '2025-11-28 15:55:24'),
(5, 'PET', 2, 'assets/images/dog/phoc-soc/1.png', 1, 1, 'Bống - Phốc Sóc vàng', '2025-11-28 15:55:24'),
(6, 'PET', 2, 'assets/images/dog/phoc-soc/2.png', 2, 0, 'Bống - góc nghiêng', '2025-11-28 15:55:24'),
(7, 'PET', 2, 'assets/images/dog/phoc-soc/3.png', 3, 0, 'Bống - toàn thân', '2025-11-28 15:55:24'),
(8, 'PET', 2, 'assets/images/dog/phoc-soc/4.png', 4, 0, 'Bống - chân dung', '2025-11-28 15:55:24'),
(9, 'PET', 3, 'assets/images/dog/golden-retriever/1.png', 1, 1, 'Milo - Golden Retriever', '2025-11-28 15:55:24'),
(10, 'PET', 3, 'assets/images/dog/golden-retriever/2.png', 2, 0, 'Milo - góc nghiêng', '2025-11-28 15:55:24'),
(11, 'PET', 3, 'assets/images/dog/golden-retriever/3.png', 3, 0, 'Milo - toàn thân', '2025-11-28 15:55:24'),
(12, 'PET', 3, 'assets/images/dog/golden-retriever/4.png', 4, 0, 'Milo - chân dung', '2025-11-28 15:55:24'),
(13, 'PET', 4, 'assets/images/cat/meo-ta/1.png', 1, 1, 'Mướp - Mèo ta xám vằn', '2025-11-28 15:55:24'),
(14, 'PET', 4, 'assets/images/cat/meo-ta/2.png', 2, 0, 'Mướp - góc nghiêng', '2025-11-28 15:55:24'),
(15, 'PET', 4, 'assets/images/cat/meo-ta/3.png', 3, 0, 'Mướp - toàn thân', '2025-11-28 15:55:24'),
(16, 'PET', 4, 'assets/images/cat/meo-ta/4.png', 4, 0, 'Mướp - chân dung', '2025-11-28 15:55:24'),
(17, 'PET', 5, 'assets/images/cat/anh-long-dai/1.png', 1, 1, 'Snow - Mèo Anh lông dài trắng', '2025-11-28 15:55:24'),
(18, 'PET', 5, 'assets/images/cat/anh-long-dai/2.png', 2, 0, 'Snow - góc nghiêng', '2025-11-28 15:55:24'),
(19, 'PET', 5, 'assets/images/cat/anh-long-dai/3.png', 3, 0, 'Snow - toàn thân', '2025-11-28 15:55:24'),
(20, 'PET', 5, 'assets/images/cat/anh-long-dai/4.png', 4, 0, 'Snow - chân dung', '2025-11-28 15:55:24'),
(21, 'PET', 6, 'assets/images/cat/scottish-fold/1.png', 1, 1, 'Cookie - Scottish Fold tai cụp', '2025-11-28 15:55:24'),
(22, 'PET', 6, 'assets/images/cat/scottish-fold/2.png', 2, 0, 'Cookie - góc nghiêng', '2025-11-28 15:55:24'),
(23, 'PET', 6, 'assets/images/cat/scottish-fold/3.png', 3, 0, 'Cookie - toàn thân', '2025-11-28 15:55:24'),
(24, 'PET', 6, 'assets/images/cat/scottish-fold/4.png', 4, 0, 'Cookie - chân dung', '2025-11-28 15:55:24'),
(25, 'PET', 7, 'assets/images/dog/corgi/1.png', 1, 1, 'Coco - Corgi chân ngắn', '2025-11-28 15:55:24'),
(26, 'PET', 7, 'assets/images/dog/corgi/2.png', 2, 0, 'Coco - góc nghiêng', '2025-11-28 15:55:24'),
(27, 'PET', 8, 'assets/images/cat/bengal/4.png', 4, 0, 'Leo - chân dung', '2025-11-28 15:55:24'),
(28, 'ACCESSORY', 1, 'assets/images/accessories/dog-food-beef.png', 1, 1, 'Hạt khô vị bò cho chó', '2025-11-28 15:55:24'),
(29, 'ACCESSORY', 2, '/assets/images/phukien/1764346722_6929cb62d607d.png', 1, 1, 'Hạt khô vị gà cho chó', '2025-11-28 15:55:24'),
(30, 'ACCESSORY', 3, '/assets/images/phukien/1764346714_6929cb5a332df.png', 1, 1, 'Thức ăn vị cá biển cho mèo', '2025-11-28 15:55:24'),
(31, 'ACCESSORY', 4, '/assets/images/phukien/1764346704_6929cb508d7cd.png', 1, 1, 'Pate vị gà cho mèo', '2025-11-28 15:55:24'),
(32, 'ACCESSORY', 5, '/assets/images/phukien/1764346694_6929cb46b2469.png', 1, 1, 'Bóng cao su phát sáng', '2025-11-28 15:55:24'),
(33, 'ACCESSORY', 6, '/assets/images/phukien/1764346683_6929cb3b71434.png', 1, 1, 'Xương gặm sạch răng', '2025-11-28 15:55:24'),
(34, 'ACCESSORY', 7, '/assets/images/phukien/1764346676_6929cb344eee9.png', 1, 1, 'Chuột giả kêu', '2025-11-28 15:55:24'),
(35, 'ACCESSORY', 8, '/assets/images/phukien/1764346654_6929cb1ed04aa.png', 1, 1, 'Cát vệ sinh cho mèo', '2025-11-28 15:55:24'),
(36, 'ACCESSORY', 9, '/assets/images/phukien/1764346614_6929caf601bf2.png', 1, 1, 'Bàn chải lông', '2025-11-28 15:55:24'),
(37, 'ACCESSORY', 10, '/assets/images/phukien/1764346595_6929cae342a44.png', 1, 1, 'Dầu tắm khử mùi cho chó', '2025-11-28 15:55:24'),
(38, 'ACCESSORY', 11, '/assets/images/phukien/1764346580_6929cad4ee12b.png', 1, 1, 'Chuồng chó kích thước trung', '2025-11-28 15:55:24'),
(39, 'ACCESSORY', 12, '/assets/images/phukien/1764346540_6929caac53957.png', 1, 1, 'Snack thưởng huấn luyện', '2025-11-28 15:55:24'),
(40, 'ACCESSORY', 13, '/assets/images/phukien/1764346530_6929caa2d37cf.png', 1, 1, 'Vitamin tổng hợp cho mèo', '2025-11-28 15:55:24'),
(41, 'ACCESSORY', 14, '/assets/images/phukien/1764346518_6929ca968fdd6.png', 1, 1, 'Dây dắt chó phản quang', '2025-11-28 15:55:24'),
(42, 'ACCESSORY', 15, '/assets/images/phukien/1764346502_6929ca8667747.png', 1, 1, 'Vòng cổ tên khắc', '2025-11-28 15:55:24'),
(43, 'ACCESSORY', 16, '/assets/images/phukien/1764346491_6929ca7b15d5a.png', 1, 1, 'Bình nước tự động', '2025-11-28 15:55:24'),
(44, 'ACCESSORY', 17, '/assets/images/phukien/1764346476_6929ca6ca48de.png', 1, 1, 'Khay vệ sinh cho mèo', '2025-11-28 15:55:24'),
(45, 'ACCESSORY', 18, '/assets/images/phukien/1764346453_6929ca55a2d43.png', 1, 1, 'Nhà gỗ cho mèo', '2025-11-28 15:55:24'),
(46, 'ACCESSORY', 19, '/assets/images/phukien/1764345468_6929c67cb55a4.png', 1, 1, NULL, '2025-11-28 15:57:48'),
(47, 'PET', 9, '/assets/images/pets/dog/1764345835_6929c7ebab93e.png', 1, 1, NULL, '2025-11-28 16:03:55'),
(48, 'ACCESSORY', 20, '/assets/images/phukien/1764345953_6929c86132f69.png', 1, 1, NULL, '2025-11-28 16:05:53'),
(49, 'ACCESSORY', 21, '/assets/images/phukien/1764346348_6929c9ece1f99.png', 1, 1, NULL, '2025-11-28 16:12:28'),
(50, 'PET', 8, '/assets/images/pets/cat/1764346813_6929cbbd98fef.png', 1, 1, NULL, '2025-11-28 16:20:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_code` varchar(20) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('PENDING','CONFIRMED','SHIPPED','COMPLETED','CANCELED') NOT NULL DEFAULT 'PENDING',
  `payment_method` enum('COD','BANK','VNPAY','MOMO') NOT NULL DEFAULT 'COD',
  `shipping_address` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `recipient_name`, `total_amount`, `status`, `payment_method`, `shipping_address`, `phone`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 'ORD0001', 'Nguyen Van A', 3570000.00, 'PENDING', 'COD', '123 Đường A, Quận 1, TP.HCM', '0911111111', NULL, '2025-11-28 15:55:24', '2025-11-28 15:55:24'),
(4, 4, 'ORD1764346122294', 'a', 50000.00, 'COMPLETED', 'COD', 'an phú đông, hcm', '11111111', 'aaaa', '2025-11-28 16:08:42', '2025-11-28 16:09:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_type` enum('PET','ACCESSORY') NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `item_type`, `item_id`, `quantity`, `unit_price`, `line_total`, `created_at`) VALUES
(1, 1, 'PET', 1, 1, 3500000.00, 3500000.00, '2025-11-28 15:55:24'),
(2, 1, 'ACCESSORY', 5, 2, 35000.00, 70000.00, '2025-11-28 15:55:24'),
(3, 4, 'ACCESSORY', 20, 1, 50000.00, 50000.00, '2025-11-28 16:08:42');

--
-- Bẫy `order_details`
--
DELIMITER $$
CREATE TRIGGER `trg_order_details_after_change` AFTER INSERT ON `order_details` FOR EACH ROW BEGIN
  UPDATE orders SET total_amount = (
    SELECT IFNULL(SUM(line_total),0) FROM order_details WHERE order_id = NEW.order_id
  ) WHERE id = NEW.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_details_after_delete` AFTER DELETE ON `order_details` FOR EACH ROW BEGIN
  UPDATE orders SET total_amount = (
    SELECT IFNULL(SUM(line_total),0) FROM order_details WHERE order_id = OLD.order_id
  ) WHERE id = OLD.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_details_after_update` AFTER UPDATE ON `order_details` FOR EACH ROW BEGIN
  UPDATE orders SET total_amount = (
    SELECT IFNULL(SUM(line_total),0) FROM order_details WHERE order_id = NEW.order_id
  ) WHERE id = NEW.order_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `breed_id` int(11) DEFAULT NULL,
  `gender` enum('MALE','FEMALE','UNKNOWN') DEFAULT 'UNKNOWN',
  `age_months` int(11) DEFAULT 0,
  `color` varchar(60) DEFAULT NULL,
  `size` varchar(60) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 1,
  `status` enum('AVAILABLE','SOLD','HIDDEN') NOT NULL DEFAULT 'AVAILABLE',
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `pets`
--

INSERT INTO `pets` (`id`, `category_id`, `name`, `breed_id`, `gender`, `age_months`, `color`, `size`, `description`, `price`, `stock`, `status`, `is_visible`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lucky', 1, 'MALE', 8, 'Trắng', 'Nhỏ', 'Chó Poodle lông xoăn khỏe mạnh', 3500000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(2, 1, 'Bống', 2, 'FEMALE', 6, 'Vàng', 'Nhỏ', 'Phốc sóc năng động', 4200000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(3, 1, 'Milo', 3, 'MALE', 10, 'Vàng', 'Lớn', 'Golden thân thiện', 8000000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(4, 2, 'Mướp', 5, 'FEMALE', 12, 'Xám vằn', 'Trung bình', 'Mèo ta khỏe mạnh', 900000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(5, 2, 'Snow', 6, 'MALE', 7, 'Trắng', 'Trung bình', 'Lông dài mượt', 4500000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(6, 2, 'Cookie', 7, 'FEMALE', 5, 'Kem', 'Nhỏ', 'Tai cụp dễ thương', 5000000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(7, 1, 'Coco', 4, 'FEMALE', 9, 'Vàng trắng', 'Trung bình', 'Chân ngắn đáng yêu', 9500000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(8, 2, 'Leo', 8, 'MALE', 8, 'Đốm nâu', 'Trung bình', 'Hoa văn độc đáo', 7000000.00, 1, 'AVAILABLE', 1, '2025-11-28 15:55:23', '2025-11-28 16:20:13'),
(9, 1, 'Coco nớt', 4, 'MALE', 12, 'Trắng', 'Trung bình', 'Cute thân thiện', 5000000.00, 1, 'AVAILABLE', 1, '2025-11-28 16:03:55', '2025-11-28 16:03:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('ADMIN','CUSTOMER') NOT NULL DEFAULT 'CUSTOMER',
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `phone`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@petshop.test', '$2y$10$oLLx8xmkMWVlpUrtD3zSmeeo/Kb4OjQBmOXRDk.JhiVRAWuL8rH5i', '0900000000', 'ADMIN', 'ACTIVE', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(2, 'Nguyen Van A', 'a.customer@petshop.test', '$2y$10$3KejE8fMKqzzPsdRGPpoButXg.gYvggYJGOC6RO4rq.p4oZNVNJnq', '0911111111', 'CUSTOMER', 'ACTIVE', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(3, 'Tran Thi B', 'b.customer@petshop.test', '$2y$10$c5xA/34VHBHrGA98PhhOL.8JCEk1VZuLzPSzdQLAQyD2KE05cS4Z6', '0922222222', 'CUSTOMER', 'ACTIVE', '2025-11-28 15:55:23', '2025-11-28 15:55:23'),
(4, 'Khương', 'a@gmail.com', '$2y$10$w/UYMAC6xhJ8dQx1MeJz0u.3dNP.clhtoYVkCH/TNXGbKW9eV4UY6', '11111111', 'CUSTOMER', 'ACTIVE', '2025-11-28 16:08:13', '2025-11-28 16:08:13');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_all_products`
-- (See below for the actual view)
--
CREATE TABLE `v_all_products` (
`product_type` varchar(9)
,`product_id` int(11)
,`name` varchar(120)
,`price` decimal(10,2)
,`stock` int(11)
,`status` varchar(12)
,`category` varchar(100)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_all_products`
--
DROP TABLE IF EXISTS `v_all_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_all_products`  AS SELECT 'PET' AS `product_type`, `p`.`id` AS `product_id`, `p`.`name` AS `name`, `p`.`price` AS `price`, `p`.`stock` AS `stock`, `p`.`status` AS `status`, `c`.`name` AS `category` FROM (`pets` `p` join `categories` `c` on(`p`.`category_id` = `c`.`id`))union all select 'ACCESSORY' AS `product_type`,`a`.`id` AS `product_id`,`a`.`name` AS `name`,`a`.`price` AS `price`,`a`.`stock` AS `stock`,`a`.`status` AS `status`,`c`.`name` AS `category` from (`accessories` `a` join `categories` `c` on(`a`.`category_id` = `c`.`id`))  ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `accessories`
--
ALTER TABLE `accessories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accessories_category` (`category_id`),
  ADD KEY `idx_accessories_status` (`status`),
  ADD KEY `idx_accessories_visible` (`is_visible`);

--
-- Chỉ mục cho bảng `breeds`
--
ALTER TABLE `breeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_breeds_type` (`pet_type`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`item_type`,`item_id`),
  ADD KEY `idx_cart_user` (`user_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_categories_parent` (`parent_id`);

--
-- Chỉ mục cho bảng `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_images_item` (`item_type`,`item_id`),
  ADD KEY `idx_images_primary` (`item_type`,`item_id`,`is_primary`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_details_order` (`order_id`),
  ADD KEY `idx_order_details_item` (`item_type`,`item_id`);

--
-- Chỉ mục cho bảng `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pets_category` (`category_id`),
  ADD KEY `idx_pets_breed` (`breed_id`),
  ADD KEY `idx_pets_status` (`status`),
  ADD KEY `idx_pets_visible` (`is_visible`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `accessories`
--
ALTER TABLE `accessories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `breeds`
--
ALTER TABLE `breeds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `accessories`
--
ALTER TABLE `accessories`
  ADD CONSTRAINT `fk_accessories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `fk_pets_breed` FOREIGN KEY (`breed_id`) REFERENCES `breeds` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pets_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
