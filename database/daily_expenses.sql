-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 14, 2026 at 10:40 AM
-- Server version: 8.0.30
-- PHP Version: 8.5.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elite_daily`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_expenses`
--

CREATE TABLE `daily_expenses` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `category` enum('Food','Transport','Shopping','Bills','Education','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Other',
  `expense_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_expenses`
--

INSERT INTO `daily_expenses` (`id`, `title`, `amount`, `category`, `expense_date`, `notes`, `created_at`) VALUES
(1, 'Lunch', 850.00, 'Food', '2026-11-14', 'Lunch at restaurant', '2026-11-14 10:35:21'),
(2, 'Bus Fare', 150.00, 'Transport', '2026-11-14', 'Daily transport', '2026-11-14 10:35:21'),
(3, 'Notebook', 450.00, 'Education', '2026-11-14', 'Study materials', '2026-11-14 10:35:21'),
(4, 'Shopping', 2500.00, 'Shopping', '2026-11-14', 'Personal shopping', '2026-11-14 10:35:21'),
(5, 'Electricity Bill', 3200.00, 'Bills', '2026-11-14', 'Monthly electricity', '2026-11-14 10:35:21'),
(7, 'wifi bill', 4000.00, 'Transport', '2026-11-14', 'edew', '2026-11-14 10:39:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_expenses`
--
ALTER TABLE `daily_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_expense_date` (`expense_date`),
  ADD KEY `idx_expense_category` (`category`),
  ADD KEY `idx_expense_title` (`title`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_expenses`
--
ALTER TABLE `daily_expenses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
