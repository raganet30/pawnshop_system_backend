-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 11:30 AM
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
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `branch_address`, `contact_no`) VALUES
(1, 'Main Branch', '123 Main St, City', '09171234567'),
(2, 'East Branch', '456 East Ave, City', '09179876543');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(3, 'Appliances'),
(2, 'Electronics'),
(1, 'Jewelry');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `claim_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `date_claimed` date DEFAULT NULL,
  `months` int(11) DEFAULT NULL,
  `interest` decimal(10,2) DEFAULT NULL,
  `multa` decimal(10,2) DEFAULT NULL,
  `total_paid` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `ledger_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_entry` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` enum('cash_in','cash_out') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ledger`
--

INSERT INTO `ledger` (`ledger_id`, `branch_id`, `date_entry`, `description`, `amount`, `type`) VALUES
(1, 1, '2025-08-01', 'Pawn - Gold Necklace', 5000.00, 'cash_out'),
(2, 1, '2025-08-10', 'Remata - Samsung TV', 9000.00, 'cash_in');

-- --------------------------------------------------------

--
-- Table structure for table `pawned_items`
--

CREATE TABLE `pawned_items` (
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `owner_name` varchar(100) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `unit_description` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `amount_pawned` decimal(10,2) DEFAULT NULL,
  `interest` decimal(10,2) DEFAULT 0.00,
  `date_pawned` date DEFAULT NULL,
  `status` enum('Pawned','Claimed','Forfeited','Sold') DEFAULT 'Pawned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pawned_items`
--

INSERT INTO `pawned_items` (`pawn_id`, `branch_id`, `owner_name`, `contact_no`, `address`, `unit_description`, `category_id`, `amount_pawned`, `interest`, `date_pawned`, `status`) VALUES
(1, 1, 'Juan Dela Cruz', '09170000001', '123 Main St', 'Gold Necklace', 1, 5000.00, 0.00, '2025-08-01', 'Pawned'),
(2, 1, 'Maria Clara', '09170000002', '456 East St', 'Samsung TV', 3, 8000.00, 0.00, '2025-07-15', 'Forfeited');

-- --------------------------------------------------------

--
-- Table structure for table `remata`
--

CREATE TABLE `remata` (
  `remata_id` int(11) NOT NULL,
  `pawn_id` int(11) NOT NULL,
  `date_forfeited` date DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `date_sold` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remata`
--

INSERT INTO `remata` (`remata_id`, `pawn_id`, `date_forfeited`, `selling_price`, `date_sold`) VALUES
(1, 2, '2025-08-10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','admin','cashier') DEFAULT 'cashier',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `branch_id`, `username`, `password`, `full_name`, `role`, `status`) VALUES
(1, NULL, 'admin', '$2y$10$QtFXatQ3XlT0iRW0TyLvVeYbB1CyiKiZ2HLsCBoVaWufT80JwZtnq', 'Admin User', 'super_admin', 'active'),
(2, 1, 'cashier1', '$2y$10$zVw/DoV37B9zXpuY1Qk1HOeovgZm.z5xM8gOw0gJmW6TvT4gn1yXO', 'Cashier Main', 'cashier', 'active'),
(4, NULL, 'admin2', '$2y$10$QtFXatQ3XlT0iRW0TyLvVeYbB1CyiKiZ2HLsCBoVaWufT80JwZtnq', 'Test Account', 'super_admin', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `pawn_id` (`pawn_id`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`ledger_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `pawned_items`
--
ALTER TABLE `pawned_items`
  ADD PRIMARY KEY (`pawn_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `remata`
--
ALTER TABLE `remata`
  ADD PRIMARY KEY (`remata_id`),
  ADD KEY `pawn_id` (`pawn_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `branch_id` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pawned_items`
--
ALTER TABLE `pawned_items`
  MODIFY `pawn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `remata`
--
ALTER TABLE `remata`
  MODIFY `remata_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`);

--
-- Constraints for table `ledger`
--
ALTER TABLE `ledger`
  ADD CONSTRAINT `ledger_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `pawned_items`
--
ALTER TABLE `pawned_items`
  ADD CONSTRAINT `pawned_items_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  ADD CONSTRAINT `pawned_items_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `remata`
--
ALTER TABLE `remata`
  ADD CONSTRAINT `remata_ibfk_1` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
