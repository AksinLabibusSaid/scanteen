-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2026 at 08:27 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scanteen`
--

-- --------------------------------------------------------

--
-- Table structure for table `dining_tables`
--

CREATE TABLE `dining_tables` (
  `id` bigint UNSIGNED NOT NULL,
  `venue_id` bigint UNSIGNED NOT NULL,
  `table_number` varchar(32) NOT NULL,
  `barcode_token` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dining_tables`
--

INSERT INTO `dining_tables` (`id`, `venue_id`, `table_number`, `barcode_token`, `is_active`, `created_at`) VALUES
(1, 1, '12', 'scan_demo_meja_12', 1, '2026-05-15 05:55:39'),
(2, 1, '1', 'TBL-db5d0835', 1, '2026-05-15 20:06:32'),
(3, 1, '2', 'TBL-de027087', 1, '2026-05-16 14:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint UNSIGNED NOT NULL,
  `warung_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `image_url` varchar(512) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `warung_id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image_url`, `is_available`, `created_at`) VALUES
(1, 1, 2, 'Wader Goreng', NULL, '25000.00', 5, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1, '2026-05-15 05:55:39'),
(2, 1, 2, 'Soto Babat', NULL, '25000.00', 10, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 1, '2026-05-15 05:55:39'),
(3, 1, 2, 'Mie Instan', NULL, '12000.00', 0, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 0, '2026-05-15 05:55:39'),
(4, 2, 2, 'Rawon Jumbo', NULL, '25000.00', 0, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 0, '2026-05-15 05:55:39'),
(5, 2, 2, 'Bubur Ayam', NULL, '18000.00', 0, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 0, '2026-05-15 05:55:39'),
(6, 2, 2, 'Nasi Kuning Telur', NULL, '22000.00', 0, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 0, '2026-05-15 05:55:39'),
(7, 3, 3, 'Es Teh Manis', NULL, '5000.00', 0, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 0, '2026-05-15 05:55:39'),
(8, 3, 4, 'Keripik Tempe', NULL, '8000.00', 0, 'https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380', 0, '2026-05-15 05:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Semua', 'semua', 0),
(2, 'Makanan', 'makanan', 1),
(3, 'Minuman', 'minuman', 2),
(4, 'Jajanan', 'jajanan', 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `venue_id` bigint UNSIGNED NOT NULL,
  `dining_table_id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(32) NOT NULL,
  `public_token` char(32) NOT NULL,
  `customer_name` varchar(120) DEFAULT NULL,
  `customer_email` varchar(180) DEFAULT NULL,
  `dining_type` enum('dine_in','take_away') NOT NULL DEFAULT 'dine_in',
  `payment_method` enum('qris','cashier','midtrans') NOT NULL,
  `status` enum('pending_payment','paid','accepted','processing','ready','completed','cancelled') NOT NULL DEFAULT 'pending_payment',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `service_tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_deadline_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gateway_order_id` varchar(64) DEFAULT NULL COMMENT 'legacy / tidak dipakai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `venue_id`, `dining_table_id`, `order_number`, `public_token`, `customer_name`, `customer_email`, `dining_type`, `payment_method`, `status`, `subtotal`, `service_tax`, `total`, `payment_deadline_at`, `created_at`, `updated_at`, `gateway_order_id`) VALUES
(1, 1, 1, 'DUMMY-221017-0', 'fff15c4742309653a2bc49e02b65f0a1', 'Eko Susilo', NULL, 'dine_in', 'cashier', 'paid', '102000.00', '10200.00', '112200.00', NULL, '2026-05-15 15:10:17', '2026-05-15 15:10:17', NULL),
(2, 1, 1, 'DUMMY-221059-0', 'da6ca357d75225cc5d81fed91531163c', 'Rina Pratama', NULL, 'dine_in', 'cashier', 'processing', '52000.00', '5200.00', '57200.00', NULL, '2026-05-15 15:10:59', '2026-05-15 15:10:59', NULL),
(3, 1, 1, 'DUMMY-221125-0', 'e71a58c8c2d910573648f6394cfcdf47', 'Siti Aminah', NULL, 'dine_in', 'cashier', 'pending_payment', '80000.00', '8000.00', '88000.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(4, 1, 1, 'DUMMY-221125-1', 'f64648b303442c64152cd3ae569cba91', 'Eko Susilo', NULL, 'dine_in', 'qris', 'ready', '32000.00', '3200.00', '35200.00', NULL, '2026-05-15 15:11:25', '2026-05-17 16:42:35', NULL),
(5, 1, 1, 'DUMMY-221125-2', '36abcaff79568c5f669fd9bfa74cccf1', 'Andi Wijaya', NULL, 'dine_in', 'cashier', 'ready', '15000.00', '1500.00', '16500.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(6, 1, 1, 'DUMMY-221125-3', 'a63166679fb7161f699cca70eb5f6ce5', 'Siti Aminah', NULL, 'dine_in', 'cashier', 'accepted', '68000.00', '6800.00', '74800.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(7, 1, 1, 'DUMMY-221125-4', 'e98d95c8a94fbb51154ee81cc0755ea6', 'Eko Susilo', NULL, 'dine_in', 'qris', 'cancelled', '58000.00', '5800.00', '63800.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(8, 1, 1, 'DUMMY-221125-5', 'e79bcbee9ca0ad5f3e8a73e379f131fc', 'Siti Aminah', NULL, 'dine_in', 'qris', 'processing', '50000.00', '5000.00', '55000.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(9, 1, 1, 'DUMMY-221125-6', '062265ce11d5c7ab5d4af3bc562442b8', 'Rina Pratama', NULL, 'dine_in', 'qris', 'completed', '30000.00', '3000.00', '33000.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(10, 1, 1, 'DUMMY-221125-7', '6d482d69e13ef973a661373842f0f841', 'Budi Santoso', NULL, 'dine_in', 'qris', 'completed', '10000.00', '1000.00', '11000.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(11, 1, 1, 'DUMMY-221125-8', '9e165d11bb815978ca5fc52c83616dd0', 'Budi Santoso', NULL, 'dine_in', 'cashier', 'processing', '50000.00', '5000.00', '55000.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:11:25', NULL),
(12, 1, 1, 'DUMMY-221125-9', '1b9b1b44e3b2e54554274ce347da48d8', 'Rina Pratama', NULL, 'dine_in', 'cashier', 'paid', '21000.00', '2100.00', '23100.00', NULL, '2026-05-15 15:11:25', '2026-05-15 15:31:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_daily_sequences`
--

CREATE TABLE `order_daily_sequences` (
  `venue_id` bigint UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `last_sequence` int UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED NOT NULL,
  `warung_id` bigint UNSIGNED NOT NULL,
  `menu_name_snapshot` varchar(160) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `note` varchar(255) DEFAULT NULL,
  `line_subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `warung_id`, `menu_name_snapshot`, `unit_price`, `quantity`, `note`, `line_subtotal`) VALUES
(1, 1, 5, 2, 'Bubur Ayam', '18000.00', 2, NULL, '36000.00'),
(2, 1, 1, 1, 'Wader Goreng', '25000.00', 2, NULL, '50000.00'),
(3, 1, 8, 3, 'Keripik Tempe', '8000.00', 2, NULL, '16000.00'),
(4, 2, 8, 3, 'Keripik Tempe', '8000.00', 1, NULL, '8000.00'),
(5, 2, 6, 2, 'Nasi Kuning Telur', '22000.00', 2, NULL, '44000.00'),
(6, 3, 4, 2, 'Rawon Jumbo', '25000.00', 1, NULL, '25000.00'),
(7, 3, 4, 2, 'Rawon Jumbo', '25000.00', 2, NULL, '50000.00'),
(8, 3, 7, 3, 'Es Teh Manis', '5000.00', 1, NULL, '5000.00'),
(9, 4, 3, 1, 'Mie Instan', '12000.00', 2, NULL, '24000.00'),
(10, 4, 8, 3, 'Keripik Tempe', '8000.00', 1, NULL, '8000.00'),
(11, 5, 7, 3, 'Es Teh Manis', '5000.00', 2, NULL, '10000.00'),
(12, 5, 7, 3, 'Es Teh Manis', '5000.00', 1, NULL, '5000.00'),
(13, 6, 5, 2, 'Bubur Ayam', '18000.00', 1, NULL, '18000.00'),
(14, 6, 2, 1, 'Soto Babat', '25000.00', 2, NULL, '50000.00'),
(15, 7, 2, 1, 'Soto Babat', '25000.00', 2, NULL, '50000.00'),
(16, 7, 8, 3, 'Keripik Tempe', '8000.00', 1, NULL, '8000.00'),
(17, 8, 4, 2, 'Rawon Jumbo', '25000.00', 2, NULL, '50000.00'),
(18, 9, 8, 3, 'Keripik Tempe', '8000.00', 1, NULL, '8000.00'),
(19, 9, 6, 2, 'Nasi Kuning Telur', '22000.00', 1, NULL, '22000.00'),
(20, 10, 7, 3, 'Es Teh Manis', '5000.00', 2, NULL, '10000.00'),
(21, 11, 4, 2, 'Rawon Jumbo', '25000.00', 2, NULL, '50000.00'),
(22, 12, 8, 3, 'Keripik Tempe', '8000.00', 2, NULL, '16000.00'),
(23, 12, 7, 3, 'Es Teh Manis', '5000.00', 1, NULL, '5000.00');

-- --------------------------------------------------------

--
-- Table structure for table `order_warung_fulfillment`
--

CREATE TABLE `order_warung_fulfillment` (
  `order_id` bigint UNSIGNED NOT NULL,
  `warung_id` bigint UNSIGNED NOT NULL,
  `status` enum('new','preparing','ready') NOT NULL DEFAULT 'new',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_warung_fulfillment`
--

INSERT INTO `order_warung_fulfillment` (`order_id`, `warung_id`, `status`, `updated_at`) VALUES
(4, 1, 'ready', '2026-05-17 16:42:35'),
(5, 3, 'ready', '2026-05-15 15:11:25'),
(6, 1, 'ready', '2026-05-15 21:02:08'),
(6, 2, 'new', '2026-05-15 15:11:25'),
(8, 2, 'new', '2026-05-15 15:11:25'),
(9, 2, 'ready', '2026-05-15 15:11:25'),
(9, 3, 'ready', '2026-05-15 15:11:25'),
(10, 3, 'ready', '2026-05-15 15:11:25'),
(11, 2, 'new', '2026-05-15 15:11:25');

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateway_transactions`
--

CREATE TABLE `payment_gateway_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `provider` varchar(32) NOT NULL DEFAULT 'midtrans',
  `external_id` varchar(120) DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'pending',
  `snap_token` text,
  `gross_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(8) NOT NULL DEFAULT 'IDR',
  `raw_request` text,
  `raw_response` text,
  `raw_notification` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_users`
--

CREATE TABLE `staff_users` (
  `id` bigint UNSIGNED NOT NULL,
  `venue_id` bigint UNSIGNED NOT NULL,
  `email` varchar(180) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(120) NOT NULL,
  `role` enum('admin','kasir','warung') NOT NULL,
  `warung_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff_users`
--

INSERT INTO `staff_users` (`id`, `venue_id`, `email`, `password_hash`, `name`, `role`, `warung_id`, `is_active`, `created_at`) VALUES
(1, 1, 'admin@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Admin Demo', 'admin', NULL, 1, '2026-05-15 05:55:39'),
(2, 1, 'kasir@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Kasir Demo', 'kasir', NULL, 1, '2026-05-15 05:55:39'),
(3, 1, 'warung1@scanteen.local', '$2y$10$6iwpyzibN1cTeUIRHbmeEOmaws/OYCxlNIoguPv2RrG7yC2ylKbCe', 'Stan Warung 1', 'warung', 1, 1, '2026-05-15 05:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Kantin Demo', 'demo', '2026-05-15 05:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `warungs`
--

CREATE TABLE `warungs` (
  `id` bigint UNSIGNED NOT NULL,
  `venue_id` bigint UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `warungs`
--

INSERT INTO `warungs` (`id`, `venue_id`, `name`, `slug`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 1, 'Warung 1', 'warung-1', 1, 1, '2026-05-15 05:55:39'),
(2, 1, 'Warung 2', 'warung-2', 2, 1, '2026-05-15 05:55:39'),
(3, 1, 'Warung 3', 'warung-3', 3, 1, '2026-05-15 05:55:39'),
(4, 1, 'Warung Hebat', 'Wong Hebat', 4, 1, '2026-05-16 14:30:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dining_tables`
--
ALTER TABLE `dining_tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dining_tables_token` (`barcode_token`),
  ADD KEY `idx_dining_tables_venue` (`venue_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menus_warung` (`warung_id`),
  ADD KEY `idx_menus_category` (`category_id`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_menu_categories_slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_orders_order_number` (`order_number`),
  ADD UNIQUE KEY `uq_orders_public_token` (`public_token`),
  ADD KEY `idx_orders_table` (`dining_table_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `fk_orders_venue` (`venue_id`);

--
-- Indexes for table `order_daily_sequences`
--
ALTER TABLE `order_daily_sequences`
  ADD PRIMARY KEY (`venue_id`,`order_date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_menu` (`menu_id`),
  ADD KEY `fk_order_items_warung` (`warung_id`);

--
-- Indexes for table `order_warung_fulfillment`
--
ALTER TABLE `order_warung_fulfillment`
  ADD PRIMARY KEY (`order_id`,`warung_id`),
  ADD KEY `fk_owf_warung` (`warung_id`);

--
-- Indexes for table `payment_gateway_transactions`
--
ALTER TABLE `payment_gateway_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pgt_order` (`order_id`),
  ADD KEY `idx_pgt_provider_ext` (`provider`,`external_id`);

--
-- Indexes for table `staff_users`
--
ALTER TABLE `staff_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_staff_venue_email` (`venue_id`,`email`),
  ADD KEY `idx_staff_role` (`venue_id`,`role`),
  ADD KEY `fk_staff_warung` (`warung_id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_venues_slug` (`slug`);

--
-- Indexes for table `warungs`
--
ALTER TABLE `warungs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_warungs_venue_slug` (`venue_id`,`slug`),
  ADD KEY `idx_warungs_venue` (`venue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dining_tables`
--
ALTER TABLE `dining_tables`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `payment_gateway_transactions`
--
ALTER TABLE `payment_gateway_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_users`
--
ALTER TABLE `staff_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warungs`
--
ALTER TABLE `warungs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dining_tables`
--
ALTER TABLE `dining_tables`
  ADD CONSTRAINT `fk_dining_tables_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `fk_menus_category` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_menus_warung` FOREIGN KEY (`warung_id`) REFERENCES `warungs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_table` FOREIGN KEY (`dining_table_id`) REFERENCES `dining_tables` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_daily_sequences`
--
ALTER TABLE `order_daily_sequences`
  ADD CONSTRAINT `fk_order_daily_sequences_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_warung` FOREIGN KEY (`warung_id`) REFERENCES `warungs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_warung_fulfillment`
--
ALTER TABLE `order_warung_fulfillment`
  ADD CONSTRAINT `fk_owf_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_owf_warung` FOREIGN KEY (`warung_id`) REFERENCES `warungs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `payment_gateway_transactions`
--
ALTER TABLE `payment_gateway_transactions`
  ADD CONSTRAINT `fk_pgt_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff_users`
--
ALTER TABLE `staff_users`
  ADD CONSTRAINT `fk_staff_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_staff_warung` FOREIGN KEY (`warung_id`) REFERENCES `warungs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `warungs`
--
ALTER TABLE `warungs`
  ADD CONSTRAINT `fk_warungs_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
