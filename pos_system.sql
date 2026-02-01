-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2026 at 07:56 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(2, '2'),
(3, '3'),
(4, 'eg_category'),
(6, 'new'),
(7, 'marlar');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_log`
--

CREATE TABLE `inventory_log` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `action` enum('add','reduce') NOT NULL,
  `qty_changed` int(11) DEFAULT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_log`
--

INSERT INTO `inventory_log` (`id`, `product_id`, `action`, `qty_changed`, `log_time`, `note`) VALUES
(1, 1, 'reduce', 2, '2025-08-06 14:15:11', 'lost'),
(2, 2, 'add', 1, '2025-08-06 15:32:40', 'lost'),
(3, 2, 'add', 1, '2025-08-06 15:33:17', ''),
(4, 2, 'add', 2, '2025-08-06 15:33:28', ''),
(5, 2, 'reduce', 4, '2025-08-06 15:33:45', ''),
(6, 2, 'reduce', 25, '2025-08-07 03:50:34', 'lost'),
(7, 2, 'add', 5, '2025-08-07 03:51:13', ''),
(8, 2, 'reduce', 10, '2025-08-07 03:51:46', ''),
(9, 2, 'reduce', 1, '2025-08-20 14:22:37', ''),
(10, 2, 'reduce', 33, '2025-08-20 14:23:53', 'ff'),
(11, 1, 'reduce', 666, '2025-08-20 14:44:23', 'lost'),
(12, 2, 'add', 44, '2025-08-20 14:57:02', 'in stock'),
(13, 3, 'reduce', 1, '2025-08-21 16:17:01', 'lost');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `category_id`, `place`, `qty`, `selling_price`, `original_price`, `created_at`) VALUES
(1, 'eg_product2', 4, 'A4', 214, 2000.00, 1000.00, '2025-08-06 14:08:26'),
(2, 'food', 2, 'A1', 37, 2000.00, 1000.00, '2025-08-06 15:16:07'),
(3, 'Network', 7, 'A4', 5, 2000.00, 1500.00, '2025-08-20 15:34:10');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty_sold` int(11) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `sale_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `qty_sold`, `selling_price`, `original_price`, `sale_date`) VALUES
(1, 2, 2, 2000.00, 1000.00, '2025-08-20'),
(2, 2, 1, 2000.00, 1000.00, '2025-08-20'),
(3, 1, 2, 2000.00, 1000.00, '2025-08-20'),
(4, 1, 2, 2000.00, 1000.00, '2025-08-20'),
(5, 1, 2, 2000.00, 1000.00, '2025-08-20'),
(6, 2, 2, 2000.00, 1000.00, '2025-08-20'),
(7, 2, 2, 2000.00, 1000.00, '2025-08-20'),
(8, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(9, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(10, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(11, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(12, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(13, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(14, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(15, 3, 2, 2000.00, 1500.00, '2025-08-20'),
(16, 1, 2, 2000.00, 1000.00, '2025-08-21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_log`
--
ALTER TABLE `inventory_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD CONSTRAINT `inventory_log_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
