-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 14, 2025 lúc 10:12 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `sportisa`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nike', 'nike', 'nikelogo.webp', 'Thương hiệu thể thao hàng đầu thế giới', 'active', '2025-05-05 13:04:34', '2025-05-07 03:23:34'),
(2, 'Adidas', 'adidas', 'adidaslogo.webp', 'Thương hiệu thể thao nổi tiếng', 'active', '2025-05-05 13:04:34', '2025-05-07 03:24:01'),
(3, 'Puma', 'puma', NULL, 'Thương hiệu thể thao đẳng cấp', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 'Under Armour', 'under-armour', NULL, 'Thương hiệu thể thao chuyên nghiệp', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(5, 'New Balance', 'new-balance', NULL, 'Thương hiệu giày thể thao nổi tiếng', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(6, 'Asics', 'asics', NULL, 'Thương hiệu giày chạy bộ hàng đầu', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(7, 'Reebok', 'reebok', NULL, 'Thương hiệu thể thao đẳng cấp', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(2, 1, 5, 2, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(3, 2, 7, 1, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 3, 10, 1, '2025-05-05 13:04:34', '2025-05-05 13:04:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `categories_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`, `categories_name`) VALUES
(1, 'Bóng đá', 'do-bong-da', 'Giày bóng đá chính hãng', 'active', '2025-05-13 07:47:24', '2025-05-13 07:47:24', 'football'),
(2, 'Bóng rổ', 'do-bong-ro', 'Giày chạy bộ chất lượng cao', 'active', '2025-05-13 07:52:43', '2025-05-13 07:52:43', 'basketball'),
(3, 'Thể hình', 'do-the-hinh', 'Giày bóng rổ chuyên nghiệp', 'active', '2025-05-13 07:52:43', '2025-05-13 07:52:43', 'fitness'),
(4, 'Tennis', 'do-tennis', 'Áo thể thao thời trang', 'active', '2025-05-13 07:52:43', '2025-05-13 07:52:43', 'tennis'),
(5, 'Chạy bộ', 'do-chay-bo', 'Quần thể thao thoải mái', 'active', '2025-05-13 07:52:43', '2025-05-13 07:52:43', 'running');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn D', 'nguyenvand@example.com', '0123456789', 'Tôi muốn biết thêm về chính sách đổi trả sản phẩm', 'replied', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(2, 'Trần Thị E', 'tranthie@example.com', '0987654321', 'Cần tư vấn về giày chạy bộ', 'read', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(3, 'Lê Văn F', 'levanf@example.com', '0369852147', 'Sản phẩm nhận được không đúng với mô tả', 'new', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 'Quý Thịnh', 'thinhcound96@gmail.com', '0869114177', 'Lỗi sản phẩm', 'new', '2025-05-06 15:19:55', '2025-05-06 15:19:55'),
(5, 'Nam Thiên', 'thinhcound123@gmail.com', '0869114177', '111111', 'new', '2025-05-06 15:25:11', '2025-05-06 15:25:11'),
(6, 'Tieến Black', 'thinhlu@gmail.com', '0775587366', 'Sách sẽ', 'new', '2025-05-06 15:36:38', '2025-05-06 15:36:38'),
(7, 'Nam Thiên', 'thinhcound964@gmail.com', '0869114177', '111111xx', 'new', '2025-05-06 15:37:42', '2025-05-06 15:37:42'),
(8, 'Nam Thiên5r', 'thinhcound123@gmail.com', '0869114177', '1111111', 'new', '2025-05-06 15:45:28', '2025-05-06 15:45:28'),
(9, 'Nam Thiên', 'thinhcound96@gmail.co', '0869114177', 'qưqwwqqwqwqwqw', 'new', '2025-05-06 15:57:37', '2025-05-06 15:57:37'),
(10, 'Nam Thiên 43', 'thinhcound13@gmail.com', '0869114177', 'Thập cầme', 'new', '2025-05-12 15:06:35', '2025-05-12 15:06:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `content`, `image`, `author_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Giới thiệu dòng giày mới Nike Air Max', 'gioi-thieu-dong-giay-moi-nike-air-max', 'Nike vừa ra mắt dòng giày Air Max mới với nhiều cải tiến...', 'nike-air-max.jpg', 1, 'published', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(2, 'Cách chọn giày chạy bộ phù hợp', 'cach-chon-giay-chay-bo-phu-hop', 'Hướng dẫn chi tiết cách chọn giày chạy bộ phù hợp với nhu cầu...', 'chon-giay-chay-bo.jpg', 1, 'published', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(3, 'Xu hướng thời trang thể thao 2023', 'xu-huong-thoi-trang-the-thao-2023', 'Cập nhật những xu hướng thời trang thể thao mới nhất năm 2023...', 'thoi-trang-the-thao-2023.jpg', 1, 'published', '2025-05-05 13:04:34', '2025-05-05 13:04:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_name` varchar(100) NOT NULL,
  `payment_method` enum('cod','bank_transfer','credit_card') NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_address`, `shipping_phone`, `shipping_name`, `payment_method`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 8000000.00, '456 Đường XYZ, Quận 2, TP.HCM', '0987654321', 'Trần Thị B', 'bank_transfer', 'pending', NULL, '2025-05-05 13:04:34', '2025-05-12 03:35:05'),
(2, 3, 4500000.00, '789 Đường DEF, Quận 3, TP.HCM', '0369852147', 'Lê Văn C', 'cod', 'pending', NULL, '2025-05-05 13:04:34', '2025-05-12 03:35:09'),
(3, 2, 3600000.00, '456 Đường XYZ, Quận 2, TP.HCM', '0987654321', 'Trần Thị B', 'credit_card', 'pending', NULL, '2025-05-05 13:04:34', '2025-05-12 03:35:07'),
(4, 5, 7700000.00, 'Bình Định, Qui Nhơn, NESSS', '', '', 'cod', 'cancelled', '', '2025-05-07 03:40:26', '2025-05-12 14:58:23'),
(5, 5, 8400000.00, 'Bình Định, Qui Nhơn', '', '', 'cod', 'cancelled', 'giao hàng đúng nơi nhé!', '2025-05-13 15:41:10', '2025-05-14 03:53:16'),
(6, 5, 5700000.00, 'Bình Định, Qui Nhơn', '', '', 'cod', 'shipped', '', '2025-05-14 03:51:36', '2025-05-14 03:52:50'),
(7, 5, 4300000.00, 'Bình Định, Qui Nhơn', '', '', '', 'pending', 'ssss', '2025-05-14 03:54:24', '2025-05-14 03:54:24'),
(8, 5, 1800000.00, 'Bình Định, Qui Nhơn', '', '', 'cod', 'pending', 'sss', '2025-05-14 07:44:00', '2025-05-14 07:44:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 1, 2, 3500000.00, '2025-05-05 13:04:34'),
(2, 1, 4, 1, 1000000.00, '2025-05-05 13:04:34'),
(3, 2, 3, 1, 4500000.00, '2025-05-05 13:04:34'),
(4, 3, 9, 1, 3600000.00, '2025-05-05 13:04:34'),
(5, 4, 1, 1, 3500000.00, '2025-05-07 03:40:26'),
(6, 4, 2, 1, 4200000.00, '2025-05-07 03:40:26'),
(7, 5, 2, 2, 4200000.00, '2025-05-13 15:41:10'),
(8, 6, 16, 1, 1800000.00, '2025-05-14 03:51:36'),
(9, 6, 17, 1, 2500000.00, '2025-05-14 03:51:36'),
(10, 6, 18, 4, 350000.00, '2025-05-14 03:51:36'),
(11, 7, 16, 1, 1800000.00, '2025-05-14 03:54:24'),
(12, 7, 17, 1, 2500000.00, '2025-05-14 03:54:24'),
(13, 8, 16, 1, 1800000.00, '2025-05-14 07:44:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `image`, `category_id`, `brand_id`, `status`, `featured`, `created_at`, `updated_at`) VALUES
(1, 'Nike Mercurial Vapor 14', 'nike-mercurial-vapor-14', 'Giày bóng đá cao cấp với công nghệ Flyknit', 3500000.00, 3200000.00, 51, NULL, 1, 1, 'active', 1, '2025-05-05 13:04:34', '2025-05-13 08:14:03'),
(2, 'Adidas Ultraboost 21', 'adidas-ultraboost-21', 'Giày chạy bộ với công nghệ Boost', 4200000.00, 4000000.00, 29, NULL, 1, 2, 'active', 1, '2025-05-05 13:04:34', '2025-05-13 15:41:10'),
(3, 'Nike Air Jordan 1', 'nike-air-jordan-1', 'Giày bóng rổ cổ điển', 4500000.00, NULL, 20, NULL, NULL, 1, 'active', 1, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 'Adidas Tiro 21', 'adidas-tiro-21', 'Quần tập luyện thoải mái', 1200000.00, 1000000.00, 100, NULL, NULL, 2, 'active', 0, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(5, 'Nike Dri-FIT', 'nike-dri-fit', 'Áo thể thao thấm hút mồ hôi', 800000.00, NULL, 80, NULL, NULL, 1, 'active', 0, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(6, 'Puma Backpack', 'puma-backpack', 'Balo thể thao đa năng', 900000.00, 800000.00, 40, NULL, NULL, 3, 'active', 0, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(7, 'Under Armour Curry 8', 'under-armour-curry-8', 'Giày bóng rổ chuyên nghiệp', 3800000.00, 3500000.00, 25, NULL, NULL, 4, 'active', 1, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(8, 'New Balance 990v5', 'new-balance-990v5', 'Giày chạy bộ cao cấp', 4000000.00, NULL, 35, NULL, NULL, 5, 'active', 0, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(9, 'Asics Gel-Kayano 28', 'asics-gel-kayano-28', 'Giày chạy bộ hỗ trợ', 3600000.00, 3400000.00, 45, NULL, NULL, 6, 'active', 0, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(10, 'Reebok Nano X1', 'reebok-nano-x1', 'Giày tập luyện đa năng', 2800000.00, 2500000.00, 30, NULL, NULL, 7, 'active', 0, '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(11, 'Giày Bóng Đá Nike Mercurial', 'giay-bong-da-nike-mercurial', 'Giày bóng đá chính hãng Nike Mercurial', 1800000.00, 1500000.00, 100, 'nike-mercurial.jpg', 1, 1, 'active', 1, '2025-05-13 08:08:33', '2025-05-13 08:08:33'),
(12, 'Giày Bóng Rổ Adidas Harden', 'giay-bong-ro-adidas-harden', 'Giày bóng rổ chất lượng cao từ Adidas', 2200000.00, 1900000.00, 80, 'adidas-harden.jpg', 2, 1, 'active', 1, '2025-05-13 08:08:33', '2025-05-13 08:08:33'),
(13, 'Găng Tay Tập Gym', 'gang-tay-tap-gym', 'Găng tay thể hình chuyên dụng cho phòng tập', 350000.00, 299000.00, 150, 'gang-tay-gym.jpg', 3, 1, 'active', 0, '2025-05-13 08:08:33', '2025-05-13 08:08:33'),
(14, 'Vợt Tennis Wilson', 'vot-tennis-wilson', 'Vợt tennis cao cấp Wilson chính hãng', 3200000.00, 2950000.00, 50, 'vot-wilson.jpg', 4, 1, 'active', 1, '2025-05-13 08:08:33', '2025-05-13 08:08:33'),
(15, 'Giày Chạy Bộ Asics Gel', 'giay-chay-bo-asics-gel', 'Giày chạy bộ chuyên dụng Asics Gel', 1700000.00, 1490000.00, 120, 'asics-gel.jpg', 5, 1, 'active', 0, '2025-05-13 08:08:33', '2025-05-13 08:08:33'),
(16, 'Giày Bóng Đá Nike Tiempo', 'giay-bong-da-nike-tiempo', 'Giày bóng đá chính hãng Nike Tiempo dành cho sân cỏ nhân tạo.', 1800000.00, 1500000.00, 97, 'nike-tiempo.jpg', 1, 1, 'active', 1, '2025-05-13 08:20:53', '2025-05-14 07:44:00'),
(17, 'Giày Bóng Rổ Jordan Zion', 'giay-bong-ro-jordan-zion', 'Giày bóng rổ Jordan Zion với đệm Air Zoom và thiết kế thể thao.', 2500000.00, 2150000.00, 78, 'jordan-zion.jpg', 2, 1, 'active', 1, '2025-05-13 08:20:53', '2025-05-14 03:54:24'),
(18, 'Găng Tay Tập Gym Adidas', 'gang-tay-tap-gym-adidas', 'Găng tay thể hình chống trượt, bảo vệ bàn tay khi tập luyện.', 350000.00, 299000.00, 146, 'gangtay-adidas.jpg', 3, 2, 'active', 0, '2025-05-13 08:20:53', '2025-05-14 03:51:36'),
(19, 'Vợt Tennis Yonex Vcore', 'vot-tennis-yonex-vcore', 'Vợt tennis chính hãng Yonex Vcore dành cho người chơi chuyên nghiệp.', 3200000.00, 2950000.00, 50, 'yonex-vcore.jpg', 4, 2, 'active', 1, '2025-05-13 08:20:53', '2025-05-13 08:20:53'),
(20, 'Giày Chạy Bộ Asics GelZ', 'giay-chay-bo-asics-gel-nimbus', 'Giày chạy bộ với đệm GEL giúp giảm chấn tốt, phù hợp mọi địa hình.', 1700000.00, 1490000.00, 120, 'asics-nimbus.jpg', 5, 2, 'active', 0, '2025-05-13 08:20:53', '2025-05-13 09:03:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `is_primary`, `created_at`) VALUES
(1, 1, 'mercurial-vapor-14-1.jpg', 1, '2025-05-05 13:04:34'),
(2, 1, 'mercurial-vapor-14-2.jpg', 0, '2025-05-05 13:04:34'),
(3, 1, 'mercurial-vapor-14-3.jpg', 0, '2025-05-05 13:04:34'),
(4, 2, 'ultraboost-21-1.jpg', 1, '2025-05-05 13:04:34'),
(5, 2, 'ultraboost-21-2.jpg', 0, '2025-05-05 13:04:34'),
(6, 3, 'air-jordan-1-1.jpg', 1, '2025-05-05 13:04:34'),
(7, 3, 'air-jordan-1-2.jpg', 0, '2025-05-05 13:04:34'),
(8, 4, 'tiro-21-1.jpg', 1, '2025-05-05 13:04:34'),
(9, 5, 'dri-fit-1.jpg', 1, '2025-05-05 13:04:34'),
(10, 6, 'puma-backpack-1.jpg', 1, '2025-05-05 13:04:34'),
(11, 7, 'curry-8-1.jpg', 1, '2025-05-05 13:04:34'),
(12, 8, '990v5-1.jpg', 1, '2025-05-05 13:04:34'),
(13, 9, 'gel-kayano-28-1.jpg', 1, '2025-05-05 13:04:34'),
(14, 10, 'nano-x1-1.jpg', 1, '2025-05-05 13:04:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5, 'Giày rất đẹp và thoải mái, chất lượng tốt', 'approved', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(2, 3, 2, 4, 'Giày chạy bộ rất êm, nhưng giá hơi cao', 'approved', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(3, 2, 3, 5, 'Thiết kế cổ điển, chất liệu tốt', 'approved', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 1, 4, 3, 'Quần hơi dài so với size', 'approved', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(5, 3, 5, 4, 'Áo thấm hút mồ hôi tốt', 'approved', '2025-05-05 13:04:34', '2025-05-05 13:04:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'SPORTISA', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(2, 'site_description', 'Cửa hàng thể thao chính hãng', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(3, 'contact_email', 'contact@sportisa.com', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 'contact_phone', '0123456789', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(5, 'address', '123 Đường ABC, Quận XYZ, TP.HCM', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(6, 'shipping_fee', '30000', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(7, 'facebook_url', 'https://facebook.com/sportisa', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(8, 'instagram_url', 'https://instagram.com/sportisa', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(9, 'twitter_url', 'https://twitter.com/sportisa', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(10, 'youtube_url', 'https://youtube.com/sportisa', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(11, 'return_policy', 'Chấp nhận đổi trả trong vòng 7 ngày', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(12, 'shipping_policy', 'Miễn phí vận chuyển cho đơn hàng trên 1.000.000đ', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(13, 'payment_policy', 'Chấp nhận thanh toán qua thẻ tín dụng, chuyển khoản và COD', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(14, 'about_us', 'SPORTISA - Cửa hàng thể thao chính hãng hàng đầu Việt Nam', '2025-05-05 13:04:34', '2025-05-05 13:04:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@sportisa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', NULL, NULL, 'admin', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(2, 'user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0123456789', '123 Đường ABC, Quận 1, TP.HCM', 'user', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(3, 'user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0987654321', '456 Đường XYZ, Quận 2, TP.HCM', 'user', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(4, 'user3', 'user3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0369852147', '789 Đường DEF, Quận 3, TP.HCM', 'user', 'active', '2025-05-05 13:04:34', '2025-05-05 13:04:34'),
(5, 'Trần Quý Thịnh', 'thinhcound96@gmail.com', '$2y$10$pHLwzRmLN8Ttytib2sJfGO96JqDAP8bDiAla3acezPSXFMvCWIci.', 'Trần Quý Thịnh', '0775587366', 'Bình Định, Qui Nhơn', 'user', 'active', '2025-05-05 13:06:09', '2025-05-14 07:43:33');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
