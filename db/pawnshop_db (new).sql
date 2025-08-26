-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 03:27 PM
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
(178, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #3 (Unit: realme 8i, Category: Gadgets, Amount: ₱2,000.00)', '2025-08-25 06:55:28'),
(179, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 06:56:33'),
(180, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 06:59:54'),
(181, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 07:08:49'),
(182, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 08:17:05'),
(183, 9, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 08:22:43'),
(184, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 08:29:22'),
(185, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 08:35:35'),
(186, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 08:37:14'),
(187, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 214, Amount: ₱2,000.00)', '2025-08-25 08:37:28'),
(188, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 08:38:08'),
(189, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #4 (Unit: iPhone 12 128GB, Category: Gadgets, Amount: ₱2,000.00)', '2025-08-25 08:42:47'),
(190, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 215, Unit: iPhone 12 128GB, Total Amount: ₱2,000.00', '2025-08-25 08:43:00'),
(191, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned item (Pawn ID: 215, Reason: overdue)', '2025-08-25 09:02:08'),
(192, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 215, Unit: iPhone 12 128GB, Total Amount: ₱2,000.00', '2025-08-25 09:08:43'),
(193, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 214, Amount: ₱2,000.00)', '2025-08-25 09:19:50'),
(194, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,120.00', '2025-08-25 09:21:21'),
(195, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 214, Amount: ₱2,000.00)', '2025-08-25 09:21:32'),
(196, 12, 1, 'edit_pawn', 'Edit pawn ID 214 details', '2025-08-25 09:21:52'),
(197, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 214, Unit: realme 8i, Total Amount: ₱2,000.00', '2025-08-25 09:22:02'),
(198, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #4 (Unit: realme 8i, Category: Computer, Amount: ₱500.00)', '2025-08-25 09:22:48'),
(199, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 216, Unit: realme 8i, Total Amount: ₱500.00', '2025-08-25 09:22:57'),
(200, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #23 (Unit: realme 8i, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 09:23:29'),
(201, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 217, Unit: realme 8i, Total Amount: ₱500.00', '2025-08-25 09:23:40'),
(202, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #4 (Unit: realme 8i, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 09:23:57'),
(203, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 218, Unit: realme 8i, Total Amount Paid: ₱530.00', '2025-08-25 09:24:11'),
(204, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 218, Amount: ₱500.00)', '2025-08-25 09:24:38'),
(205, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 215, Amount: ₱2,000.00, Item: iPhone 12 128GB)', '2025-08-25 09:27:12'),
(206, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 215, Unit: iPhone 12 128GB, Total Amount: ₱2,000.00', '2025-08-25 09:27:38'),
(207, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 214, Amount: ₱2,000.00, Item: realme 8i)', '2025-08-25 09:27:50'),
(208, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 216, Amount: ₱500.00, Item: realme 8i)', '2025-08-25 09:27:54'),
(209, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 217, Amount: ₱500.00, Item: realme 8i)', '2025-08-25 09:27:57'),
(210, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 215, Amount: ₱2,000.00, Item: iPhone 12 128GB)', '2025-08-25 09:28:01'),
(211, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,400.00', '2025-08-25 09:28:49'),
(212, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 215, Unit: iPhone 12 128GB, Total Amount Paid: ₱2,360.00', '2025-08-25 09:29:57'),
(213, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 216, Unit: realme 8i, Total Amount Paid: ₱590.00', '2025-08-25 09:30:11'),
(214, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 217, Unit: realme 8i, Total Amount Paid: ₱590.00', '2025-08-25 09:30:22'),
(215, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 218, Unit: realme 8i, Total Amount Paid: ₱530.00', '2025-08-25 09:30:31'),
(216, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 214, Amount: ₱2,000.00)', '2025-08-25 09:30:52'),
(217, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 215, Amount: ₱2,000.00)', '2025-08-25 09:30:55'),
(218, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 216, Amount: ₱500.00)', '2025-08-25 09:30:58'),
(219, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 217, Amount: ₱500.00)', '2025-08-25 09:31:01'),
(220, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 218, Amount: ₱500.00)', '2025-08-25 09:31:04'),
(221, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 214, Unit: realme 8i', '2025-08-25 09:31:30'),
(222, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 215, Unit: iPhone 12 128GB', '2025-08-25 09:31:34'),
(223, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 216, Unit: realme 8i', '2025-08-25 09:31:37'),
(224, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 217, Unit: realme 8i', '2025-08-25 09:31:41'),
(225, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 218, Unit: realme 8i', '2025-08-25 09:31:44'),
(226, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 214, Unit: realme 8i, Total Amount Paid: ₱2,360.00', '2025-08-25 09:42:41'),
(227, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 216, Unit: realme 8i, Total Amount Paid: ₱650.00', '2025-08-25 09:43:20'),
(228, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 215, Unit: iPhone 12 128GB, Total Amount Paid: ₱2,420.00', '2025-08-25 09:43:49'),
(229, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 217, Unit: realme 8i, Total Amount Paid: ₱590.00', '2025-08-25 09:45:21'),
(230, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 218, Unit: realme 8i, Total Amount Paid: ₱530.00', '2025-08-25 09:45:36'),
(231, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #6 (Unit: realme 8i, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 09:46:56'),
(232, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 219, Unit: realme 8i, Total Amount Paid: ₱530.00', '2025-08-25 09:47:26'),
(233, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #5 (Unit: realme 8i, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 09:47:45'),
(234, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #5 (Unit: realme 8i, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 09:47:53'),
(235, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #6 (Unit: realme 8i, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 09:48:00'),
(236, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 220, Unit: realme 8i, Total Amount Paid: ₱530.00', '2025-08-25 09:48:41'),
(237, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 221, Unit: realme 8i, Total Amount Paid: ₱550.00', '2025-08-25 09:49:16'),
(238, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 221, Amount: ₱500.00)', '2025-08-25 09:50:23'),
(239, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 220, Amount: ₱500.00)', '2025-08-25 09:50:38'),
(240, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 214, Amount: ₱2,000.00)', '2025-08-25 09:51:16'),
(241, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 216, Amount: ₱500.00)', '2025-08-25 09:51:19'),
(242, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 215, Amount: ₱2,000.00)', '2025-08-25 09:51:22'),
(243, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 217, Amount: ₱500.00)', '2025-08-25 09:51:26'),
(244, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 218, Amount: ₱500.00)', '2025-08-25 09:51:30'),
(245, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 219, Amount: ₱500.00)', '2025-08-25 09:51:33'),
(246, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #5 (Unit: realme 8i, Category: Gadgets, Amount: ₱200.00)', '2025-08-25 09:55:47'),
(247, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 223, Unit: realme 8i, Total Amount Paid: ₱212.00', '2025-08-25 09:56:06'),
(248, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 223, Amount: ₱200.00)', '2025-08-25 09:56:19'),
(249, 12, 1, 'edit_pawn', 'Edit pawn ID 223 details', '2025-08-25 09:56:57'),
(250, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 223, Unit: realme 8i, Total Amount: ₱500.00', '2025-08-25 09:57:05'),
(251, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 223, Amount: ₱500.00, Item: realme 8i)', '2025-08-25 09:57:18'),
(252, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 223, Unit: realme 8i, Total Amount Paid: ₱590.00', '2025-08-25 09:57:35'),
(253, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 223, Amount: ₱500.00)', '2025-08-25 09:58:34'),
(254, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 223, Unit: realme 8i, Total Amount: ₱500.00', '2025-08-25 09:58:53'),
(255, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 223, Amount: ₱500.00, Item: realme 8i)', '2025-08-25 09:59:12'),
(256, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 223, Unit: realme 8i, Total Amount Paid: ₱590.00', '2025-08-25 09:59:32'),
(257, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #6 (Unit: VIVO, Category: Gadgets, Amount: ₱300.00)', '2025-08-25 10:00:02'),
(258, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 224, Unit: VIVO, Total Amount Paid: ₱318.00', '2025-08-25 10:00:26'),
(259, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #6 (Unit: OPPO A5S, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-25 10:00:58'),
(260, 12, 1, 'Add Pawned Item', 'Added a new pawn item for PRINCESS MAE DELAROSA (Unit: SAMSUNG PHONE, Category: Gadgets, Amount: ₱100.00)', '2025-08-25 10:02:13'),
(261, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 225, Unit: OPPO A5S, Total Amount Paid: ₱1,060.00', '2025-08-25 10:02:45'),
(262, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 226, Unit: SAMSUNG PHONE, Total Amount Paid: ₱106.00', '2025-08-25 10:02:58'),
(263, 12, 1, 'Add Pawned Item', 'Added a new pawn item for PRINCESS MAE DELAROSA (Unit: redme, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 10:03:48'),
(264, 12, 1, 'Add Pawned Item', 'Added a new pawn item for PRINCESS MAE DELAROSA (Unit: XRM, Category: Vehicle, Amount: ₱500.00)', '2025-08-25 10:04:40'),
(265, 12, 1, 'edit_pawn', 'Edit pawn ID 227 details', '2025-08-25 10:05:31'),
(266, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 227, Unit: redme, Total Amount: ₱500.00', '2025-08-25 10:05:42'),
(267, 12, 1, 'edit_pawn', 'Edit pawn ID 228 details', '2025-08-25 10:06:04'),
(268, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 228, Unit: XRM, Total Amount: ₱500.00', '2025-08-25 10:06:10'),
(269, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 223, Amount: ₱500.00)', '2025-08-25 10:07:33'),
(270, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 224, Amount: ₱300.00)', '2025-08-25 10:07:37'),
(271, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 225, Amount: ₱1,000.00)', '2025-08-25 10:07:41'),
(272, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 226, Amount: ₱100.00)', '2025-08-25 10:07:44'),
(273, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 227, Amount: ₱500.00, Item: redme)', '2025-08-25 10:07:48'),
(274, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 228, Amount: ₱500.00, Item: XRM)', '2025-08-25 10:07:51'),
(275, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 227, Unit: redme', '2025-08-25 10:08:48'),
(276, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 228, Unit: XRM', '2025-08-25 10:08:51'),
(277, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 223, Unit: realme 8i', '2025-08-25 10:08:54'),
(278, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 224, Unit: VIVO', '2025-08-25 10:08:57'),
(279, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 225, Unit: OPPO A5S', '2025-08-25 10:09:01'),
(280, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 226, Unit: SAMSUNG PHONE', '2025-08-25 10:09:05'),
(281, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 224, Unit: VIVO', '2025-08-25 10:28:10'),
(282, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 227, Unit: redme', '2025-08-25 10:30:08'),
(283, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 228, Unit: XRM', '2025-08-25 10:30:11'),
(284, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 223, Unit: realme 8i', '2025-08-25 10:30:14'),
(285, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 224, Unit: VIVO', '2025-08-25 10:30:18'),
(286, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 225, Unit: OPPO A5S', '2025-08-25 10:30:20'),
(287, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 226, Unit: SAMSUNG PHONE', '2025-08-25 10:30:24'),
(288, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #26 (Unit: SAMSUNG, Category: Gadgets, Amount: ₱500.00)', '2025-08-25 10:31:17'),
(289, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #6 (Unit: VIVO, Category: Gadgets, Amount: ₱200.00)', '2025-08-25 10:31:33'),
(290, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #2 (Unit: OPPO, Category: Gadgets, Amount: ₱100.00)', '2025-08-25 10:31:55'),
(291, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 229, Unit: SAMSUNG', '2025-08-25 10:36:29'),
(292, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 230, Unit: VIVO', '2025-08-25 10:36:32'),
(293, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 231, Unit: OPPO', '2025-08-25 10:36:35'),
(294, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 229, Unit: SAMSUNG', '2025-08-25 10:38:12'),
(295, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 230, Unit: VIVO', '2025-08-25 10:38:15'),
(296, 12, 1, 'Deleted Pawned Item', 'Deleted pawn ID: 231, Unit: OPPO', '2025-08-25 10:38:18'),
(297, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 229, Unit: SAMSUNG, Total Amount Paid: ₱530.00', '2025-08-25 10:40:30'),
(298, 12, 1, 'Revert Claimed Item', 'Reverted a claimed iteom to pawn item.(PawnID: 229, Amount: ₱500.00)', '2025-08-25 10:42:02'),
(299, 12, 1, 'Claim Pawned Item', 'Claimed pawn ID: 229, Unit: SAMSUNG, Total Amount Paid: ₱530.00', '2025-08-25 10:42:23'),
(300, 12, 1, 'edit_pawn', 'Edit pawn ID 230 details', '2025-08-25 10:42:41'),
(301, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 230, Unit: VIVO, Total Amount: ₱200.00', '2025-08-25 10:42:48'),
(302, 12, 1, 'Revert Forfeited Item', 'Reverted a forfeited item to pawned status. (PawnID: 230, Amount: ₱200.00, Item: VIVO)', '2025-08-25 10:43:00'),
(303, 12, 1, 'Forfeit Pawned Item', 'Forfeited pawn ID: 230, Unit: VIVO, Total Amount: ₱200.00', '2025-08-25 10:43:13'),
(304, 11, 2, 'Add Pawned Item', 'Added a new pawn item for RODULFO GUTIEREZ (Unit: XIAOMI, Category: Gadgets, Amount: ₱1,500.00)', '2025-08-25 10:51:53'),
(305, 11, 2, 'Add Pawned Item', 'Added a new pawn item for ROBERTO DEMAKITA (Unit: OPPO, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-25 10:52:30'),
(306, 11, 2, 'Claim Pawned Item', 'Claimed pawn ID: 232, Unit: XIAOMI, Total Amount Paid: ₱1,590.00', '2025-08-25 10:52:46'),
(307, 11, 2, 'Add Pawned Item', 'Added a new pawn item for MYRNA LIM (Unit: SAMSUNG, Category: Gadgets, Amount: ₱1,000.00)', '2025-08-25 10:53:35'),
(308, 11, 2, 'edit_pawn', 'Edit pawn ID 233 details', '2025-08-25 10:53:45'),
(309, 11, 2, 'Forfeit Pawned Item', 'Forfeited pawn ID: 233, Unit: OPPO, Total Amount: ₱1,000.00', '2025-08-25 10:53:58'),
(310, 12, 1, 'Add Pawned Item', 'Added a new pawn item for Customer #19 (Unit: SAMSUNG PHONE, Category: Gadgets, Amount: ₱200.00)', '2025-08-25 12:33:56');

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
(1, 'Calbayog Branch - Main', 'Navarro St., Calbayog City, Samar', '09171234567', 'active', 0.0600, 20000.00, '2025-08-14 12:57:02'),
(2, 'Gandara Branch', 'Gandara Samar', '09181234567', 'active', 0.0600, 10000.00, '2025-08-14 12:57:02');

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
(195, 1, 'pawned_items', 246, 200.00, 'pawn', 'out', 'Pawn Add (ID #246)', 'realme 8i', 12, '2025-08-25 09:55:47'),
(196, 1, 'claims', 124, 212.00, 'claim', 'in', 'Claim (ID #124)', 'Pawn ID #223 claimed with interest + penalty (if any)', 12, '2025-08-25 09:56:06'),
(198, 1, 'pawned_items', 223, 300.00, 'pawn_edit', 'out', 'Pawn Edit (ID #223)', 'Pawn amount adjusted from ₱200.00 to ₱500.00', 12, '2025-08-25 09:56:40'),
(201, 1, 'claims', 125, 590.00, 'claim', 'in', 'Claim (ID #125)', 'Pawn ID #223 claimed with interest + penalty (if any)', 12, '2025-08-25 09:57:35'),
(204, 1, 'forfeitures', 223, 500.00, 'forfeit', 'out', 'Revert Forfeit (Pawn ID #223)', 'Reverted forfeited pawned item. COH deducted ₱500.00', 12, '2025-08-25 09:59:12'),
(205, 1, 'claims', 126, 590.00, 'claim', 'in', 'Claim (ID #126)', 'Pawn ID #223 claimed with interest + penalty (if any)', 12, '2025-08-25 09:59:32'),
(206, 1, 'pawned_items', 257, 300.00, 'pawn', 'out', 'Pawn Add (ID #257)', 'VIVO', 12, '2025-08-25 10:00:02'),
(207, 1, 'claims', 127, 318.00, 'claim', 'in', 'Claim (ID #127)', 'Pawn ID #224 claimed with interest + penalty (if any)', 12, '2025-08-25 10:00:26'),
(208, 1, 'pawned_items', 259, 1000.00, 'pawn', 'out', 'Pawn Add (ID #259)', 'OPPO A5S', 12, '2025-08-25 10:00:58'),
(209, 1, 'pawned_items', 260, 100.00, 'pawn', 'out', 'Pawn Add (ID #260)', 'SAMSUNG PHONE', 12, '2025-08-25 10:02:13'),
(210, 1, 'claims', 128, 1060.00, 'claim', 'in', 'Claim (ID #128)', 'Pawn ID #225 claimed with interest + penalty (if any)', 12, '2025-08-25 10:02:45'),
(211, 1, 'claims', 129, 106.00, 'claim', 'in', 'Claim (ID #129)', 'Pawn ID #226 claimed with interest + penalty (if any)', 12, '2025-08-25 10:02:58'),
(212, 1, 'pawned_items', 263, 500.00, 'pawn', 'out', 'Pawn Add (ID #263)', 'redme', 12, '2025-08-25 10:03:48'),
(213, 1, 'pawned_items', 264, 500.00, 'pawn', 'out', 'Pawn Add (ID #264)', 'XRM', 12, '2025-08-25 10:04:40'),
(216, 1, 'claims', 223, 590.00, 'claim', 'out', 'Revert Claim (ID #223)', 'Reverted claimed pawned item. Total amount ₱590.00', 12, '2025-08-25 10:07:33'),
(217, 1, 'claims', 224, 318.00, 'claim', 'out', 'Revert Claim (ID #224)', 'Reverted claimed pawned item. Total amount ₱318.00', 12, '2025-08-25 10:07:37'),
(218, 1, 'claims', 225, 1060.00, 'claim', 'out', 'Revert Claim (ID #225)', 'Reverted claimed pawned item. Total amount ₱1,060.00', 12, '2025-08-25 10:07:41'),
(219, 1, 'claims', 226, 106.00, 'claim', 'out', 'Revert Claim (ID #226)', 'Reverted claimed pawned item. Total amount ₱106.00', 12, '2025-08-25 10:07:44'),
(220, 1, 'forfeitures', 227, 500.00, 'forfeit', 'out', 'Revert Forfeit (Pawn ID #227)', 'Reverted forfeited pawned item. COH deducted ₱500.00', 12, '2025-08-25 10:07:48'),
(221, 1, 'forfeitures', 228, 500.00, 'forfeit', 'out', 'Revert Forfeit (Pawn ID #228)', 'Reverted forfeited pawned item. COH deducted ₱500.00', 12, '2025-08-25 10:07:51'),
(222, 1, 'pawned_items', 227, 500.00, 'delete', 'in', 'Pawn Deleted (ID #227)', 'Pawn ID #227 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:08:48'),
(223, 1, 'pawned_items', 228, 500.00, 'delete', 'in', 'Pawn Deleted (ID #228)', 'Pawn ID #228 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:08:51'),
(224, 1, 'pawned_items', 223, 500.00, 'delete', 'in', 'Pawn Deleted (ID #223)', 'Pawn ID #223 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:08:54'),
(225, 1, 'pawned_items', 224, 300.00, 'delete', 'in', 'Pawn Deleted (ID #224)', 'Pawn ID #224 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:08:57'),
(226, 1, 'pawned_items', 225, 1000.00, 'delete', 'in', 'Pawn Deleted (ID #225)', 'Pawn ID #225 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:09:01'),
(227, 1, 'pawned_items', 226, 100.00, 'delete', 'in', 'Pawn Deleted (ID #226)', 'Pawn ID #226 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:09:05'),
(228, 1, 'claims', 224, 300.00, 'restore', 'in', 'Restore Pawn (ID #224)', 'Deleted Pawn ID(s) #224 restored.', 12, '2025-08-25 10:27:11'),
(229, 1, 'pawned_items', 224, 300.00, 'delete', 'in', 'Pawn Deleted (ID #224)', 'Pawn ID #224 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:28:10'),
(230, 1, 'claims', 224, 300.00, 'restore', 'in', 'Restore Pawn (ID #224)', 'Deleted Pawn ID(s) #224 restored.', 12, '2025-08-25 10:28:27'),
(231, 1, 'claims', 225, 1000.00, 'restore', 'in', 'Restore Pawn (ID #225)', 'Deleted Pawn ID(s) #225 restored.', 12, '2025-08-25 10:28:40'),
(232, 1, 'claims', 226, 100.00, 'restore', 'in', 'Restore Pawn (ID #226)', 'Deleted Pawn ID(s) #226 restored.', 12, '2025-08-25 10:28:53'),
(233, 1, 'claims', 223, 500.00, 'restore', 'in', 'Restore Pawn (ID #223)', 'Deleted Pawn ID(s) #223 restored.', 12, '2025-08-25 10:28:53'),
(234, 1, 'claims', 227, 500.00, 'restore', 'in', 'Restore Pawn (ID #227)', 'Deleted Pawn ID(s) #227 restored.', 12, '2025-08-25 10:28:53'),
(235, 1, 'claims', 228, 500.00, 'restore', 'in', 'Restore Pawn (ID #228)', 'Deleted Pawn ID(s) #228 restored.', 12, '2025-08-25 10:28:53'),
(236, 1, 'pawned_items', 227, 500.00, 'delete', 'in', 'Pawn Deleted (ID #227)', 'Pawn ID #227 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:30:08'),
(237, 1, 'pawned_items', 228, 500.00, 'delete', 'in', 'Pawn Deleted (ID #228)', 'Pawn ID #228 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:30:11'),
(238, 1, 'pawned_items', 223, 500.00, 'delete', 'in', 'Pawn Deleted (ID #223)', 'Pawn ID #223 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:30:14'),
(239, 1, 'pawned_items', 224, 300.00, 'delete', 'in', 'Pawn Deleted (ID #224)', 'Pawn ID #224 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:30:18'),
(240, 1, 'pawned_items', 225, 1000.00, 'delete', 'in', 'Pawn Deleted (ID #225)', 'Pawn ID #225 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:30:20'),
(241, 1, 'pawned_items', 226, 100.00, 'delete', 'in', 'Pawn Deleted (ID #226)', 'Pawn ID #226 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:30:24'),
(242, 1, 'pawned_items', 288, 500.00, 'pawn', 'out', 'Pawn Add (ID #288)', 'SAMSUNG', 12, '2025-08-25 10:31:17'),
(243, 1, 'pawned_items', 289, 200.00, 'pawn', 'out', 'Pawn Add (ID #289)', 'VIVO', 12, '2025-08-25 10:31:33'),
(244, 1, 'pawned_items', 290, 100.00, 'pawn', 'out', 'Pawn Add (ID #290)', 'OPPO', 12, '2025-08-25 10:31:55'),
(245, 1, 'pawned_items', 229, 500.00, 'delete', 'in', 'Pawn Deleted (ID #229)', 'Pawn ID #229 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:36:29'),
(246, 1, 'pawned_items', 230, 200.00, 'delete', 'in', 'Pawn Deleted (ID #230)', 'Pawn ID #230 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:36:32'),
(247, 1, 'pawned_items', 231, 100.00, 'delete', 'in', 'Pawn Deleted (ID #231)', 'Pawn ID #231 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:36:35'),
(249, 1, 'claims', 230, 200.00, 'restore', 'in', 'Restore Pawn (ID #230)', 'Deleted Pawn ID(s) #230 restored.', 12, '2025-08-25 10:36:43'),
(250, 1, 'claims', 231, 100.00, 'restore', 'in', 'Restore Pawn (ID #231)', 'Deleted Pawn ID(s) #231 restored.', 12, '2025-08-25 10:36:43'),
(251, 1, 'pawned_items', 229, 500.00, 'delete', 'in', 'Pawn Deleted (ID #229)', 'Pawn ID #229 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:38:12'),
(252, 1, 'pawned_items', 230, 200.00, 'delete', 'in', 'Pawn Deleted (ID #230)', 'Pawn ID #230 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:38:15'),
(253, 1, 'pawned_items', 231, 100.00, 'delete', 'in', 'Pawn Deleted (ID #231)', 'Pawn ID #231 deleted - amount refunded to cash on hand', 12, '2025-08-25 10:38:18'),
(255, 1, 'claims', 230, 200.00, 'restore', 'in', 'Restore Pawn (ID #230)', 'Deleted Pawn ID(s) #230 restored.', 12, '2025-08-25 10:38:29'),
(256, 1, 'claims', 231, 100.00, 'restore', 'in', 'Restore Pawn (ID #231)', 'Deleted Pawn ID(s) #231 restored.', 12, '2025-08-25 10:38:29'),
(257, 1, 'claims', 130, 530.00, 'claim', 'in', 'Claim (ID #130)', 'Pawn ID #229 claimed with interest + penalty (if any)', 12, '2025-08-25 10:40:30'),
(258, 1, 'claims', 229, 530.00, 'claim', 'out', 'Revert Claim (ID #229)', 'Reverted claimed pawned item. Total amount ₱530.00', 12, '2025-08-25 10:42:02'),
(259, 1, 'claims', 131, 530.00, 'claim', 'in', 'Claim (ID #131)', 'Pawn ID #229 claimed with interest + penalty (if any)', 12, '2025-08-25 10:42:23'),
(261, 1, 'forfeitures', 230, 200.00, 'forfeit', 'out', 'Revert Forfeit (Pawn ID #230)', 'Reverted forfeited pawned item. COH deducted ₱200.00', 12, '2025-08-25 10:43:00'),
(262, 1, 'forfeitures', 230, 200.00, 'forfeit', 'in', 'Pawn Forfeite', 'Pawn ID #230 forfeited - amount added to COH', 12, '2025-08-25 10:43:13'),
(263, 2, 'pawned_items', 304, 1500.00, 'pawn', 'out', 'Pawn Add (ID #304)', 'XIAOMI', 11, '2025-08-25 10:51:53'),
(264, 2, 'pawned_items', 305, 1000.00, 'pawn', 'out', 'Pawn Add (ID #305)', 'OPPO', 11, '2025-08-25 10:52:30'),
(265, 2, 'claims', 132, 1590.00, 'claim', 'in', 'Claim (ID #132)', 'Pawn ID #232 claimed with interest + penalty (if any)', 11, '2025-08-25 10:52:46'),
(266, 2, 'pawned_items', 307, 1000.00, 'pawn', 'out', 'Pawn Add (ID #307)', 'SAMSUNG', 11, '2025-08-25 10:53:35'),
(267, 2, 'forfeitures', 233, 1000.00, 'forfeit', 'in', 'Pawn Forfeite', 'Pawn ID #233 forfeited - amount added to COH', 11, '2025-08-25 10:53:58'),
(268, 1, 'pawned_items', 310, 200.00, 'pawn', 'out', 'Pawn Add (ID #310)', 'SAMSUNG PHONE', 12, '2025-08-25 12:33:56');

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
(131, 229, 1, '2025-08-25', 1, 0.06, 30.00, 500.00, 0.00, 530.00, 12, '', 'uploads/claimants/claimant_229_1756118543.png', '2025-08-25 10:42:23'),
(132, 232, 2, '2025-08-25', 1, 0.06, 90.00, 1500.00, 0.00, 1590.00, 11, '', 'uploads/claimants/claimant_232_1756119166.png', '2025-08-25 10:52:46');

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
(29, 'MYRNA LIM', '09123456789', 'IPAO', NULL, '2025-08-25 10:53:35');

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
(32, 230, 1, '2025-08-25', 'overdue', 'test only', '2025-08-25 10:43:13'),
(33, 233, 2, '2025-08-25', 'overdue', 'test only', '2025-08-25 10:53:58');

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
(229, 1, 26, 'SAMSUNG', 'Gadgets', 500.00, 6.00, '2025-08-25', 'claimed', 'test only', 12, NULL, '2025-08-25 10:31:17', '2025-08-25 10:42:23', 0),
(230, 1, 6, 'VIVO', 'Gadgets', 200.00, 6.00, '2025-06-25', 'forfeited', 'test only', 12, NULL, '2025-08-25 10:31:33', '2025-08-25 10:43:13', 0),
(231, 1, 2, 'OPPO', 'Gadgets', 100.00, 6.00, '2025-08-25', 'pawned', 'TEST', 12, NULL, '2025-08-25 10:31:55', '2025-08-25 10:38:29', 0),
(232, 2, 27, 'XIAOMI', 'Gadgets', 1500.00, 6.00, '2025-08-25', 'claimed', 'test only', 11, NULL, '2025-08-25 10:51:53', '2025-08-25 10:52:46', 0),
(233, 2, 28, 'OPPO', 'Gadgets', 1000.00, 6.00, '2025-06-25', 'forfeited', 'test only', 11, NULL, '2025-08-25 10:52:30', '2025-08-25 10:53:58', 0),
(234, 2, 29, 'SAMSUNG', 'Gadgets', 1000.00, 6.00, '2025-08-25', 'pawned', 'test only', 11, NULL, '2025-08-25 10:53:35', NULL, 0),
(235, 1, 19, 'SAMSUNG PHONE', 'Gadgets', 200.00, 6.00, '2025-08-25', 'pawned', 'TEST', 12, NULL, '2025-08-25 12:33:56', NULL, 0);

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

--
-- Dumping data for table `tubo_payments`
--

INSERT INTO `tubo_payments` (`tubo_id`, `pawn_id`, `branch_id`, `date_paid`, `months_covered`, `interest_rate`, `interest_amount`, `cashier_id`, `txn_code`, `payment_method`, `notes`, `created_at`) VALUES
(82, 229, 1, '2025-08-25', 1, 0.06, 30.00, 12, NULL, 'cash', 'Claim payment', '2025-08-25 10:42:23'),
(83, 232, 2, '2025-08-25', 1, 0.06, 90.00, 11, NULL, 'cash', 'Claim payment', '2025-08-25 10:52:46');

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_ledger`
--
ALTER TABLE `cash_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=269;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `forfeitures`
--
ALTER TABLE `forfeitures`
  MODIFY `forfeiture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `interest_rates`
--
ALTER TABLE `interest_rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pawned_items`
--
ALTER TABLE `pawned_items`
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `remata_sales`
--
ALTER TABLE `remata_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tubo_payments`
--
ALTER TABLE `tubo_payments`
  MODIFY `tubo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

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
