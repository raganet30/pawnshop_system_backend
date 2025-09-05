-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 07:09 PM
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
  `branch_id` int(11) DEFAULT NULL,
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
  `interest_rate` decimal(5,2) DEFAULT 0.06,
  `cash_on_hand` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `branch_address`, `branch_phone`, `status`, `interest_rate`, `cash_on_hand`, `created_at`) VALUES
(1, 'Calbayog Branch - Main', 'Navarro St., Calbayog City, Samar', '09171234567', 'active', 0.06, 0.00, '2025-08-14 12:57:02'),
(2, 'Gandara Branch', 'Gandara Samar', '09181234567', 'active', 0.06, 0.00, '2025-08-14 12:57:02');

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
(32, 'Test Owner', '09123456789', 'Test', NULL, '2025-09-02 13:52:42'),
(33, 'MARIA CLARA', '09123456789', 'Test', NULL, '2025-09-03 15:04:20'),
(34, 'KEN KEN', '09123456789', 'Test', NULL, '2025-09-03 15:05:07');

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
-- Table structure for table `partial_payments`
--

CREATE TABLE `partial_payments` (
  `pp_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `interest_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `principal_paid` decimal(12,2) NOT NULL,
  `remaining_principal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','settled') NOT NULL DEFAULT 'active',
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `amount_pawned` decimal(10,2) NOT NULL COMMENT 'current pawned amount/remaining balance',
  `original_amount_pawned` decimal(10,2) DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT 6.00,
  `date_pawned` date NOT NULL,
  `current_due_date` date DEFAULT NULL,
  `status` enum('pawned','claimed','forfeited') NOT NULL DEFAULT 'pawned',
  `has_partial_payments` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `photo_path` varchar(256) DEFAULT NULL,
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
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `cash_threshold` decimal(12,2) DEFAULT 0.00,
  `pawn_maturity_reminder_days` int(11) DEFAULT 3,
  `export_format` varchar(20) DEFAULT 'excel',
  `report_info` text DEFAULT NULL,
  `backup_frequency` varchar(20) DEFAULT 'manual',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_timeout` int(11) DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `cash_threshold`, `pawn_maturity_reminder_days`, `export_format`, `report_info`, `backup_frequency`, `updated_at`, `session_timeout`) VALUES
(1, 10000.00, 10, 'excel', '', 'daily', '2025-09-01 12:59:50', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tubo_payments`
--

CREATE TABLE `tubo_payments` (
  `tubo_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `payment_type` enum('renewal','penalty','other') DEFAULT 'renewal',
  `branch_id` int(11) NOT NULL,
  `date_paid` date NOT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `months_covered` int(11) NOT NULL DEFAULT 1,
  `new_due_date` date DEFAULT NULL,
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
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `branch_id`, `username`, `password_hash`, `role`, `full_name`, `status`, `last_login`, `photo_path`, `created_at`) VALUES
(8, NULL, 'super_admin', '$2y$10$RCfvdDWOKIKaA9yWjuJS6ObLGRyVjE.pHifIFYarTYhr5COZtnLRm', 'super_admin', 'Super Admin', 'active', '2025-09-06 01:05:31', 'uploads/avatars/user_8_1756613481.jpg', '2025-08-14 13:36:07'),
(9, 1, 'cashier_main_husay', '$2y$10$kv8rvQan1kyfYMGFZrkv.eeWUols7muHzSbyHz40QrQrcPWqD6DV6', 'cashier', 'Cashier Husay Daw', 'active', '2025-09-02 23:35:33', 'uploads/avatars/avatar_68b33b667927f.jpg', '2025-08-14 13:36:07'),
(10, 2, 'cashier_gandara', '$2y$10$vs5O7hWLNbNEOJCEU/Et5uB7.AHrq.VeXGd2P7ZYdWU8vMCKy.6nq', 'cashier', 'Cashier 2nd Branch', 'active', '2025-09-03 21:04:07', 'uploads/avatars/avatar_68b31a1abda62.jpg', '2025-08-14 13:36:07'),
(11, 2, 'admin_gandara', '$2y$10$5PuaTNO.DlygUl9KDBogaeR5sVaEeTAi6P6xiBn0hFZ2aqs2m6/Gu', 'admin', 'Admin 2nd Branch', 'active', '2025-09-05 14:39:24', 'uploads/avatars/avatar_68b3c903b9cd7.jpg', '2025-08-14 13:36:07'),
(12, 1, 'admin_main', '$2y$10$Q..9xrdBpGrXd3p.j66mC.Sye20TmUmYRYy9Zuc.BPdhc1TgiO95C', 'admin', 'Admin Main User', 'active', '2025-09-06 00:59:51', 'uploads/avatars/avatar_68b3c90c783b1.jpg', '2025-08-14 13:36:07'),
(13, 1, 'test_user', '$2y$10$WHK7F9k4iXy/dLsiDM/89e8MnSnuORV5.QN.0my3ZBWpfab8k6/Ou', 'admin', 'test user', 'active', '2025-08-29 20:36:51', 'uploads/avatars/avatar_68b3b5ca3d4d7.jpg', '2025-08-29 12:31:09'),
(14, 1, 'tester', '$2y$10$Fs8eOf3ZvpG7/Piw0GVNxuhPoEXeI22KKVGky64z2TW2.K7AaSqlW', 'admin', 'test test', 'active', '2025-08-31 17:47:27', 'uploads/avatars/avatar_68b3c162ce7c3.jpg', '2025-08-30 15:48:21');

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
-- Indexes for table `partial_payments`
--
ALTER TABLE `partial_payments`
  ADD PRIMARY KEY (`pp_id`),
  ADD KEY `idx_pawn_date` (`pawn_id`),
  ADD KEY `idx_branch_date` (`branch_id`),
  ADD KEY `fk_pp_user` (`user_id`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=852;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=491;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `forfeitures`
--
ALTER TABLE `forfeitures`
  MODIFY `forfeiture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `interest_rates`
--
ALTER TABLE `interest_rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `partial_payments`
--
ALTER TABLE `partial_payments`
  MODIFY `pp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pawned_items`
--
ALTER TABLE `pawned_items`
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT for table `remata_sales`
--
ALTER TABLE `remata_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  MODIFY `tubo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Constraints for table `partial_payments`
--
ALTER TABLE `partial_payments`
  ADD CONSTRAINT `fk_pp_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pp_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

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
