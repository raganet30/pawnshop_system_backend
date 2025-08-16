-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2025 at 08:16 PM
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
-- Database: `pawnshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `before_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_json`)),
  `after_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_json`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  `branch_phone` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `interest_rate` decimal(5,4) DEFAULT 0.0600,
  `cash_on_hand` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `branch_address`, `branch_phone`, `status`, `interest_rate`, `cash_on_hand`, `created_at`) VALUES
(1, 'Main Branch', 'Navarro St. , Calbayog City, Samar', '09171234567', 'active', 0.0600, 400.00, '2025-08-14 12:57:02'),
(2, 'Sample 2nd Branch', 'Sample', '09181234567', 'active', 0.0600, 0.00, '2025-08-14 12:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `cash_ledger`
--

CREATE TABLE `cash_ledger` (
  `ledger_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `txn_type` enum('pawn_disbursement','claim_receipt','tubo_interest','cash_adjustment','remata_sale','refund','other') NOT NULL,
  `direction` enum('in','out') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `ref_table` varchar(50) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_ledger`
--

INSERT INTO `cash_ledger` (`ledger_id`, `branch_id`, `txn_type`, `direction`, `amount`, `ref_table`, `ref_id`, `notes`, `user_id`, `created_at`) VALUES
(12, 1, '', 'in', 212.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 14:22:01'),
(13, 1, '', 'in', 530.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 14:37:32'),
(14, 1, '', 'in', 530.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 14:38:24'),
(15, 1, '', 'in', 530.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 14:44:23'),
(16, 1, '', 'in', 530.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 14:47:25'),
(17, 1, '', 'in', 530.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 14:56:43'),
(18, 1, '', 'in', 530.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 15:02:05'),
(19, 1, '', 'in', 0.00, 'claims', 0, 'Claim of pawned item', 8, '2025-08-16 15:11:19'),
(20, 1, '', 'in', 100.06, 'claims', 17, 'Pawn claimed', 8, '2025-08-16 15:19:07'),
(21, 1, '', 'in', 200.12, 'claims', 18, 'Pawn claimed', 8, '2025-08-16 15:30:28'),
(22, 1, '', 'in', 500.30, 'claims', 19, 'Pawn claimed', 8, '2025-08-16 15:32:50'),
(23, 1, '', 'in', 530.00, 'claims', 20, 'Pawn claimed', 8, '2025-08-16 15:41:01'),
(24, 1, '', 'in', 530.00, 'claims', 21, 'Pawn claimed', 8, '2025-08-16 16:04:03'),
(25, 1, '', 'in', 1060.00, 'claims', 22, 'Pawn claimed', 8, '2025-08-16 16:06:02'),
(26, 1, '', 'in', 1060.00, 'claims', 23, 'Pawn claimed', 8, '2025-08-16 16:07:54'),
(27, 1, '', 'in', 848.00, 'claims', 24, 'Pawn claimed', 8, '2025-08-16 16:14:08'),
(28, 1, '', 'in', 106.00, 'claims', 25, 'Pawn claimed', 8, '2025-08-16 16:16:07'),
(29, 1, '', 'in', 848.00, 'claims', 26, 'Pawn claimed', 8, '2025-08-16 16:19:00'),
(30, 1, '', 'in', 212.00, 'claims', 27, 'Pawn claimed', 8, '2025-08-16 16:23:54'),
(31, 1, '', 'in', 224.72, 'claims', 28, 'Pawn claimed', 8, '2025-08-16 16:26:21'),
(32, 1, '', 'in', 530.00, 'claims', 29, 'Pawn claimed', 8, '2025-08-16 17:17:19'),
(33, 1, '', 'in', 530.00, 'claims', 30, 'Pawn claimed', 8, '2025-08-16 17:20:58'),
(34, 1, '', '', 100.00, 'pawned_items', 50, 'Pawn edit adjustment', NULL, '2025-08-16 17:42:42'),
(35, 1, '', '', -100.00, 'pawned_items', 50, 'Pawn edit adjustment', NULL, '2025-08-16 17:44:36'),
(36, 1, '', '', 100.00, 'pawned_items', 50, 'Pawn edit adjustment', 8, '2025-08-16 17:46:52'),
(37, 1, '', '', 100.00, 'pawned_items', 50, 'Pawn edit adjustment', 8, '2025-08-16 17:46:52'),
(38, 1, '', '', -100.00, 'pawned_items', 50, 'Pawn edit adjustment', 8, '2025-08-16 17:47:08'),
(39, 1, '', '', -100.00, 'pawned_items', 50, 'Pawn edit adjustment', 8, '2025-08-16 17:47:08'),
(40, 1, '', 'out', 100.00, 'pawned_items', 22, 'Edit pawn amount from 500 to 600', NULL, '2025-08-16 18:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `claim_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_claimed` date NOT NULL,
  `months` int(11) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `interest_amount` decimal(10,2) NOT NULL,
  `principal_amount` decimal(10,2) NOT NULL,
  `penalty_amount` decimal(10,2) DEFAULT 0.00,
  `total_paid` decimal(10,2) NOT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`claim_id`, `pawn_id`, `branch_id`, `date_claimed`, `months`, `interest_rate`, `interest_amount`, `principal_amount`, `penalty_amount`, `total_paid`, `cashier_id`, `notes`, `photo_path`, `created_at`) VALUES
(9, 19, 1, '2025-08-16', 1, 6.00, 12.00, 200.00, 0.00, 212.00, 8, NULL, NULL, '2025-08-16 14:22:01'),
(10, 20, 1, '2025-08-16', 1, 6.00, 30.00, 500.00, 0.00, 530.00, 8, NULL, 'uploads/claimants/claimant_68a097ac8753b.png', '2025-08-16 14:37:32'),
(11, 21, 1, '2025-08-16', 1, 6.00, 30.00, 500.00, 0.00, 530.00, 8, NULL, 'uploads/claimants/claimant_68a097e0e114b.png', '2025-08-16 14:38:24'),
(12, 22, 1, '2025-08-16', 1, 6.00, 30.00, 500.00, 0.00, 530.00, 8, NULL, NULL, '2025-08-16 14:44:23'),
(13, 23, 1, '2025-08-16', 1, 6.00, 30.00, 500.00, 0.00, 530.00, 8, NULL, 'uploads/claimants/claimant_68a099fd18d41.png', '2025-08-16 14:47:25'),
(14, 24, 1, '2025-08-16', 1, 6.00, 30.00, 500.00, 0.00, 530.00, 8, NULL, NULL, '2025-08-16 14:56:43'),
(15, 25, 1, '2025-08-16', 1, 6.00, 30.00, 500.00, 0.00, 530.00, 8, NULL, NULL, '2025-08-16 15:02:05'),
(16, 26, 1, '2025-08-16', 1, 0.00, 0.00, 0.00, 0.00, 0.00, 8, NULL, NULL, '2025-08-16 15:11:19'),
(17, 27, 1, '2025-08-16', 1, 0.06, 0.06, 100.00, 0.00, 100.06, 8, '', 'uploads/claimants/claimant_27_1755357547.png', '2025-08-16 15:19:07'),
(18, 28, 1, '2025-08-16', 1, 0.06, 0.12, 200.00, 0.00, 200.12, 8, '', 'uploads/claimants/claimant_28_1755358228.png', '2025-08-16 15:30:28'),
(19, 29, 1, '2025-08-16', 1, 0.06, 0.30, 500.00, 0.00, 500.30, 8, '', 'uploads/claimants/claimant_29_1755358370.png', '2025-08-16 15:32:50'),
(20, 30, 1, '2025-08-16', 1, 0.06, 30.00, 500.00, 0.00, 530.00, 8, '', 'uploads/claimants/claimant_30_1755358861.png', '2025-08-16 15:41:01'),
(21, 31, 1, '2025-08-17', 1, 0.06, 30.00, 500.00, 0.00, 530.00, 8, '', 'uploads/claimants/claimant_31_1755360243.png', '2025-08-16 16:04:03'),
(22, 32, 1, '2025-08-17', 1, 0.06, 60.00, 1000.00, 0.00, 1060.00, 8, '', 'uploads/claimants/claimant_32_1755360362.png', '2025-08-16 16:06:02'),
(23, 33, 1, '2025-08-17', 1, 0.06, 60.00, 1000.00, 0.00, 1060.00, 8, '', 'uploads/claimants/claimant_33_1755360474.png', '2025-08-16 16:07:54'),
(24, 34, 1, '2025-08-17', 1, 0.06, 48.00, 800.00, 0.00, 848.00, 8, '', 'uploads/claimants/claimant_34_1755360848.png', '2025-08-16 16:14:08'),
(25, 36, 1, '2025-08-17', 1, 0.06, 6.00, 100.00, 0.00, 106.00, 8, '', 'uploads/claimants/claimant_36_1755360967.png', '2025-08-16 16:16:07'),
(26, 35, 1, '2025-08-17', 1, 0.06, 48.00, 800.00, 0.00, 848.00, 8, '', 'uploads/claimants/claimant_35_1755361140.png', '2025-08-16 16:19:00'),
(27, 19, 1, '2025-08-17', 1, 0.06, 12.00, 200.00, 0.00, 212.00, 8, '', 'uploads/claimants/claimant_19_1755361434.png', '2025-08-16 16:23:54'),
(28, 37, 1, '2025-08-17', 1, 0.06, 12.72, 212.00, 0.00, 224.72, 8, '', 'uploads/claimants/claimant_37_1755361581.png', '2025-08-16 16:26:21'),
(29, 20, 1, '2025-08-17', 1, 0.06, 30.00, 500.00, 0.00, 530.00, 8, '', 'uploads/claimants/claimant_20_1755364639.png', '2025-08-16 17:17:19'),
(30, 21, 1, '2025-08-17', 1, 0.06, 30.00, 500.00, 0.00, 530.00, 8, '', 'uploads/claimants/claimant_21_1755364858.png', '2025-08-16 17:20:58');

-- --------------------------------------------------------

--
-- Table structure for table `forfeitures`
--

CREATE TABLE `forfeitures` (
  `forfeiture_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_forfeited` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interest_rates`
