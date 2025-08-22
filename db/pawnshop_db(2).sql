-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 10:58 AM
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
(23, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #6 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 13:09:51'),
(24, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #6 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 13:14:28'),
(25, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Ken Ken (Unit: iPhone 12 128GB, Category: Computer, Amount: ₱120.00)', '2025-08-21 13:17:55'),
(26, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #1 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 13:18:48'),
(27, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #4 (Unit: iPhone 12 128GB, Category: Computer, Amount: ₱120.00)', '2025-08-21 13:32:59'),
(28, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #4 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 13:33:18'),
(29, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #4 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 13:38:02'),
(30, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #asdasdas (Unit: iPhone 12 128GB, Category: Computer, Amount: ₱1,000.00)', '2025-08-21 13:46:51'),
(31, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #dgdfgfdfghgh (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 13:47:16'),
(32, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Test Owner (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 14:39:59'),
(33, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Test Owner33 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 15:01:54'),
(34, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Test Owner (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:05:59'),
(35, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #16 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:11:06'),
(36, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #6 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:11:57'),
(37, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Test Owner (Unit: iPhone 12 128GB, Category: Computer, Amount: ₱120.00)', '2025-08-21 15:12:04'),
(38, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Kenken Test (Unit: oppo, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 15:12:51'),
(39, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #2 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 15:13:49'),
(40, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #2 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:14:14'),
(41, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #6 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:14:40'),
(42, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #4 (Unit: AAA, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:19:02'),
(43, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #13 (Unit: AAA, Category: Computer, Amount: ₱120.00)', '2025-08-21 15:19:27'),
(44, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for pawnshop_db (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱120.00)', '2025-08-21 15:19:43'),
(45, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #3 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 15:20:28'),
(46, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #17 (Unit: realme, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-21 15:24:44'),
(47, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #5 (Unit: realme 8i, Category: Gadgets, Amount: ₱2,000.00)', '2025-08-21 15:30:48'),
(48, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Kenneth Son (Unit: oppo a5s, Category: Gadgets, Amount: ₱500.00)', '2025-08-21 15:31:36'),
(49, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #19 (Unit: realme 8i, Category: Gadgets, Amount: ₱1,500.00)', '2025-08-21 15:36:31'),
(50, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for KENNETH SON RAMOS (Unit: XRM 125, Category: Gadgets, Amount: ₱500.00)', '2025-08-22 02:21:38'),
(51, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #6 (Unit: XRM 125, Category: Computer, Amount: ₱50.00)', '2025-08-22 02:23:39'),
(52, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for super_admin (Unit: XRM 125, Category: Gadgets, Amount: ₱500.00)', '2025-08-22 02:23:56'),
(53, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #19 (Unit: ASUS Laptop, Category: Computer, Amount: ₱2,500.00)', '2025-08-22 02:40:24'),
(54, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for King JayR Montecalvo (Unit: LENOVO LAPTOP, Category: Computer, Amount: ₱5,000.00)', '2025-08-22 02:42:33'),
(55, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #22 (Unit: XRM 125, Category: Vehicle, Amount: ₱5,000.00)', '2025-08-22 02:43:08'),
(56, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #5 (Unit: XRM 125, Category: Vehicle, Amount: ₱50.00)', '2025-08-22 02:47:34'),
(57, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #5 (Unit: LENOVO LAPTOP, Category: Computer, Amount: ₱2,500.00)', '2025-08-22 03:01:20'),
(58, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #6 (Unit: LENOVO LAPTOP, Category: Computer, Amount: ₱2,500.00)', '2025-08-22 03:05:05'),
(59, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for King JayR Montecalvo (Unit: LENOVO LAPTOP, Category: Gadgets, Amount: ₱5,000.00)', '2025-08-22 03:05:54'),
(60, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #20 (Unit: LENOVO LAPTOP, Category: Gadgets, Amount: ₱2,500.00)', '2025-08-22 03:11:21'),
(61, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for King JayR Montecalvo (Unit: LENOVO LAPTOP, Category: Gadgets, Amount: ₱5,000.00)', '2025-08-22 03:11:34'),
(62, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for Customer #6 (Unit: XRM 125, Category: Vehicle, Amount: ₱50.00)', '2025-08-22 03:13:04'),
(63, 11, 2, 'Add Pawned Item', 'Admin 2nd Branch added a new pawn item for King JayR Montecalvo (Unit: LENOVO LAPTOP, Category: Gadgets, Amount: ₱5,000.00)', '2025-08-22 03:13:22'),
(64, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #6 (Unit: XRM 125, Category: Computer, Amount: ₱50.00)', '2025-08-22 04:01:24'),
(65, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for John Doe (Unit: LENOVO LAPTOP, Category: Computer, Amount: ₱3,000.00)', '2025-08-22 06:47:45'),
(66, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #23 (Unit: VIVO Phone, Category: Gadgets, Amount: ₱950.00)', '2025-08-22 06:48:28'),
(67, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #5 (Unit: VIVO Phone, Category: Gadgets, Amount: ₱950.00)', '2025-08-22 07:11:41'),
(68, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #5 (Unit: VIVO Phone, Category: Computer, Amount: ₱950.00)', '2025-08-22 07:12:24'),
(69, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #5 (Unit: VIVO Phone, Category: Computer, Amount: ₱10.00)', '2025-08-22 07:21:08'),
(70, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for John Doe (Unit: LENOVO LAPTOP, Category: Gadgets, Amount: ₱50.00)', '2025-08-22 07:21:41'),
(71, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #5 (Unit: VIVO Phone, Category: Gadgets, Amount: ₱10.00)', '2025-08-22 07:55:00'),
(72, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #20 (Unit: VIVO Phone, Category: Gadgets, Amount: ₱5.00)', '2025-08-22 08:01:20'),
(73, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for asdasdasd (Unit: LENOVO LAPTOP, Category: Gadgets, Amount: ₱1.00)', '2025-08-22 08:05:34'),
(74, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Shiela May (Unit: DELL Laptop, Category: Camera, Amount: ₱5,000.00)', '2025-08-22 08:15:55');

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
(1, 'Calbayog Branch - Main', 'Navarro St. , Calbayog City, Samar', '09171234567', 'active', 0.0600, 6400.00, '2025-08-14 12:57:02'),
(2, 'Gandara Branch', 'Gandara Samar', '09181234567', 'active', 0.0600, 13850.00, '2025-08-14 12:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `cash_ledger`
--

CREATE TABLE `cash_ledger` (
  `ledger_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `ref_table` varchar(50) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `txn_type` varchar(50) NOT NULL,
  `direction` enum('in','out') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cash_ledger`
--

INSERT INTO `cash_ledger` (`ledger_id`, `branch_id`, `ref_table`, `ref_id`, `amount`, `txn_type`, `direction`, `description`, `notes`, `user_id`, `created_at`) VALUES
(1, 1, 'pawned_items', 151, 4500.00, 'pawn_edit', 'in', NULL, 'Edit pawn amount from 5000 to 500', NULL, '2025-08-22 08:19:54'),
(2, 1, 'pawned_items', 151, 450.00, 'pawn_edit', 'in', NULL, 'Edit pawn amount from 500 to 50', 12, '2025-08-22 08:21:24'),
(3, 1, 'pawned_items', 151, 100.00, 'pawn_edit', 'out', 'Pawn edit (ID #151)', 'Edit pawn amount from 50 to 150', 12, '2025-08-22 08:26:24');

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

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `contact_no`, `address`, `photo_path`, `created_at`) VALUES
(1, 'Juan Dela Cruz', '09181234567', 'Manila', NULL, '2025-08-21 00:59:12'),
(2, 'Maria Santos', '09182345678', 'Pasay', NULL, '2025-08-21 00:59:12'),
(3, 'Pedro Reyes', '09183456789', 'Makati', NULL, '2025-08-21 00:59:12'),
(4, 'Josefa Aquino', '09184567890', 'Quezon City', NULL, '2025-08-21 00:59:13'),
(5, 'Andres Bonifacio', '09185678901', 'Caloocan', NULL, '2025-08-21 00:59:13'),
(6, 'Gabriela Silang', '09186789012', 'Valenzuela', NULL, '2025-08-21 00:59:13'),
(19, 'Kenneth Son', '09123456789', 'Brgy. Sabang', NULL, '2025-08-21 15:31:36'),
(20, 'KENNETH SON RAMOS', '09185678901', 'Caloocan', NULL, '2025-08-22 02:21:38'),
(21, 'super_admin', '09184567890', 'Quezon City', NULL, '2025-08-22 02:23:56'),
(22, 'King JayR Montecalvo', '09123456789', 'Brgy. Saljag', NULL, '2025-08-22 02:42:33'),
(23, 'John Doe', '09123456789', 'Brgy. Dimahanap', NULL, '2025-08-22 06:47:45'),
(24, 'asdasdasd', '09123456789', 'Brgy. Saljag', NULL, '2025-08-22 08:05:34'),
(25, 'Shiela May', '09123456789', 'Brgy. Mahusay', NULL, '2025-08-22 08:15:55');

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
  `customer_id` int(11) DEFAULT NULL,
  `unit_description` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `amount_pawned` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT 6.00,
  `date_pawned` date NOT NULL,
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

INSERT INTO `pawned_items` (`pawn_id`, `branch_id`, `customer_id`, `unit_description`, `category`, `amount_pawned`, `interest_rate`, `date_pawned`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `is_deleted`) VALUES
(151, 1, 5, 'HP Laptop', 'Computer', 150.00, 6.00, '2025-08-20', 'pawned', 'Test ', 12, NULL, '2025-08-21 15:30:48', '2025-08-22 08:26:24', 0),
(152, 1, 19, 'oppo a5s', 'Gadgets', 500.00, 6.00, '2025-08-21', 'pawned', 'test only', 12, NULL, '2025-08-21 15:31:36', NULL, 0),
(153, 1, 19, 'realme 8i', 'Gadgets', 1500.00, 6.00, '2025-08-21', 'pawned', 'Test Only', 12, NULL, '2025-08-21 15:36:31', NULL, 0),
(154, 2, 20, 'XRM 125', 'Gadgets', 500.00, 6.00, '2025-08-22', 'pawned', 'TEST', 11, NULL, '2025-08-22 02:21:38', NULL, 0),
(155, 2, 6, 'XRM 125', 'Computer', 50.00, 6.00, '2025-08-22', 'pawned', 'TEST', 11, NULL, '2025-08-22 02:23:39', NULL, 0),
(156, 2, 21, 'XRM 125', 'Gadgets', 500.00, 6.00, '2025-08-22', 'pawned', 'TEST', 11, NULL, '2025-08-22 02:23:56', NULL, 0),
(157, 2, 19, 'ASUS Laptop', 'Computer', 2500.00, 6.00, '2025-08-22', 'pawned', 'for testing', 11, NULL, '2025-08-22 02:40:24', NULL, 0),
(158, 2, 22, 'LENOVO LAPTOP', 'Computer', 5000.00, 6.00, '2025-08-22', 'pawned', 'for claims', 11, NULL, '2025-08-22 02:42:33', NULL, 0),
(159, 2, 22, 'XRM 125', 'Vehicle', 5000.00, 6.00, '2025-08-22', 'pawned', 'Test Pawn Item', 11, NULL, '2025-08-22 02:43:08', NULL, 0),
(160, 2, 5, 'XRM 125', 'Vehicle', 50.00, 6.00, '2025-08-22', 'pawned', 'TEST', 11, NULL, '2025-08-22 02:47:34', NULL, 0),
(161, 2, 5, 'LENOVO LAPTOP', 'Computer', 2500.00, 6.00, '2025-08-22', 'pawned', 'for testing', 11, NULL, '2025-08-22 03:01:20', NULL, 0),
(162, 2, 6, 'LENOVO LAPTOP', 'Computer', 2500.00, 6.00, '2025-08-22', 'pawned', 'for testing', 11, NULL, '2025-08-22 03:05:05', NULL, 0),
(163, 2, 22, 'LENOVO LAPTOP', 'Gadgets', 5000.00, 6.00, '2025-08-22', 'pawned', 'for claims', 11, NULL, '2025-08-22 03:05:54', NULL, 0),
(164, 2, 20, 'LENOVO LAPTOP', 'Gadgets', 2500.00, 6.00, '2025-08-22', 'pawned', 'for testing', 11, NULL, '2025-08-22 03:11:21', NULL, 0),
(165, 2, 22, 'LENOVO LAPTOP', 'Gadgets', 5000.00, 6.00, '2025-08-22', 'pawned', 'for claims', 11, NULL, '2025-08-22 03:11:34', NULL, 0),
(166, 2, 6, 'XRM 125', 'Vehicle', 50.00, 6.00, '2025-08-22', 'pawned', 'TEST', 11, NULL, '2025-08-22 03:13:04', NULL, 0),
(167, 2, 22, 'LENOVO LAPTOP', 'Gadgets', 5000.00, 6.00, '2025-08-22', 'pawned', 'for claims', 11, NULL, '2025-08-22 03:13:22', NULL, 0),
(168, 1, 6, 'Edit Test', 'Gadgets', 500.00, 6.00, '2025-08-23', 'pawned', 'edit test', 12, NULL, '2025-08-22 04:01:24', '2025-08-22 08:13:32', 0),
(169, 1, 23, 'LENOVO LAPTOP', 'Computer', 3000.00, 6.00, '2025-08-22', 'pawned', 'for pawning', 12, NULL, '2025-08-22 06:47:45', NULL, 0),
(170, 1, 23, 'VIVO Phone', 'Gadgets', 950.00, 6.00, '2025-08-22', 'pawned', 'TEST', 12, NULL, '2025-08-22 06:48:28', NULL, 0),
(171, 1, 5, 'VIVO Phone', 'Gadgets', 950.00, 6.00, '2025-08-22', 'pawned', 'TEST', 12, NULL, '2025-08-22 07:11:41', NULL, 0),
(172, 1, 5, 'VIVO Phone', 'Computer', 950.00, 6.00, '2025-08-22', 'pawned', 'TEST', 12, NULL, '2025-08-22 07:12:24', NULL, 0),
(173, 1, 5, 'VIVO Phone', 'Computer', 10.00, 6.00, '2025-08-22', 'pawned', 'TEST', 12, NULL, '2025-08-22 07:21:08', NULL, 0),
(174, 1, 23, 'LENOVO LAPTOP', 'Gadgets', 50.00, 6.00, '2025-08-22', 'pawned', 'for pawning', 12, NULL, '2025-08-22 07:21:41', NULL, 0),
(175, 1, 5, 'VIVO Phone', 'Gadgets', 10.00, 6.00, '2025-08-22', 'pawned', 'TEST', 12, NULL, '2025-08-22 07:55:00', NULL, 0),
(176, 1, 20, 'VIVO Phone', 'Gadgets', 5.00, 6.00, '2025-08-22', 'pawned', 'TEST', 12, NULL, '2025-08-22 08:01:20', NULL, 0),
(177, 1, 24, 'LENOVO LAPTOP', 'Gadgets', 1.00, 6.00, '2025-08-22', 'pawned', 'for claims', 12, NULL, '2025-08-22 08:05:34', NULL, 0),
(178, 1, 25, 'DELL Laptop', 'Camera', 5000.00, 6.00, '2025-08-22', 'pawned', 'with charger & mouse', 12, NULL, '2025-08-22 08:15:55', NULL, 0);

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
  `months_covered` int(11) NOT NULL DEFAULT 1,
  `interest_rate` decimal(5,2) NOT NULL,
  `interest_amount` decimal(10,2) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `txn_code` varchar(50) DEFAULT NULL,
  `payment_method` enum('cash','gcash','bank','other') DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `ref_table` (`ref_table`,`ref_id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `fk_claim_pawn` (`pawn_id`),
  ADD KEY `fk_claim_cashier` (`cashier_id`),
  ADD KEY `idx_claim_branch_date` (`branch_id`,`date_claimed`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

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
  ADD KEY `idx_pawn_dates` (`date_pawned`);

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
  ADD UNIQUE KEY `txn_code` (`txn_code`),
  ADD KEY `cashier_id` (`cashier_id`),
  ADD KEY `pawn_id` (`pawn_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `date_paid` (`date_paid`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `remata_sales`
--
ALTER TABLE `remata_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  MODIFY `tubo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `cash_ledger_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

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
  ADD CONSTRAINT `tubo_payments_ibfk_1` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`),
  ADD CONSTRAINT `tubo_payments_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  ADD CONSTRAINT `tubo_payments_ibfk_3` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
