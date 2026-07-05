-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2026 at 05:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raksa_coffee`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `location` text DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `img_shop` varchar(255) DEFAULT 'default_shop.png',
  `is_open` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `location`, `phone`, `manager_id`, `img_shop`, `is_open`) VALUES
(1, 'Tuek Thla, Phnom Penh', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d850.413565652243!2d104.87333482480555!3d11.556945210260745!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310951f7383f6c91%3A0xbc2e3dc5224ef47d!2sThe%20Coffee%20Shop%20st.2002!5e0!3m2!1sen!2skh!4v1782988182357!5m2!1sen!2skh', '0975570191', 10, 'coffee_shop.png', 1),
(2, 'Toul Kork, Phnom Penh', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7817.243045770525!2d104.89636349488447!3d11.578966863723222!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310951ccd499f4b3%3A0x87b0e84721684567!2sDELI%20tk%20Coffee%20%26%20Eatery!5e0!3m2!1sen!2skh!4v1783009987054!5m2!1sen!2skh', '016631848', 29, '1783010154_cozy-coffee-shop-interior-stylish-decor-soft-lighting-warm-ambiance-step-inviting-embrace-coffee-shops-cozy-298897043.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Iced Coffee', NULL),
(2, 'Hot Coffee', 'Back Coffee and high caffeine'),
(3, 'Ice Cream', NULL),
(4, 'Bread', 'Fast Food'),
(5, 'Beverages', NULL),
(6, 'Snacks', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `join_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `phone`, `loyalty_points`, `join_date`) VALUES
(1, 'Saran Koum', '0976066695', 0, '2026-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`item_id`, `item_name`, `unit`, `branch_id`) VALUES
(1, 'Fresh Milk', 'Bottle', 1),
(2, 'Coffee Beans', 'Kg', 1),
(3, 'Glass Coffee', 'Glass', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(150) NOT NULL,
  `type` varchar(20) NOT NULL,
  `status` varchar(15) DEFAULT 'unread',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `email`, `phone`, `type`, `status`, `created_at`) VALUES
(4, 'reaksakun93@gmail.com', '060394664', 'Password Reset', 'unread', '2026-07-01 13:09:22'),
(5, 'seanghaihorn9@gmail.com', '+85560394664', 'Password Reset', 'unread', '2026-07-01 13:31:01'),
(6, 'kunr99762@gmail.com', '0123456789', 'Password Reset', 'read', '2026-07-03 18:24:36'),
(7, 'youry1234@gmail.com', '9876543456', 'Password Reset', 'read', '2026-07-04 16:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL,
  `cash_received` decimal(10,2) DEFAULT 0.00,
  `cash_change` decimal(10,2) DEFAULT 0.00,
  `status` varchar(30) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `payment_method` enum('Cash','QR') DEFAULT 'Cash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `subtotal`, `discount_amount`, `grand_total`, `cash_received`, `cash_change`, `status`, `shift_id`, `user_id`, `customer_id`, `promo_id`, `payment_method`) VALUES
(7, '2026-06-24 06:59:18', 5.25, 0.00, 5.25, 6.00, 0.75, 'Paid', 8, 11, NULL, NULL, 'Cash'),
(8, '2026-06-24 18:50:08', 2.25, 0.00, 2.25, 2.25, 0.00, 'Paid', 9, 11, NULL, NULL, 'QR'),
(9, '2026-06-29 21:00:48', 2.75, 0.00, 2.75, 2.75, 0.00, 'Paid', 12, 11, NULL, NULL, 'QR'),
(10, '2026-06-29 21:01:27', 7.00, 0.00, 7.00, 7.00, 0.00, 'Paid', 12, 11, NULL, NULL, 'Cash'),
(11, '2026-06-29 21:02:47', 2.25, 0.00, 2.25, 2.25, 0.00, 'Paid', 12, 11, NULL, NULL, 'Cash'),
(12, '2026-06-29 22:03:46', 2.50, 0.00, 2.50, 2.50, 0.00, 'Paid', 12, 11, NULL, NULL, 'Cash'),
(13, '2026-06-29 22:05:19', 1.25, 0.00, 1.25, 1.25, 0.00, 'Paid', 12, 11, NULL, NULL, 'QR'),
(14, '2026-06-29 22:06:31', 1.50, 0.00, 1.50, 1.50, 0.00, 'Paid', 12, 11, NULL, NULL, 'QR'),
(15, '2026-07-01 13:12:37', 4.75, 0.00, 4.75, 4.75, 0.00, 'Paid', 20, 23, NULL, NULL, 'QR'),
(16, '2026-07-01 13:22:16', 1.00, 0.00, 1.00, 2.00, 1.00, 'Paid', 21, 23, NULL, NULL, 'Cash'),
(17, '2026-07-01 16:25:51', 3.25, 0.00, 3.25, 3.25, 0.00, 'Paid', 22, 26, NULL, NULL, 'QR'),
(18, '2026-07-01 19:01:20', 2.25, 0.00, 2.25, 2.25, 0.00, 'Paid', 23, 27, NULL, NULL, 'QR'),
(19, '2026-07-03 00:30:36', 3.75, 0.00, 3.75, 3.75, 0.00, 'Paid', 24, 30, NULL, NULL, 'QR'),
(20, '2026-07-03 00:30:54', 3.25, 0.00, 3.25, 3.25, 0.00, 'Paid', 24, 30, NULL, NULL, 'Cash'),
(21, '2026-07-03 00:31:01', 2.25, 0.00, 2.25, 2.25, 0.00, 'Paid', 24, 30, NULL, NULL, 'QR'),
(22, '2026-07-03 00:47:40', 1.25, 0.00, 1.25, 1.25, 0.00, 'Paid', 27, 11, NULL, NULL, 'QR'),
(23, '2026-07-03 00:47:47', 1.00, 0.00, 1.00, 1.00, 0.00, 'Paid', 26, 30, NULL, NULL, 'QR'),
(24, '2026-07-03 09:32:13', 2.25, 0.00, 2.25, 2.25, 0.00, 'Paid', 28, 31, NULL, NULL, 'QR'),
(25, '2026-07-03 12:58:33', 8.50, 0.00, 8.50, 10.00, 1.50, 'Paid', 29, 11, NULL, NULL, 'Cash'),
(26, '2026-07-03 18:18:45', 8.75, 0.00, 8.75, 8.75, 0.00, 'Paid', 31, 32, NULL, NULL, 'QR'),
(27, '2026-07-04 13:31:47', 1.00, 0.00, 1.00, 1.00, 0.00, 'Paid', 33, 11, NULL, NULL, 'QR'),
(28, '2026-07-04 14:42:20', 2.75, 0.00, 2.75, 3.00, 0.25, 'Paid', 34, 11, NULL, NULL, 'Cash'),
(29, '2026-07-04 16:43:40', 7.50, 0.00, 7.50, 10.00, 2.50, 'Paid', 35, 11, NULL, NULL, 'Cash'),
(30, '2026-07-04 20:03:36', 1.25, 0.00, 1.25, 1.50, 0.25, 'Paid', 36, 33, NULL, NULL, 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`detail_id`, `qty`, `unit_price`, `line_total`, `order_id`, `product_id`) VALUES
(7, 2.00, 1.25, 2.50, 7, 4),
(8, 1.00, 1.25, 1.25, 7, 3),
(9, 1.00, 1.50, 1.50, 7, 2),
(10, 1.00, 1.25, 1.25, 8, 1),
(11, 1.00, 1.00, 1.00, 8, 7),
(12, 1.00, 1.25, 1.25, 9, 1),
(13, 1.00, 1.50, 1.50, 9, 2),
(14, 2.00, 1.00, 2.00, 10, 8),
(15, 1.00, 1.00, 1.00, 10, 7),
(16, 2.00, 1.25, 2.50, 10, 6),
(17, 1.00, 1.50, 1.50, 10, 2),
(18, 1.00, 1.25, 1.25, 11, 1),
(19, 1.00, 1.00, 1.00, 11, 5),
(20, 1.00, 1.25, 1.25, 12, 12),
(21, 1.00, 1.25, 1.25, 12, 13),
(22, 1.00, 1.25, 1.25, 13, 9),
(23, 1.00, 1.50, 1.50, 14, 2),
(24, 1.00, 1.00, 1.00, 15, 7),
(25, 1.00, 1.25, 1.25, 15, 6),
(26, 1.00, 1.00, 1.00, 15, 5),
(27, 1.00, 1.50, 1.50, 15, 2),
(28, 1.00, 1.00, 1.00, 16, 14),
(29, 1.00, 1.25, 1.25, 17, 1),
(30, 1.00, 1.00, 1.00, 17, 7),
(31, 1.00, 1.00, 1.00, 17, 10),
(32, 1.00, 1.00, 1.00, 18, 14),
(33, 1.00, 1.25, 1.25, 18, 12),
(34, 1.00, 1.50, 1.50, 19, 2),
(35, 1.00, 1.25, 1.25, 19, 6),
(36, 1.00, 1.00, 1.00, 19, 5),
(37, 1.00, 1.00, 1.00, 20, 8),
(38, 1.00, 1.00, 1.00, 20, 7),
(39, 1.00, 1.25, 1.25, 20, 6),
(40, 1.00, 1.25, 1.25, 21, 12),
(41, 1.00, 1.00, 1.00, 21, 11),
(42, 1.00, 1.25, 1.25, 22, 3),
(43, 1.00, 1.00, 1.00, 23, 8),
(44, 1.00, 1.00, 1.00, 24, 14),
(45, 1.00, 1.25, 1.25, 24, 13),
(46, 1.00, 1.25, 1.25, 25, 4),
(47, 1.00, 1.25, 1.25, 25, 3),
(48, 4.00, 1.50, 6.00, 25, 2),
(49, 1.00, 1.25, 1.25, 26, 4),
(50, 6.00, 1.25, 7.50, 26, 3),
(51, 1.00, 1.00, 1.00, 27, 7),
(52, 1.00, 1.50, 1.50, 28, 2),
(53, 1.00, 1.25, 1.25, 28, 3),
(54, 5.00, 1.50, 7.50, 29, 2),
(55, 1.00, 1.25, 1.25, 30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `payment_method`, `amount_paid`, `payment_date`, `order_id`) VALUES
(1, 'Cash', 5.25, '2026-06-24 06:59:18', 7),
(2, 'QR', 2.25, '2026-06-24 18:50:08', 8),
(3, 'QR', 2.75, '2026-06-29 21:00:48', 9),
(4, 'Cash', 7.00, '2026-06-29 21:01:27', 10),
(5, 'Cash', 2.25, '2026-06-29 21:02:47', 11),
(6, 'Cash', 2.50, '2026-06-29 22:03:46', 12),
(7, 'QR', 1.25, '2026-06-29 22:05:19', 13),
(8, 'QR', 1.50, '2026-06-29 22:06:31', 14),
(9, 'QR', 4.75, '2026-07-01 13:12:37', 15),
(10, 'Cash', 1.00, '2026-07-01 13:22:16', 16),
(11, 'QR', 3.25, '2026-07-01 16:25:51', 17),
(12, 'QR', 2.25, '2026-07-01 19:01:20', 18),
(13, 'QR', 3.75, '2026-07-03 00:30:36', 19),
(14, 'Cash', 3.25, '2026-07-03 00:30:54', 20),
(15, 'QR', 2.25, '2026-07-03 00:31:01', 21),
(16, 'QR', 1.25, '2026-07-03 00:47:40', 22),
(17, 'QR', 1.00, '2026-07-03 00:47:47', 23),
(18, 'QR', 2.25, '2026-07-03 09:32:13', 24),
(19, 'Cash', 8.50, '2026-07-03 12:58:33', 25),
(20, 'QR', 8.75, '2026-07-03 18:18:45', 26),
(21, 'QR', 1.00, '2026-07-04 13:31:47', 27),
(22, 'Cash', 2.75, '2026-07-04 14:42:20', 28),
(23, 'Cash', 7.50, '2026-07-04 16:43:40', 29),
(24, 'Cash', 1.25, '2026-07-04 20:03:36', 30);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `base_price`, `image`, `is_available`, `category_id`) VALUES
(1, 'Iced Latte', 1.25, 'Iced_Latte.jpg', 1, 1),
(2, 'Matcha Latte', 1.50, '1780928939_6a26d1ab08983.jpg', 1, 1),
(3, 'Americano', 1.25, '1781106860_6a2988ac066e8.png', 1, 2),
(4, 'Cappuccino', 1.25, '1781107268_6a298a44c29bb.webp', 1, 2),
(5, 'Frozen Desserts', 1.00, '1781107543_6a298b57aba17.jpg', 1, 3),
(6, 'Mocha', 1.25, '1781107737_6a298c197cdb9.webp', 1, 2),
(7, 'Croissant', 1.00, '1781108342_6a298e763418a.jpg', 1, 4),
(8, 'Donut', 1.00, '1781108471_6a298ef7a4a37.jpg', 1, 4),
(9, 'Coffee Milk', 1.25, '1782742105_6a427c598a5b5.webp', 1, 1),
(10, 'Lemon Green Tea', 1.00, '1782744495_6a4285afb7609.jpg', 1, 5),
(11, 'Lemon Tea', 1.00, '1782744667_6a42865ba1249.webp', 1, 5),
(12, 'Tea Milk Green', 1.25, '1782745214_6a42887ea9d37.webp', 1, 5),
(13, 'Milk Tea', 1.25, '1782745331_6a4288f309372.webp', 1, 5),
(14, 'Sandwich', 1.00, '1782886608_6a44b0d004d8e.webp', 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promo_id` int(11) NOT NULL,
  `promo_name` varchar(100) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`promo_id`, `promo_name`, `discount_percent`, `start_date`, `end_date`, `is_active`) VALUES
(1, 'New Shop', 5.00, '2026-07-01', '2026-07-03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` varchar(30) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `order_date`, `total_amount`, `status`, `supplier_id`, `branch_id`) VALUES
(2, '2026-06-09 16:48:27', 66.00, 'Completed', 2, 1),
(3, '2026-06-10 22:40:16', 13.00, 'Completed', 2, 1),
(4, '2026-07-01 16:29:42', 50.00, 'Completed', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `po_detail_id` int(11) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_details`
--

INSERT INTO `purchase_order_details` (`po_detail_id`, `qty`, `unit_price`, `line_total`, `po_id`, `item_id`) VALUES
(1, 3.00, 22.00, 66.00, 2, 2),
(2, 100.00, 0.13, 13.00, 3, 3),
(3, 25.00, 2.00, 50.00, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `starting_cash` decimal(10,2) NOT NULL,
  `expected_cash` decimal(10,2) DEFAULT NULL,
  `expected_qr` decimal(10,2) DEFAULT 0.00,
  `actual_cash` decimal(10,2) DEFAULT NULL,
  `status` varchar(30) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `shift_date`, `start_time`, `end_time`, `starting_cash`, `expected_cash`, `expected_qr`, `actual_cash`, `status`, `user_id`, `branch_id`) VALUES
(8, '2026-06-24', '06:44:38', '08:06:31', 100.00, 5.25, 0.00, 5.25, '0', 11, 1),
(9, '2026-06-24', '18:48:52', '18:51:37', 50.00, NULL, 2.25, 0.00, '0', 11, 1),
(12, '2026-06-29', '20:58:04', '22:07:23', 100.00, 11.75, 5.50, 11.75, '0', 11, 1),
(20, '2026-07-01', '13:11:53', '13:13:47', 50.00, NULL, 4.75, 0.00, '0', 23, 1),
(21, '2026-07-01', '13:20:28', '13:22:51', 100.00, 1.00, 0.00, 0.00, '0', 23, 1),
(22, '2026-07-01', '16:24:05', '16:26:43', 100.00, NULL, 3.25, 0.00, '0', 26, 1),
(23, '2026-07-01', '18:59:24', '19:01:52', 100.00, NULL, 2.25, 0.00, '0', 27, 1),
(24, '2026-07-03', '00:30:15', '00:31:31', 100.00, 3.25, 6.00, 3.25, '0', 30, 2),
(26, '2026-07-03', '00:43:59', '00:48:36', 100.00, NULL, 1.00, 0.00, '0', 30, 2),
(27, '2026-07-03', '00:46:34', '00:48:47', 100.00, NULL, 1.25, 0.00, '0', 11, 1),
(28, '2026-07-03', '09:29:13', '09:42:50', 100.00, NULL, 2.25, 0.00, '0', 31, 2),
(29, '2026-07-03', '12:57:36', '12:59:59', 100.00, 8.50, 0.00, 0.00, '0', 11, 1),
(30, '2026-07-03', '13:21:04', '13:46:47', 100.00, NULL, 0.00, 0.00, '0', 11, 1),
(31, '2026-07-03', '18:16:47', '18:20:00', 100.00, NULL, 8.75, NULL, '0', 32, 2),
(33, '2026-07-04', '13:07:05', '13:10:00', 100.00, NULL, 1.00, NULL, '0', 11, 1),
(34, '2026-07-04', '14:39:42', '14:42:46', 100.00, 2.75, 0.00, 2.75, '0', 11, 1),
(35, '2026-07-04', '16:42:19', '16:49:23', 100.00, 7.50, 0.00, 7.50, '0', 11, 1),
(36, '2026-07-04', '20:02:03', '20:04:19', 100.00, 1.25, 0.00, 1.25, '0', 33, 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `position` varchar(50) NOT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `phone` varchar(20) NOT NULL,
  `hire_date` date NOT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `full_name`, `position`, `salary`, `phone`, `hire_date`, `branch_id`) VALUES
(1, 'Kun Raksa', 'Manager', 500.00, '(097)5570191', '2026-06-06', 1),
(3, 'Chhay Youry', 'Cashier', 100.00, '(012)542255', '2026-06-07', 1),
(6, 'Youry', 'Cashier', 100.00, '0975570191', '2026-06-30', 1),
(7, 'Houn Seanghei', 'Cashier', 100.00, '060394664', '2026-06-30', 1),
(8, 'Test Email', 'Cashier', 100.00, '0314222596', '2026-07-01', 1),
(9, 'Phal Cute', 'Cashier', 100.00, '0967441437', '2026-07-01', 1),
(10, 'Kun Rachana', 'Manager', 500.00, '016631848', '2026-07-02', 2),
(11, 'Join Son', 'Cashier', 100.00, '0123456789', '2026-07-04', 2),
(12, 'Yury Cute', 'Cashier', 100.00, '0123456674', '2026-07-04', 2),
(13, 'Bun Sokong', 'Cashier', 100.00, '081323659', '2026-07-03', 2),
(14, 'Ranuth\r\n', 'Cashier', 100.00, '098765432', '2026-07-03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(150) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `phone`, `address`) VALUES
(1, 'Lucky Mall', 'Join Smit', '(012)345678', 'Phnom Penh'),
(2, 'New Market', 'Mark Elon', '(012)0975678', 'Phnom Penh'),
(3, 'Supplier Local', 'Join Son', '(097)1234567', 'Phnom Penh');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `image_staff` varchar(255) DEFAULT 'default_profile.png',
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `staff_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `code_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `image_staff`, `password_hash`, `role`, `is_active`, `staff_id`, `branch_id`, `verification_code`, `code_expires_at`) VALUES
(10, 'Raksa Kun', 'reaksakun93@gmail.com', '1780809679_photo_2026-02-22_08-48-16.jpg', '$2y$10$eehHjYtwiRfloiTWhn6lJO1NXN05mmtvmP06xrCILsC70xkOVWl/e', 'Admin', 1, 1, 1, NULL, NULL),
(11, 'Chhay Youry', 'youry1234@gmail.com', 'default_profile.png', '$2y$10$HLTzN8CMQwSLVBfU6b1KQO82Q/cgoDjgRwafliikJ6KQl3JH/EpA2', 'Cashier', 1, 3, 1, NULL, NULL),
(22, 'Youry', 'youryraksa@gmail.com', '1782878637_logo bird1-01.jpg', '$2y$10$54Eyk7psaK0Q8duVzru85u3MTfiyqBl/m049AlLlY7LHfIumUWyEq', 'Cashier', 1, 6, 1, NULL, NULL),
(23, 'Houn Seanghei', 'seanghaihorn9@gmail.com', '1782885932_robot (1).png', '$2y$10$YgmxREnXEJwoaqx9kNH2EePVjJMgCD8wT3c.OPy.Y4.CknhHyOeKO', 'Cashier', 1, 7, 1, NULL, NULL),
(26, 'Test Email', 'businessraksa99@gmail.com', '1782897730_Untitled-2-01.jpg', '$2y$10$1Jtnxf0ln.U6056kQhj.mORo53qd9NLs1GGZmx2nqYfwFrUJZYlb.', 'Cashier', 1, 8, 1, NULL, NULL),
(27, 'Phal Cute', 'sphat4080@gmail.com', '1782907007_photo_2025-01-06_17-30-39.jpg', '$2y$10$fnwWFsWL4WDKE798po.jDOCbwswkt1vw2lR6XJ76nwcBjWrEMLITy', 'Cashier', 1, 9, 1, NULL, NULL),
(29, 'Kun Rachana', 'kunrachana1@gmail.com', '1783009650_photo_2025-10-11_18-51-29.jpg', '$2y$10$JukTehyVobqfwf4t.CoYvOBDfoMvl5Y8un2PkFPJlZeOgUGzC5evC', 'Admin', 1, 10, 2, NULL, NULL),
(30, 'Join Son', 'kunr99762@gmail.com', '1783013215_sasa.jpg', '$2y$10$.Wwj5y5rwthTMC9/tTl.OORtVHL6cGdKGR0CWcS41IwCyDjGYOTLK', 'Cashier', 1, 11, 2, NULL, NULL),
(31, 'Yury Cute', 'chhayyoury7@gmail.com', '1783045621_photo_2026-05-03_22-09-08.jpg', '$2y$10$oO0xeS7FQL2T1PIL//WcfeTcd0yNdsfuUGbECWhk6oP0Fp6GkzO4m', 'Cashier', 1, 12, 2, NULL, NULL),
(32, 'Bun Sokong', 'sokong301@gmail.com', '1783077315_photo_2026-07-03_18-14-54.jpg', '$2y$10$wBq9QKLqXBniCzWBKBLqsOyCQxWv5Cole00PH.6Ov64N5IhSWo.a6', 'Cashier', 1, 13, 2, NULL, NULL),
(33, 'ranuth', 'sxingsxing33@gmail.com', '1783170061_photo_2026-07-03_19-53-58.jpg', '$2y$10$EfWZQle3Uu5d8kQOLVG8f.3.ecU16KjAPU/FSXMOttLJPrDs85oZ.', 'Cashier', 1, 14, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promo_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`po_detail_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `po_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`promo_id`) REFERENCES `promotions` (`promo_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD CONSTRAINT `purchase_order_details_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`),
  ADD CONSTRAINT `purchase_order_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`);

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `shifts_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
