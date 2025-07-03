-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 12:29 PM
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
-- Database: `supermarket_billing`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `address`, `phone`, `gstin`, `created_at`) VALUES
(1, 'karthivk', 'NO1, Admin, Quick Booking, Admin', '9876543211', '685315', '2025-06-22 19:00:39'),
(2, 'karthivk', 'NO1, Admin, Quick Booking, Admin', '9876543211', '685315', '2025-06-22 19:00:50'),
(3, 'karthivk', 'No.6., vivekanander street, Manapparai, Trichy', '9876543211', '685315', '2025-06-22 19:01:19'),
(4, 'karthivk', 'n0.2 xxx street.,yy state. xendia', '9876543211', '685315', '2025-06-22 19:03:09'),
(5, 'karthivk', 'n0.2 xxx street.,yy state. xendia', '9876543211', '685315', '2025-06-22 19:04:07'),
(6, 'Partha', 'lsukhgseiurhgwlernlwetg', '8434379860687', '8557651', '2025-06-22 19:05:22'),
(7, 'Partha', 'lsukhgseiurhgwlernlwetg', '8434379860687', '8557651', '2025-06-22 19:05:38'),
(8, 'Parthar', 'NO1, Admin, Quick Booking, Admin', '8434379860687', '8557651', '2025-06-22 19:13:10'),
(9, 'Parthar', 'NO1, Admin, adv Booking, Admin', '8434379860687', '8557651', '2025-06-22 19:13:59'),
(10, 'Parthar', 'NO1, Admin, adv Booking, Admin', '8434379860687', '8557651', '2025-06-22 19:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `invoice_date` date NOT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `customer_name`, `invoice_date`, `pdf_path`, `created_at`) VALUES
(1, '8777565', 'Wally', '2025-06-24', 'invoices/Invoice_8777565.pdf', '2025-06-24 07:29:31'),
(3, '5225645', 'Pruno Dass', '2025-06-24', 'invoices/Invoice_5225645.pdf', '2025-06-24 08:54:08'),
(4, '89987', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89987.pdf', '2025-06-24 08:57:02'),
(6, '89980', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89980.pdf', '2025-06-24 09:13:20'),
(7, '89981', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89981.pdf', '2025-06-24 09:14:24'),
(8, '89982', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89982.pdf', '2025-06-24 09:15:02'),
(9, '89984', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89984.pdf', '2025-06-24 09:15:58'),
(10, '89985', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89985.pdf', '2025-06-24 09:16:21'),
(11, '8989', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_8989.pdf', '2025-06-24 09:19:56'),
(12, '89890', 'Noor Salim Ahmed', '2025-06-24', 'invoices/Invoice_89890.pdf', '2025-06-24 09:23:32'),
(16, '899855', 'Sahul Hameed', '2025-06-24', 'invoices/Invoice_899855.pdf', '2025-06-24 09:45:06'),
(17, '77778', 'Bullet', '2025-06-24', 'invoices/Invoice_77778.pdf', '2025-06-24 14:03:26'),
(18, '4456', 'Kiran Raj', '2025-06-24', 'invoices/Invoice_4456.pdf', '2025-06-24 14:13:11'),
(20, '883520', 'p', '2025-06-24', 'invoices/Invoice_883520.pdf', '2025-06-24 15:34:23'),
(21, '899800', 'Pruno Dass', '2025-06-24', 'invoices/Invoice_899800.pdf', '2025-06-24 16:24:19'),
(23, '9910', 'iio', '2025-06-24', 'invoices/Invoice_9910.pdf', '2025-06-24 17:08:04'),
(26, '899801', 'First Name Last Name', '2025-07-03', 'invoices/Invoice_899801.pdf', '2025-07-03 09:03:45');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `hsn_code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `price`, `hsn_code`, `created_at`) VALUES
(1, 'Sample Material', 100.00, 'A7', '2025-06-19 21:08:38'),
(2, 'Sample Material', 150.00, 'B6', '2025-06-19 21:08:38'),
(3, 'Sample Material 3', 200.00, 'C3', '2025-06-19 21:08:38'),
(6, 'Material- 88', 4000.00, 'P8', '2025-06-20 10:34:45'),
(7, 'Material -NN', 580.00, 'Z3', '2025-06-20 18:51:13'),
(8, 'Sample material -YY', 8400.00, 'L9', '2025-06-24 09:31:33'),
(9, 'Kiran\'s Material -1', 5100.00, 'K7', '2025-06-24 14:17:00'),
(10, 'Kiran', 6000.00, 'K5', '2025-06-24 14:17:57'),
(11, 'PipeBomber-66', 9110.00, 'J8', '2025-06-24 16:25:59');

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE `transports` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transports`
--

INSERT INTO `transports` (`id`, `name`, `created_at`) VALUES
(1, 'F-22', '2025-06-19 20:06:38'),
(5, 'Globemaster', '2025-06-19 21:08:38'),
(6, 'Submarine', '2025-06-19 21:08:38'),
(7, 'Missile', '2025-06-19 22:39:53'),
(8, 'PSLV', '2025-06-20 10:36:31'),
(9, 'GSLV', '2025-06-20 18:51:39'),
(10, 'J-7', '2025-06-24 09:31:49'),
(12, 'Helicopter', '2025-06-24 16:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'ssenterpriseserp@gmail.com', '$2y$10$BVTwT73yjb37coYoI33rB.njueLneVXQrRWZ9P/8U0tyjzeLbV4eO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hsn_code` (`hsn_code`);

--
-- Indexes for table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
