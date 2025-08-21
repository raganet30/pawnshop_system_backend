-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 05:37 PM
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
(49, 12, 1, 'Add Pawned Item', 'Admin Main Branch added a new pawn item for Customer #19 (Unit: realme 8i, Category: Gadgets, Amount: ₱1,500.00)', '2025-08-21 15:36:31');

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
(1, 'Calbayog Branch - Main', 'Navarro St. , Calbayog City, Samar', '09171234567', 'active', 0.0600, 6000.00, '2025-08-14 12:57:02'),
(2, 'Gandara Branch', 'Gandara Samar', '09181234567', 'active', 0.0600, 50000.00, '2025-08-14 12:57:02');

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
  `type` enum('in','out') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(19, 'Kenneth Son', '09123456789', 'Brgy. Sabang', NULL, '2025-08-21 15:31:36');

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
(151, 1, 5, 'realme 8i', 'Gadgets', 2000.00, 6.00, '2025-08-21', 'pawned', 'Test Only', 12, NULL, '2025-08-21 15:30:48', NULL, 0),
(152, 1, 19, 'oppo a5s', 'Gadgets', 500.00, 6.00, '2025-08-21', 'pawned', 'test only', 12, NULL, '2025-08-21 15:31:36', NULL, 0),
(153, 1, 19, 'realme 8i', 'Gadgets', 1500.00, 6.00, '2025-08-21', 'pawned', 'Test Only', 12, NULL, '2025-08-21 15:36:31', NULL, 0);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

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
