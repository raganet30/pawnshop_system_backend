-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 10:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `branch_id`, `action_type`, `description`, `created_at`) VALUES
(9, 8, 1, 'Add Pawned Item', 'Admin User added a new pawn item: Test Owner  (Unit: BIKE, Category: Gadgets, Amount: ₱50.00) ', '2025-08-19 08:33:06'),
(10, 8, 1, 'Add Pawned Item', 'Admin User added a new pawn item: Test Owner  (Unit: BIKE, Category: Others, Amount: ₱50.00) ', '2025-08-19 08:34:45'),
(11, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Test Owner 2nd Branch 2 (Unit: XRM 125, Category: Vehicle, Amount: ₱700.00) ', '2025-08-20 03:30:35'),
(12, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Test Owner  (Unit: BIKE, Category: Vehicle, Amount: ₱50.00) ', '2025-08-20 03:30:56'),
(13, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Kenken (Unit: iphone 15, Category: Gadgets, Amount: ₱10,000.00) ', '2025-08-20 03:36:18'),
(14, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Test Owner  (Unit: BIKE, Category: Vehicle, Amount: ₱50.00) ', '2025-08-20 03:39:29'),
(15, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Kenken (Unit: iphone 16 pro max, Category: Gadgets, Amount: ₱20,000.00) ', '2025-08-20 03:40:19'),
(16, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Kenken (Unit: BIKE, Category: Vehicle, Amount: ₱2,000.00) ', '2025-08-20 03:47:02'),
(17, 9, 1, 'Add Pawned Item', 'Cashier Main Branch added a new pawn item: Kenken (Unit: BIKE, Category: Vehicle, Amount: ₱2,000.00) ', '2025-08-20 05:14:04'),
(18, 9, 1, 'Add Pawned Item', 'Cashier Main Branch added a new pawn item: Kenken (Unit: BIKE, Category: Vehicle, Amount: ₱2,000.00) ', '2025-08-20 05:14:31'),
(19, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Test Owner 2nd Branch 2 (Unit: XRM 125, Category: Vehicle, Amount: ₱700.00) ', '2025-08-20 05:20:17'),
(20, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item: Test Owner  (Unit: XRM 125, Category: Vehicle, Amount: ₱50.00) ', '2025-08-20 05:20:38'),
(21, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Kenken (Unit: Car, Category: Vehicle, Amount: ₱2,000.00) ', '2025-08-20 05:32:21'),
(22, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item: Test Owner 2nd Branch 2 (Unit: XRM 125, Category: Gadgets, Amount: ₱700.00) ', '2025-08-20 07:04:03');

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
(1, 'Main Branch - Calbayog', 'Navarro St. , Calbayog City, Samar', '09171234567', 'active', 0.0600, 103296.00, '2025-08-14 12:57:02'),
(2, 'Gandara Branch', 'Gandara Samar', '09181234567', 'active', 0.0600, 49950.00, '2025-08-14 12:57:02');

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
(90, 1, '', 'in', 800.00, 'pawned_items', 73, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 05:59:42'),
(91, 1, '', 'in', 800.00, 'pawned_items', 73, 'Pawn restored from trash', 8, '2025-08-19 06:00:12'),
(92, 1, '', 'in', 800.00, 'pawned_items', 73, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 06:00:35'),
(93, 1, '', 'out', 300.00, 'pawned_items', 75, 'Edit pawn amount from 500 to 800', NULL, '2025-08-19 06:01:30'),
(94, 1, '', 'in', 848.00, 'claims', 40, 'Pawn claimed', 8, '2025-08-19 06:03:00'),
(95, 1, '', 'in', 530.00, 'claims', 41, 'Pawn claimed', 8, '2025-08-19 06:06:07'),
(96, 1, '', 'in', 500.00, 'pawned_items', 78, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 06:37:18'),
(97, 1, '', 'in', 100.00, 'pawned_items', 79, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 06:37:22'),
(98, 1, '', 'in', 100.00, 'pawned_items', 80, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 06:37:25'),
(99, 1, '', 'in', 100.00, 'pawned_items', 81, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 06:37:28'),
(100, 1, '', 'in', 1.00, 'pawned_items', 82, 'Pawn deleted - moved to trash, amount refunded to COH', 8, '2025-08-19 06:37:33'),
(101, 1, '', 'in', 500.00, 'pawned_items', 83, 'Edit pawn amount from 1000 to 500', NULL, '2025-08-19 06:42:46'),
(102, 1, '', 'out', 500.00, 'pawned_items', 83, 'Edit pawn amount from 500 to 1000', NULL, '2025-08-19 07:12:17'),
(103, 1, '', 'out', 77.00, 'pawned_items', 83, 'Edit pawn amount from 1000 to 1077', NULL, '2025-08-19 07:12:42'),
(104, 1, '', 'in', 1141.62, 'claims', 42, 'Pawn claimed', 8, '2025-08-19 07:21:11'),
(105, 1, '', 'in', 50.00, 'pawned_items', 100, 'Pawn forfeited', 8, '2025-08-19 08:33:22'),
(106, 1, '', 'in', 50.00, 'pawned_items', 101, 'Pawn forfeited', 8, '2025-08-19 08:34:53'),
(107, 1, '', 'in', 53.00, 'claims', 43, 'Pawn claimed', 9, '2025-08-20 03:11:45'),
(108, 2, '', 'out', 450.00, 'pawned_items', 103, 'Edit pawn amount from 50 to 500', NULL, '2025-08-20 03:20:22'),
(109, 2, '', 'in', 530.00, 'claims', 44, 'Pawn claimed', 11, '2025-08-20 03:20:34'),
(110, 1, '', 'in', 742.00, 'claims', 45, 'Pawn claimed', 12, '2025-08-20 03:31:10'),
(111, 1, '', 'in', 11800.00, 'claims', 46, 'Pawn claimed', 12, '2025-08-20 03:37:11'),
(112, 1, '', 'in', 53.00, 'claims', 47, 'Pawn claimed', 12, '2025-08-20 03:38:37'),
(113, 1, '', 'in', 53.00, 'claims', 48, 'Pawn claimed', 12, '2025-08-20 03:39:36'),
(114, 1, '', 'in', 21200.00, 'claims', 49, 'Pawn claimed', 12, '2025-08-20 03:40:28'),
(115, 1, '', 'in', 1450.00, 'pawned_items', 109, 'Edit pawn amount from 2000 to 550', NULL, '2025-08-20 03:55:40'),
(116, 1, '', 'out', 1000.00, 'pawned_items', 109, 'Edit pawn amount from 550 to 1550', NULL, '2025-08-20 03:56:40'),
(117, 1, '', 'in', 1550.00, 'pawned_items', 109, 'Pawn deleted - moved to trash, amount refunded to COH', 12, '2025-08-20 05:13:31'),
(118, 1, '', 'in', 2120.00, 'claims', 50, 'Pawn claimed', 9, '2025-08-20 05:14:16'),
(119, 1, '', 'in', 2120.00, 'claims', 51, 'Pawn claimed', 12, '2025-08-20 05:31:47'),
(120, 1, '', 'out', 100.00, 'pawned_items', 112, 'Edit pawn amount from 700 to 800', NULL, '2025-08-20 05:31:56'),
(121, 1, '', 'in', 800.00, 'pawned_items', 112, 'Pawn forfeited', 12, '2025-08-20 05:32:05'),
(122, 1, '', 'in', 2000.00, 'pawned_items', 114, 'Pawn deleted - moved to trash, amount refunded to COH', 12, '2025-08-20 05:32:27'),
(123, 1, '', 'in', 700.00, 'pawned_items', 115, 'Pawn deleted - moved to trash, amount refunded to COH', 12, '2025-08-20 07:04:34');

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
(43, 102, 1, '2025-08-20', 1, 0.06, 3.00, 50.00, 0.00, 53.00, 9, '', 'uploads/claimants/claimant_102_1755659505.png', '2025-08-20 03:11:45'),
(44, 103, 2, '2025-08-20', 1, 0.06, 30.00, 500.00, 0.00, 530.00, 11, '', 'uploads/claimants/claimant_103_1755660034.png', '2025-08-20 03:20:34'),
(45, 104, 1, '2025-08-20', 1, 0.06, 42.00, 700.00, 0.00, 742.00, 12, '', 'uploads/claimants/claimant_104_1755660670.png', '2025-08-20 03:31:10'),
(46, 106, 1, '2025-08-20', 3, 0.06, 1800.00, 10000.00, 0.00, 11800.00, 12, '', 'uploads/claimants/claimant_106_1755661031.png', '2025-08-20 03:37:11'),
(47, 105, 1, '2025-08-20', 1, 0.06, 3.00, 50.00, 0.00, 53.00, 12, '', 'uploads/claimants/claimant_105_1755661117.png', '2025-08-20 03:38:37'),
(48, 107, 1, '2025-08-20', 1, 0.06, 3.00, 50.00, 0.00, 53.00, 12, '', 'uploads/claimants/claimant_107_1755661176.png', '2025-08-20 03:39:36'),
(49, 108, 1, '2025-08-20', 1, 0.06, 1200.00, 20000.00, 0.00, 21200.00, 12, '', 'uploads/claimants/claimant_108_1755661228.png', '2025-08-20 03:40:28'),
(50, 110, 1, '2025-08-20', 1, 0.06, 120.00, 2000.00, 0.00, 2120.00, 9, '', 'uploads/claimants/claimant_110_1755666856.png', '2025-08-20 05:14:16'),
(51, 111, 1, '2025-08-20', 1, 0.06, 120.00, 2000.00, 0.00, 2120.00, 12, '', 'uploads/claimants/claimant_111_1755667907.png', '2025-08-20 05:31:47');

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
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pawned_items`
--

INSERT INTO `pawned_items` (`pawn_id`, `branch_id`, `owner_name`, `contact_no`, `address`, `unit_description`, `category`, `amount_pawned`, `interest_rate`, `interest_amount`, `date_pawned`, `date_claimed`, `claimant_photo`, `date_forfeited`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `is_deleted`) VALUES
(100, 1, 'Test Owner ', '09123456789', NULL, 'BIKE', 'Gadgets', 50.00, 6.00, NULL, '2025-08-19', NULL, NULL, NULL, 'forfeited', 'TEST', 8, NULL, '2025-08-19 08:33:06', '2025-08-19 08:33:22', 0),
(101, 1, 'Test Owner ', '09123456789', NULL, 'BIKE', 'Others', 50.00, 6.00, NULL, '2025-08-19', NULL, NULL, '2025-08-19', 'forfeited', 'TEST', 8, NULL, '2025-08-19 08:34:45', '2025-08-19 08:34:53', 0),
(102, 1, 'Pawn Test Owner 1', '09123456789', NULL, 'BIKE', 'Gadgets', 50.00, 6.00, 3.00, '2025-08-19', '2025-08-20', NULL, NULL, 'claimed', 'TEST', 8, NULL, '2025-08-19 08:33:06', '2025-08-20 03:11:45', 0),
(103, 2, 'Pawn Test Owner 2', '09123456789', NULL, 'BIKE', 'Gadgets', 500.00, 6.00, 30.00, '2025-08-19', '2025-08-20', NULL, NULL, 'claimed', 'TEST', 8, NULL, '2025-08-19 08:33:06', '2025-08-20 03:20:34', 0),
(104, 1, 'Test Owner 2nd Branch 2', '09123456789', NULL, 'XRM 125', 'Vehicle', 700.00, 6.00, 42.00, '2025-08-20', '2025-08-20', NULL, NULL, 'claimed', 'test', 12, NULL, '2025-08-20 03:30:35', '2025-08-20 03:31:10', 0),
(105, 1, 'Test Owner ', '09123456789', NULL, 'BIKE', 'Vehicle', 50.00, 6.00, 3.00, '2025-08-20', '2025-08-20', NULL, NULL, 'claimed', 'TEST', 12, NULL, '2025-08-20 03:30:56', '2025-08-20 03:38:37', 0),
(106, 1, 'Kenken', '09123456789', NULL, 'iphone 15', 'Gadgets', 10000.00, 6.00, 1800.00, '2025-06-01', '2025-08-20', NULL, NULL, 'claimed', 'will be claimed on Aug. 30, Brgy. Trinidad', 12, NULL, '2025-08-20 03:36:18', '2025-08-20 03:37:11', 0),
(107, 1, 'Test Owner ', '09123456789', NULL, 'BIKE', 'Vehicle', 50.00, 6.00, 3.00, '2025-08-20', '2025-08-20', NULL, NULL, 'claimed', 'TEST', 12, NULL, '2025-08-20 03:39:29', '2025-08-20 03:39:36', 0),
(108, 1, 'Kenken', '09123456789', NULL, 'iphone 16 pro max', 'Gadgets', 20000.00, 6.00, 1200.00, '2025-08-20', '2025-08-20', NULL, NULL, 'claimed', ' Brgy. Trinidad', 12, NULL, '2025-08-20 03:40:19', '2025-08-20 03:40:28', 0),
(110, 1, 'Kenken', '09123456789', NULL, 'BIKE', 'Vehicle', 2000.00, 6.00, 120.00, '2025-08-20', '2025-08-20', NULL, NULL, 'claimed', ' Brgy. Trinidad', 9, NULL, '2025-08-20 05:14:04', '2025-08-20 05:14:16', 0),
(111, 1, 'Kenken', '09123456789', NULL, 'BIKE', 'Vehicle', 2000.00, 6.00, 120.00, '2025-08-20', '2025-08-20', NULL, NULL, 'claimed', ' Brgy. Trinidad', 9, NULL, '2025-08-20 05:14:31', '2025-08-20 05:31:47', 0),
(112, 1, 'Test Owner 2nd Branch 2', '09123456789', NULL, 'XRM 125', 'Vehicle', 800.00, 6.00, NULL, '2025-08-20', NULL, NULL, '2025-08-20', 'forfeited', 'test', 12, NULL, '2025-08-20 05:20:17', '2025-08-20 05:32:05', 0),
(113, 2, 'Test Owner ', '09123456789', NULL, 'XRM 125', 'Vehicle', 50.00, 6.00, NULL, '2025-08-20', NULL, NULL, NULL, 'pawned', 'TEST', 11, NULL, '2025-08-20 05:20:38', NULL, 0);

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
(8, NULL, 'super_admin', '$2y$10$RCfvdDWOKIKaA9yWjuJS6ObLGRyVjE.pHifIFYarTYhr5COZtnLRm', 'super_admin', 'Super Admin', 'active', NULL, '2025-08-14 13:36:07'),
(9, 1, 'cashier_main', '$2y$10$kv8rvQan1kyfYMGFZrkv.eeWUols7muHzSbyHz40QrQrcPWqD6DV6', 'cashier', 'Cashier Main Branch', 'active', NULL, '2025-08-14 13:36:07'),
(10, 2, 'cashier_gandara', '$2y$10$vs5O7hWLNbNEOJCEU/Et5uB7.AHrq.VeXGd2P7ZYdWU8vMCKy.6nq', 'cashier', 'Cashier 2nd Branch', 'active', NULL, '2025-08-14 13:36:07'),
(11, 2, 'admin_gandara', '$2y$10$5PuaTNO.DlygUl9KDBogaeR5sVaEeTAi6P6xiBn0hFZ2aqs2m6/Gu', 'admin', 'Admin 2nd Branch', 'active', NULL, '2025-08-14 13:36:07'),
(12, 1, 'admin_main', '$2y$10$WR6ENbCy3jAK1hYGSutq.Ol/TMyzPd/R9c5CSjrRRTUhpzIiohfj.', 'admin', 'Admin Main Branch', 'active', NULL, '2025-08-14 13:36:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

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
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

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
-- Constraints for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  ADD CONSTRAINT `fk_tubo_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tubo_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tubo_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
