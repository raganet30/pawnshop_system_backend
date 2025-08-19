-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2025 at 10:25 AM
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
(1, 'Main Branch', 'Navarro St. , Calbayog City, Samar', '09171234567', 'active', 0.0600, 1000.00, '2025-08-14 12:57:02'),
(2, 'Sample 2nd Branch', 'Sample', '09181234567', 'active', 0.0600, 500.00, '2025-08-14 12:57:02');

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
(104, 1, '', 'in', 1141.62, 'claims', 42, 'Pawn claimed', 8, '2025-08-19 07:21:11');

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
(8, 1, 'admin', '$2y$10$npjSjuSvSvk9XzmFNK0F2e6cWLst/qJvJSj1nejEw23ARNmzmnjJa', 'super_admin', 'Admin User', 'active', NULL, '2025-08-14 13:36:07'),
(9, 1, 'cashier', '$2y$10$KaSPllnDlu3.wz39UXATQuyLwkxRkZCfnjvNcvCfuxyBP8d/ktafq', 'cashier', 'Cashier User', 'active', NULL, '2025-08-14 13:36:07'),
(10, 2, 'cashier2', '$2y$10$KaSPllnDlu3.wz39UXATQuyLwkxRkZCfnjvNcvCfuxyBP8d/ktafq', '', 'Manager User', 'active', NULL, '2025-08-14 13:36:07'),
(11, 2, 'admin2', '$2y$10$npjSjuSvSvk9XzmFNK0F2e6cWLst/qJvJSj1nejEw23ARNmzmnjJa', 'super_admin', 'Admin User', 'active', NULL, '2025-08-14 13:36:07');

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
