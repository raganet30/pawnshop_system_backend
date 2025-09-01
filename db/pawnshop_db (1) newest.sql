-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2025 at 05:10 PM
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

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `branch_id`, `action_type`, `description`, `created_at`) VALUES
(503, 12, 1, 'Login', 'Logged In', '2025-08-28 11:11:42'),
(504, 12, 1, 'Cash On Hand Adjustment', 'COH Set: +₱5,000.00 (Old COH: ₱0.00, New COH: ₱5,000.00)', '2025-08-28 12:00:39'),
(505, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #5 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-28 12:01:15'),
(506, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #6 (Unit: SAMSUNG PHONE, Category: Gadgets, Amount: ₱1,500.00)', '2025-08-28 12:01:43'),
(507, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 272, Unit: iPhone 12 128GB, Total Amount Paid: ₱1,060.00', '2025-08-28 12:02:01'),
(508, 12, 1, 'Logout', 'Logged Out', '2025-08-28 12:24:24'),
(511, 12, 1, 'Login', 'Logged In', '2025-08-28 12:49:09'),
(512, 12, 1, 'Logout', 'Logged Out', '2025-08-28 12:49:23'),
(515, 12, 1, 'Login', 'Logged In', '2025-08-28 13:08:19'),
(516, 12, 1, 'Logout', 'Logged Out', '2025-08-28 13:18:11'),
(520, 12, 1, 'Login', 'Logged In', '2025-08-28 13:24:50'),
(521, 12, 1, 'Logout', 'Logged Out', '2025-08-28 13:36:45'),
(524, 12, 1, 'Login', 'Logged In', '2025-08-28 13:37:34'),
(527, 13, 1, 'Login', 'Logged In', '2025-08-29 12:36:51'),
(528, 13, 1, 'Cash On Hand Adjustment', 'COH Set: -₱2,560.00 (Old COH: ₱3,560.00, New COH: ₱1,000.00)', '2025-08-29 12:37:41'),
(529, 13, 1, 'Logout', 'Logged Out', '2025-08-29 12:38:09'),
(532, 12, 1, 'Login', 'Logged In', '2025-08-29 14:14:53'),
(533, 12, 1, 'Login', 'Logged In', '2025-08-29 14:17:04'),
(534, 12, 1, 'Login', 'Logged In', '2025-08-29 14:18:27'),
(535, 12, 1, 'Login', 'Logged In', '2025-08-29 14:20:31'),
(536, 12, 1, 'Login', 'Logged In', '2025-08-29 14:22:22'),
(537, 12, 1, 'Logout', 'Logged Out', '2025-08-29 14:22:36'),
(538, 12, 1, 'Login', 'Logged In', '2025-08-29 14:23:22'),
(539, 12, 1, 'Logout', 'Logged Out', '2025-08-29 14:23:24'),
(540, 12, 1, 'Login', 'Logged In', '2025-08-30 12:52:34'),
(541, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #29 (Unit: SAMSUNG PHONE, Category: Gadgets, Amount: ₱500.00)', '2025-08-30 12:59:14'),
(542, 12, 1, 'Logout', 'Logged Out', '2025-08-30 14:08:36'),
(545, 12, 1, 'Login', 'Logged In', '2025-08-30 14:08:48'),
(546, 12, 1, 'Logout', 'Logged Out', '2025-08-30 14:09:58'),
(547, 12, 1, 'Login', 'Logged In', '2025-08-30 14:10:11'),
(548, 12, 1, 'Logout', 'Logged Out', '2025-08-30 14:24:55'),
(551, 12, 1, 'Login', 'Logged In', '2025-08-30 14:39:22'),
(552, 12, 1, 'Logout', 'Logged Out', '2025-08-30 14:40:07'),
(553, 12, 1, 'Login', 'Logged In', '2025-08-30 14:40:09'),
(554, 12, 1, 'Change Password', 'Changed New Password', '2025-08-30 14:45:53'),
(555, 12, 1, 'Logout', 'Logged Out', '2025-08-30 14:47:58'),
(560, 12, 1, 'Login', 'Logged In', '2025-08-30 14:55:41'),
(561, 12, 1, 'Logout', 'Logged Out', '2025-08-30 14:56:07'),
(562, 9, 1, 'Login', 'Logged In', '2025-08-30 14:56:11'),
(563, 9, NULL, 'Update Profile', 'Updated Profile Details', '2025-08-30 15:04:34'),
(564, 9, 1, 'Update Profile', 'Updated Profile Details', '2025-08-30 15:05:21'),
(565, 9, 1, 'Logout', 'Logged Out', '2025-08-30 15:07:28'),
(566, 9, 1, 'Login', 'Logged In', '2025-08-30 15:08:18'),
(567, 9, 1, 'Logout', 'Logged Out', '2025-08-30 15:08:40'),
(577, 12, 1, 'Login', 'Logged In', '2025-08-30 16:13:17'),
(578, 12, 1, 'Logout', 'Logged Out', '2025-08-30 16:23:19'),
(579, 12, 1, 'Login', 'Logged In', '2025-08-30 16:28:03'),
(580, 12, 1, 'Logout', 'Logged Out', '2025-08-30 16:28:18'),
(583, 12, 1, 'Login', 'Logged In', '2025-08-30 16:29:23'),
(584, 12, 1, 'Login', 'Logged In', '2025-08-30 16:31:14'),
(585, 12, 1, 'Login', 'Logged In', '2025-08-30 16:35:23'),
(596, 14, 1, 'Login', 'Logged In', '2025-08-31 09:47:27'),
(597, 14, 1, 'Logout', 'Logged Out', '2025-08-31 09:56:43'),
(600, 12, 1, 'Login', 'Logged In', '2025-08-31 10:15:55'),
(601, 12, 1, 'Logout', 'Logged Out', '2025-08-31 10:16:04'),
(604, 12, 1, 'Login', 'Logged In', '2025-08-31 10:29:04'),
(605, 12, 1, 'Logout', 'Logged Out', '2025-08-31 10:30:52'),
(606, 9, 1, 'Login', 'Logged In', '2025-08-31 10:31:00'),
(607, 9, 1, 'Logout', 'Logged Out', '2025-08-31 10:31:06'),
(608, 12, 1, 'Login', 'Logged In', '2025-08-31 10:31:09'),
(609, 12, 1, 'Logout', 'Logged Out', '2025-08-31 10:33:54'),
(610, 12, 1, 'Login', 'Logged In', '2025-08-31 10:33:55'),
(611, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 273, Unit: SAMSUNG PHONE, Total Amount Paid: ₱1,590.00', '2025-08-31 10:34:07'),
(612, 12, 1, 'Edit Pawn Item', 'Edit pawn ID: 274 details', '2025-08-31 10:34:55'),
(613, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 274, Unit: SAMSUNG PHONE, Total Amount: ₱500.00', '2025-08-31 10:35:37'),
(614, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #4 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱500.00)', '2025-08-31 10:35:59'),
(615, 12, 1, 'Logout', 'Logged Out', '2025-08-31 10:36:13'),
(618, 11, 2, 'Login', 'Logged In', '2025-08-31 10:36:27'),
(619, 11, 2, 'Cash On Hand Adjustment', 'COH Set: +₱5,000.00 (Old COH: ₱0.00, New COH: ₱5,000.00)', '2025-08-31 10:37:01'),
(620, 11, 2, 'Add Pawned Item', 'Added a new pawn item for Customer #23 (Unit: SAMSUNG PHONE, Category: Gadgets, Amount: ₱500.00)', '2025-08-31 10:37:13'),
(621, 11, 2, 'Logout', 'Logged Out', '2025-08-31 10:37:16'),
(624, 12, 1, 'Login', 'Logged In', '2025-08-31 10:41:02'),
(625, 12, 1, 'Logout', 'Logged Out', '2025-08-31 10:41:31'),
(626, 12, 1, 'Login', 'Logged In', '2025-08-31 10:41:36'),
(627, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 274, Amount: ₱500.00, Item: SAMSUNG PHONE)', '2025-08-31 10:50:21'),
(628, 12, 1, 'Logout', 'Logged Out', '2025-08-31 11:12:01'),
(631, 9, 1, 'Login', 'Logged In', '2025-08-31 11:12:42'),
(632, 9, 1, 'Logout', 'Logged Out', '2025-08-31 11:21:57'),
(637, 12, 1, 'Login', 'Logged In', '2025-08-31 11:23:28'),
(638, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 274, Unit: SAMSUNG PHONE, Total Amount: ₱500.00', '2025-08-31 11:23:36'),
(639, 12, 1, 'Logout', 'Logged Out', '2025-08-31 11:30:05'),
(644, 9, 1, 'Login', 'Logged In', '2025-08-31 11:40:07'),
(645, 9, 1, 'Logout', 'Logged Out', '2025-08-31 11:40:16'),
(649, 12, 1, 'Login', 'Logged In', '2025-08-31 13:27:14'),
(650, 12, 1, 'Logout', 'Logged Out', '2025-08-31 13:27:23'),
(658, 12, 1, 'Login', 'Logged In', '2025-09-01 12:54:57'),
(659, 12, 1, 'Logout', 'Logged Out', '2025-09-01 12:56:38'),
(660, 12, 1, 'Login', 'Logged In', '2025-09-01 12:56:41'),
(661, 12, 1, 'Logout', 'Logged Out', '2025-09-01 12:56:44'),
(665, 12, 1, 'Login', 'Logged In', '2025-09-01 13:10:13'),
(666, 12, 1, 'Login', 'Logged In', '2025-09-01 13:10:13'),
(667, 12, 1, 'Logout', 'Logged Out', '2025-09-01 13:14:53'),
(671, 12, 1, 'Login', 'Logged In', '2025-09-01 14:31:33'),
(672, 12, 1, 'Logout', 'Logged Out', '2025-09-01 15:01:25'),
(673, 9, 1, 'Login', 'Logged In', '2025-09-01 15:01:27'),
(674, 9, 1, 'Logout', 'Logged Out', '2025-09-01 15:01:35'),
(677, 12, 1, 'Login', 'Logged In', '2025-09-01 15:01:46');

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
(1, 'Calbayog Branch - Main', 'Navarro St., Calbayog City, Samar', '09171234567', 'active', 0.06, 2090.00, '2025-08-14 12:57:02'),
(2, 'Gandara Branch', 'Gandara Samar', '09181234567', 'active', 0.06, 4500.00, '2025-08-14 12:57:02'),
(3, 'TEST', 'TEST', '09123456789', 'active', 0.02, 0.00, '2025-08-30 17:06:02'),
(4, 'TEST BRANCH 2', 'TEST BRANCH 2 ADDRESS', '09123456789', 'inactive', 0.01, 0.00, '2025-08-31 04:23:16');

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
(399, 1, 'branches', 1, 5000.00, 'coh_adjustment', 'in', 'Set COH Adjustment', 'initial balance', 12, '2025-08-28 12:00:39'),
(400, 1, 'pawned_items', 272, 1000.00, 'pawn', 'out', 'Pawn Add (ID #272)', 'iPhone 12 128GB', 12, '2025-08-28 12:01:15'),
(401, 1, 'pawned_items', 273, 1500.00, 'pawn', 'out', 'Pawn Add (ID #273)', 'SAMSUNG PHONE', 12, '2025-08-28 12:01:43'),
(402, 1, 'claims', 272, 1060.00, 'claim', 'in', 'Claim (ID #151)', 'Pawn ID #272 claimed with interest + penalty (if any)', 12, '2025-08-28 12:02:01'),
(403, 1, 'branches', 1, 2560.00, 'coh_adjustment', 'out', 'Set COH Adjustment', 'test', 13, '2025-08-29 12:37:41'),
(405, 1, 'claims', 273, 1590.00, 'claim', 'in', 'Claim (ID #152)', 'Pawn ID #273 claimed with interest + penalty (if any)', 12, '2025-08-31 10:34:07'),
(407, 1, 'pawned_items', 275, 500.00, 'pawn', 'out', 'Pawn Add (ID #275)', 'iPhone 12 128GB', 12, '2025-08-31 10:35:59'),
(408, 2, 'branches', 2, 5000.00, 'coh_adjustment', 'in', 'Set COH Adjustment', 'initial balance', 11, '2025-08-31 10:37:01'),
(409, 2, 'pawned_items', 276, 500.00, 'pawn', 'out', 'Pawn Add (ID #276)', 'SAMSUNG PHONE', 11, '2025-08-31 10:37:13'),
(410, 1, 'forfeitures', 274, 500.00, 'forfeit', 'out', 'Revert Forfeit (Pawn ID #274)', 'Reverted forfeited pawned item. COH deducted ₱500.00', 12, '2025-08-31 10:50:21'),
(411, 1, 'forfeitures', 274, 500.00, 'forfeit', 'in', 'Pawn Forfeiture', 'Pawn ID #274 forfeited - amount added to COH', 12, '2025-08-31 11:23:36');

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
(151, 272, 1, '2025-08-28', 1, 0.06, 60.00, 1000.00, 0.00, 1060.00, 12, '', 'uploads/claimants/claimant_272_1756382521.png', '2025-08-28 12:02:01'),
(152, 273, 1, '2025-08-31', 1, 0.06, 90.00, 1500.00, 0.00, 1590.00, 12, '', 'uploads/claimants/claimant_273_1756636447.png', '2025-08-31 10:34:07');

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
(20, 'King JayR', '09123456789', 'Brgy. Sabang', NULL, '2025-08-21 15:53:32'),
(21, 'May May', '09123456789', 'Brgy. Malopalo', NULL, '2025-08-22 13:20:45'),
(22, 'Rab Rab', '09123456789', 'Brgy. Malopalo', NULL, '2025-08-23 16:54:11'),
(23, 'Nonoy Mercader', '09123456789', 'Brgy. Malopalo', NULL, '2025-08-24 11:40:13'),
(24, 'Nonoy', '', 'Test', NULL, '2025-08-24 18:07:05'),
(25, 'PRINCESS MAE DELAROSA', '09456789456', 'Matobato', NULL, '2025-08-25 10:02:13'),
(26, 'PRINCESS MAE DELAROSA', '09123456789', 'Matobato', NULL, '2025-08-25 10:04:40'),
(27, 'RODULFO GUTIEREZ', '09123456789', 'Matobato', NULL, '2025-08-25 10:51:53'),
(28, 'ROBERTO DEMAKITA', '09123456789', 'IPAO', NULL, '2025-08-25 10:52:30'),
(29, 'MYRNA LIM', '09123456789', 'IPAO', NULL, '2025-08-25 10:53:35'),
(30, 'MAYEL MONTAÑO', '09123456789', 'BRGY. MARCATUBIG', NULL, '2025-08-27 12:26:14');

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

--
-- Dumping data for table `forfeitures`
--

INSERT INTO `forfeitures` (`forfeiture_id`, `pawn_id`, `branch_id`, `date_forfeited`, `reason`, `notes`, `created_at`) VALUES
(41, 274, 1, '2025-08-31', 'overdue', 'TEST', '2025-08-31 11:23:36');

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
  `date_paid` datetime NOT NULL DEFAULT current_timestamp(),
  `interest_rate` decimal(5,2) NOT NULL,
  `interest_amount` decimal(12,2) NOT NULL,
  `principal_paid` decimal(12,2) NOT NULL,
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
(272, 1, 5, 'iPhone 12 128GB', 'Gadgets', 1000.00, 6.00, '2025-08-28', 'claimed', 'test pawn', 12, NULL, '2025-08-28 12:01:15', '2025-08-28 12:02:01', 0),
(273, 1, 6, 'SAMSUNG PHONE', 'Gadgets', 1500.00, 6.00, '2025-08-28', 'claimed', 'TEST', 12, NULL, '2025-08-28 12:01:43', '2025-08-31 10:34:07', 0),
(274, 1, 29, 'SAMSUNG PHONE', 'Computer', 500.00, 6.00, '2025-06-01', 'forfeited', 'TEST', 12, NULL, '2025-08-30 12:59:14', '2025-08-31 11:23:36', 0),
(275, 1, 4, 'iPhone 12 128GB', 'Gadgets', 500.00, 6.00, '2025-08-31', 'pawned', 'test only', 12, NULL, '2025-08-31 10:35:59', NULL, 0),
(276, 2, 23, 'SAMSUNG PHONE', 'Gadgets', 500.00, 6.00, '2025-08-31', 'pawned', 'test only', 11, NULL, '2025-08-31 10:37:13', NULL, 0);

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

--
-- Dumping data for table `tubo_payments`
--

INSERT INTO `tubo_payments` (`tubo_id`, `pawn_id`, `branch_id`, `date_paid`, `months_covered`, `interest_rate`, `interest_amount`, `cashier_id`, `txn_code`, `payment_method`, `notes`, `created_at`) VALUES
(101, 272, 1, '2025-08-28', 1, 0.06, 60.00, 12, NULL, 'cash', 'Claim payment', '2025-08-28 12:02:01'),
(102, 273, 1, '2025-08-31', 1, 0.06, 90.00, 12, NULL, 'cash', 'Claim payment', '2025-08-31 10:34:07');

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
(8, NULL, 'super_admin', '$2y$10$RCfvdDWOKIKaA9yWjuJS6ObLGRyVjE.pHifIFYarTYhr5COZtnLRm', 'super_admin', 'Super Admin', 'active', '2025-09-01 23:01:39', 'uploads/avatars/user_8_1756613481.jpg', '2025-08-14 13:36:07'),
(9, 1, 'cashier_main_husay', '$2y$10$kv8rvQan1kyfYMGFZrkv.eeWUols7muHzSbyHz40QrQrcPWqD6DV6', 'cashier', 'Cashier Husay Daw', 'active', '2025-09-01 23:01:27', 'uploads/avatars/avatar_68b33b667927f.jpg', '2025-08-14 13:36:07'),
(10, 2, 'cashier_gandara', '$2y$10$vs5O7hWLNbNEOJCEU/Et5uB7.AHrq.VeXGd2P7ZYdWU8vMCKy.6nq', 'cashier', 'Cashier 2nd Branch', 'active', '2025-08-27 22:39:26', 'uploads/avatars/avatar_68b31a1abda62.jpg', '2025-08-14 13:36:07'),
(11, 2, 'admin_gandara', '$2y$10$5PuaTNO.DlygUl9KDBogaeR5sVaEeTAi6P6xiBn0hFZ2aqs2m6/Gu', 'admin', 'Admin 2nd Branch', 'active', '2025-08-31 18:36:27', 'uploads/avatars/avatar_68b3c903b9cd7.jpg', '2025-08-14 13:36:07'),
(12, 1, 'admin_main', '$2y$10$Q..9xrdBpGrXd3p.j66mC.Sye20TmUmYRYy9Zuc.BPdhc1TgiO95C', 'admin', 'Ken Ken Pogi', 'active', '2025-09-01 23:01:46', 'uploads/avatars/avatar_68b3c90c783b1.jpg', '2025-08-14 13:36:07'),
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
  ADD KEY `idx_pawn_date` (`pawn_id`,`date_paid`),
  ADD KEY `idx_branch_date` (`branch_id`,`date_paid`),
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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=678;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=412;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `forfeitures`
--
ALTER TABLE `forfeitures`
  MODIFY `forfeiture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `interest_rates`
--
ALTER TABLE `interest_rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `partial_payments`
--
ALTER TABLE `partial_payments`
  MODIFY `pp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pawned_items`
--
ALTER TABLE `pawned_items`
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

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
  MODIFY `tubo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

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