--

CREATE TABLE `interest_rates` (
  `rate_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `rate_percent` decimal(5,2) NOT NULL,
  `effective_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interest_rates`
--

INSERT INTO `interest_rates` (`rate_id`, `branch_id`, `rate_percent`, `effective_date`, `is_active`, `created_at`) VALUES
(1, 1, 6.00, '2025-01-01', 1, '2025-08-14 12:57:02'),
(2, 2, 6.00, '2025-01-01', 1, '2025-08-14 12:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `pawned_items`
--

CREATE TABLE `pawned_items` (
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `unit_description` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `amount_pawned` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT 6.00,
  `interest_amount` decimal(10,2) DEFAULT NULL,
  `date_pawned` date NOT NULL,
  `date_claimed` date DEFAULT NULL,
  `claimant_photo` varchar(255) DEFAULT NULL,
  `date_forfeited` date DEFAULT NULL,
  `status` enum('pawned','claimed','forfeited') NOT NULL DEFAULT 'pawned',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pawned_items`
--

INSERT INTO `pawned_items` (`pawn_id`, `branch_id`, `owner_name`, `contact_no`, `address`, `unit_description`, `category`, `amount_pawned`, `interest_rate`, `interest_amount`, `date_pawned`, `date_claimed`, `claimant_photo`, `date_forfeited`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(19, 1, 'eee', '1', NULL, 'ee', 'Gadgets', 200.00, 6.00, 12.00, '2025-08-16', '2025-08-17', NULL, NULL, 'claimed', 'ee', NULL, NULL, '2025-08-16 14:18:57', '2025-08-16 16:23:54'),
(20, 1, 'Test Owner', '09123456789', NULL, 'iPhone 12 128GB', 'Gadgets', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-17', NULL, NULL, 'claimed', 'Brgy. Ipao', NULL, NULL, '2025-08-16 14:24:34', '2025-08-16 17:17:19'),
(21, 1, 'Test Owner', '09123456789', NULL, 'Samsung Ultra', 'Gadgets', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-17', NULL, NULL, 'claimed', 'Brgy. Ipao', NULL, NULL, '2025-08-16 14:25:27', '2025-08-16 17:20:58'),
(22, 1, 'Ken Ken', '09123456789', NULL, 'Samsung ', 'Gadgets', 600.00, 6.00, 30.00, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 14:42:01', '2025-08-16 18:03:13'),
(23, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Camera', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 14:47:07', '2025-08-16 16:22:49'),
(24, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Vehicle', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 14:56:32', '2025-08-16 16:22:49'),
(25, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 14:57:32', '2025-08-16 16:22:49'),
(26, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 500.00, 6.00, 0.00, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 15:04:49', '2025-08-16 16:22:49'),
(27, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 100.00, 6.00, 0.06, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 15:05:02', '2025-08-16 16:22:49'),
(28, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 200.00, 6.00, 0.12, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 15:05:12', '2025-08-16 16:22:49'),
(29, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 500.00, 6.00, 0.30, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 15:32:15', '2025-08-16 16:22:49'),
(30, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-16', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 15:40:26', '2025-08-16 16:22:49'),
(31, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 500.00, 6.00, 30.00, '2025-08-16', '2025-08-17', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 15:46:08', '2025-08-16 16:22:49'),
(32, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 1000.00, 6.00, 60.00, '2025-08-16', '2025-08-17', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:04:45', '2025-08-16 16:22:49'),
(33, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 1000.00, 6.00, 60.00, '2025-08-16', '2025-08-17', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:06:57', '2025-08-16 16:22:49'),
(34, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 800.00, 6.00, 48.00, '2025-08-16', '2025-08-17', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:11:57', '2025-08-16 16:22:49'),
(35, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 800.00, 6.00, 48.00, '2025-08-16', '2025-08-17', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:15:00', '2025-08-16 16:21:43'),
(36, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 100.00, 6.00, 6.00, '2025-08-16', '2025-08-17', NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:15:12', '2025-08-16 16:20:41'),
(37, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 212.00, 6.00, 12.72, '2025-08-16', '2025-08-17', NULL, NULL, 'claimed', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:24:56', '2025-08-16 16:26:21'),
(38, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 500.00, 6.00, NULL, '2025-08-16', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:34:01', NULL),
(39, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 500.00, 6.00, NULL, '2025-08-16', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:35:16', NULL),
(40, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 500.00, 6.00, NULL, '2025-08-16', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:43:30', NULL),
(41, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 500.00, 6.00, NULL, '2025-08-16', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:44:02', NULL),
(42, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 100.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:47:41', NULL),
(43, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 100.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:48:07', NULL),
(44, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 100.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:51:14', NULL),
(45, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Computer', 100.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:52:01', NULL),
(46, 1, 'Test Owner', '09123456789', NULL, 'Samsung ', 'Gadgets', 100.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'Brgy. Ipao', NULL, NULL, '2025-08-16 16:53:36', NULL),
(47, 1, 'Kenneth Son', '09123456789', NULL, 'Mio', 'Gadgets', 7000.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'test note', NULL, NULL, '2025-08-16 16:55:32', '2025-08-16 17:55:21'),
(48, 1, 'Test Owner', '09123456789', NULL, 'Mio', 'Gadgets', 100.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'test note', NULL, NULL, '2025-08-16 16:56:18', NULL),
(49, 1, 'Test Owner', '09123456789', NULL, 'Mio', 'Gadgets', 684.72, 6.00, NULL, '2025-08-16', NULL, NULL, NULL, 'pawned', 'test note', NULL, NULL, '2025-08-16 17:31:38', NULL),
(50, 1, 'QQQQ', '09123456789', NULL, 'Mio', 'Gadgets', 500.00, 6.00, NULL, '2025-08-17', NULL, NULL, NULL, 'pawned', 'test note', NULL, NULL, '2025-08-16 17:32:06', '2025-08-16 17:47:08');

-- --------------------------------------------------------

--
-- Table structure for table `remata_sales`
--

CREATE TABLE `remata_sales` (
  `sale_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_sold` date NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `buyer_name` varchar(100) DEFAULT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remata_sales`
--

INSERT INTO `remata_sales` (`sale_id`, `pawn_id`, `branch_id`, `date_sold`, `selling_price`, `buyer_name`, `cashier_id`, `notes`, `created_at`) VALUES
(1, 3, 2, '2025-08-01', 15000.00, 'Walk-in Buyer', NULL, 'Sold at counter', '2025-08-14 12:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `tubo_payments`
--

CREATE TABLE `tubo_payments` (
  `tubo_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_paid` date NOT NULL,
  `months_covered` int(11) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `interest_amount` decimal(10,2) NOT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','cashier') NOT NULL DEFAULT 'cashier',
  `full_name` varchar(100) DEFAULT NULL,
  `status` enum('active','disabled') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `branch_id`, `username`, `password_hash`, `role`, `full_name`, `status`, `last_login`, `created_at`) VALUES
(8, 1, 'admin', '$2y$10$npjSjuSvSvk9XzmFNK0F2e6cWLst/qJvJSj1nejEw23ARNmzmnjJa', 'super_admin', 'Admin User', 'active', NULL, '2025-08-14 13:36:07'),
(9, 1, 'cashier', '$2y$10$KaSPllnDlu3.wz39UXATQuyLwkxRkZCfnjvNcvCfuxyBP8d/ktafq', 'cashier', 'Cashier User', 'active', NULL, '2025-08-14 13:36:07'),
(10, 1, 'manager', '$2y$10$m63O6yvN/Y9NpdffaR43YONv4D/np5fTbkR7Ad8K7yL4qxNOx1fDu', '', 'Manager User', 'active', NULL, '2025-08-14 13:36:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  ADD PRIMARY KEY (`ledger_id`),
  ADD KEY `fk_ledger_user` (`user_id`),
  ADD KEY `idx_ledger_branch_date` (`branch_id`,`created_at`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `fk_claim_pawn` (`pawn_id`),
  ADD KEY `fk_claim_cashier` (`cashier_id`),
  ADD KEY `idx_claim_branch_date` (`branch_id`,`date_claimed`);

--
-- Indexes for table `forfeitures`
--
ALTER TABLE `forfeitures`
  ADD PRIMARY KEY (`forfeiture_id`),
  ADD KEY `fk_forf_pawn` (`pawn_id`),
  ADD KEY `idx_forf_branch_date` (`branch_id`,`date_forfeited`);

--
-- Indexes for table `interest_rates`
--
ALTER TABLE `interest_rates`
  ADD PRIMARY KEY (`rate_id`),
  ADD KEY `idx_rates_branch_date` (`branch_id`,`effective_date`);

--
-- Indexes for table `pawned_items`
--
ALTER TABLE `pawned_items`
  ADD PRIMARY KEY (`pawn_id`),
  ADD KEY `fk_pawn_branch` (`branch_id`),
  ADD KEY `fk_pawn_created_by` (`created_by`),
  ADD KEY `fk_pawn_updated_by` (`updated_by`),
  ADD KEY `idx_pawn_status_branch` (`status`,`branch_id`),
  ADD KEY `idx_pawn_owner` (`owner_name`),
  ADD KEY `idx_pawn_dates` (`date_pawned`,`date_claimed`,`date_forfeited`);

--
-- Indexes for table `remata_sales`
--
ALTER TABLE `remata_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `fk_sale_pawn` (`pawn_id`),
  ADD KEY `fk_sale_cashier` (`cashier_id`),
  ADD KEY `idx_sale_branch_date` (`branch_id`,`date_sold`);

--
-- Indexes for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  ADD PRIMARY KEY (`tubo_id`),
  ADD KEY `fk_tubo_pawn` (`pawn_id`),
  ADD KEY `fk_tubo_cashier` (`cashier_id`),
  ADD KEY `idx_tubo_branch_date` (`branch_id`,`date_paid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_branch` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `forfeitures`
--
ALTER TABLE `forfeitures`
  MODIFY `forfeiture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interest_rates`
--
ALTER TABLE `interest_rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pawned_items`
--
ALTER TABLE `pawned_items`
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `remata_sales`
--
ALTER TABLE `remata_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  MODIFY `tubo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  ADD CONSTRAINT `fk_ledger_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ledger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `fk_claim_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_claim_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_claim_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE;

--
-- Constraints for table `forfeitures`
--
ALTER TABLE `forfeitures`
  ADD CONSTRAINT `fk_forf_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_forf_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE;

--
-- Constraints for table `interest_rates`
--
ALTER TABLE `interest_rates`
  ADD CONSTRAINT `fk_rates_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pawned_items`
--
ALTER TABLE `pawned_items`
  ADD CONSTRAINT `fk_pawn_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pawn_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pawn_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `remata_sales`
--
ALTER TABLE `remata_sales`
  ADD CONSTRAINT `fk_sale_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sale_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sale_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  ADD CONSTRAINT `fk_tubo_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tubo_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tubo_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
