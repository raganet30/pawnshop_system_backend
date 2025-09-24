

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1036 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_logs` VALUES ('993','12','1','Login','Logged In','2025-09-18 08:42:19');
INSERT INTO `audit_logs` VALUES ('994','12','1','Cash On Hand Adjustment','COH Add: +₱10,000.00 (Old COH: ₱0.00, New COH: ₱10,000.00)','2025-09-18 08:42:27');
INSERT INTO `audit_logs` VALUES ('995','12','1','Add Pawned Item','Added a new pawn item for Customer #52 (Unit: iphone 12, Category: Cellphone, Amount: ₱5,000.00)','2025-09-18 08:42:55');
INSERT INTO `audit_logs` VALUES ('996','12','1','Edit Pawn Item','Edit pawn item: iphone 12 details','2025-09-18 08:44:15');
INSERT INTO `audit_logs` VALUES ('997','12','1','Partial Payment + Tubo','Partial payment recorded. Pawn Item: iphone 12, Partial ₱100.00, Interest ₱600.00, New Principal ₱4,900.00, New Due Date 2025-11-05','2025-09-18 09:04:55');
INSERT INTO `audit_logs` VALUES ('998','12','1','Partial Payment','Partial payment recorded. Pawn Item: iphone 12, Partial ₱500.00, New Principal ₱4,400.00','2025-09-18 09:12:42');
INSERT INTO `audit_logs` VALUES ('999','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: iphone 12, Interest Amount: ₱264.00, Period: 2025-11-05 to 2025-12-05, Months Covered: 1, New Due Date: 2025-12-05','2025-09-18 09:13:41');
INSERT INTO `audit_logs` VALUES ('1000','12','1','Add Pawned Item','Added a new pawn item for Customer #51 (Unit: Honda Beat Fi v2, Category: Motorcycle, Amount: ₱5,000.00)','2025-09-18 09:32:25');
INSERT INTO `audit_logs` VALUES ('1001','12','1','Claim Pawned Item','Claimed pawn item: Honda Beat Fi v2, Category: Motorcycle, Total Amount Paid: ₱5,800.00','2025-09-18 09:40:11');
INSERT INTO `audit_logs` VALUES ('1002','12','1','Partial Payment','Partial payment recorded. Pawn Item: iphone 12, Partial ₱500.00, New Principal ₱3,900.00','2025-09-18 11:04:15');
INSERT INTO `audit_logs` VALUES ('1003','12','1','Partial Payment','Partial payment recorded. Pawn Item: iphone 12, Partial ₱1,000.00, New Principal ₱2,900.00','2025-09-18 11:06:59');
INSERT INTO `audit_logs` VALUES ('1004','12','1','Partial Payment','Partial payment recorded. Pawn Item: iphone 12, Partial ₱500.00, New Principal ₱2,400.00','2025-09-18 11:18:27');
INSERT INTO `audit_logs` VALUES ('1005','12','1','Claim Pawned Item','Claimed pawn item: iphone 12, Category: Cellphone, Total Amount Paid: ₱2,400.00','2025-09-18 11:19:21');
INSERT INTO `audit_logs` VALUES ('1006','12','1','Add Pawned Item','Added a new pawn item for Customer #51 (Unit: test, Category: Cellphone, Amount: ₱5,000.00)','2025-09-18 11:20:15');
INSERT INTO `audit_logs` VALUES ('1007','12','1','Partial Payment + Tubo','Partial payment recorded. Pawn Item: test, Partial ₱1,000.00, Interest ₱600.00, New Principal ₱4,000.00, New Due Date 2025-11-05','2025-09-18 11:24:43');
INSERT INTO `audit_logs` VALUES ('1008','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: test, Interest Amount: ₱240.00, Period: 2025-11-05 to 2025-12-05, Months Covered: 1, New Due Date: 2025-12-05','2025-09-18 11:25:03');
INSERT INTO `audit_logs` VALUES ('1009','12','1','Add Pawned Item','Added a new pawn item for Customer #52 (Unit: oppo a5s, Category: Cellphone, Amount: ₱3,000.00)','2025-09-18 11:49:06');
INSERT INTO `audit_logs` VALUES ('1010','12','1','Partial Payment + Tubo','Partial payment recorded. Pawn Item: oppo a5s, Partial ₱1,500.00, Interest ₱360.00, New Principal ₱1,500.00, New Due Date 2025-11-05','2025-09-18 11:49:49');
INSERT INTO `audit_logs` VALUES ('1011','12','1','Login','Logged In','2025-09-19 09:52:51');
INSERT INTO `audit_logs` VALUES ('1012','12','1','Add Pawned Item','Added a new pawn item for Customer #51 (Unit: oppo a5s, Category: Cellphone, Amount: ₱3,000.00)','2025-09-19 09:53:13');
INSERT INTO `audit_logs` VALUES ('1013','12','1','Add Pawned Item','Added a new pawn item for Customer #51 (Unit: oppo a5s, Category: Cellphone, Amount: ₱3,000.00)','2025-09-19 10:00:33');
INSERT INTO `audit_logs` VALUES ('1014','12','1','Logout','Logged Out','2025-09-20 09:01:37');
INSERT INTO `audit_logs` VALUES ('1015','12','1','Login','Logged In','2025-09-20 09:01:40');
INSERT INTO `audit_logs` VALUES ('1016','12','1','Add Pawned Item','Added a new pawn item for Customer #52 (Unit: oppo a5s, Category: Cellphone, Amount: ₱1,000.00)','2025-09-20 09:02:04');
INSERT INTO `audit_logs` VALUES ('1017','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: oppo a5s, Interest Amount: ₱60.00, Period: 2025-09-20 to 2025-10-20, Months Covered: 1, New Due Date: 2025-11-20','2025-09-20 09:02:29');
INSERT INTO `audit_logs` VALUES ('1018','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: oppo a5s, Interest Amount: ₱120.00, Period: 2025-11-20 to 2026-01-20, Months Covered: 2, New Due Date: 2026-01-20','2025-09-20 09:06:08');
INSERT INTO `audit_logs` VALUES ('1019','12','1','Claim Pawned Item','Claimed pawn item: oppo a5s, Category: Cellphone, Total Amount Paid: ₱1,000.00','2025-09-20 09:13:06');
INSERT INTO `audit_logs` VALUES ('1020','12','1','Login','Logged In','2025-09-20 11:59:43');
INSERT INTO `audit_logs` VALUES ('1021','12','1','Logout','Logged Out','2025-09-20 11:59:54');
INSERT INTO `audit_logs` VALUES ('1022','9','1','Login','Logged In','2025-09-20 12:01:57');
INSERT INTO `audit_logs` VALUES ('1023','9','1','Logout','Logged Out','2025-09-20 12:02:09');
INSERT INTO `audit_logs` VALUES ('1024','12','1','Login','Logged In','2025-09-20 12:02:13');
INSERT INTO `audit_logs` VALUES ('1025','12','1','Logout','Logged Out','2025-09-20 13:07:46');
INSERT INTO `audit_logs` VALUES ('1026','9','1','Login','Logged In','2025-09-20 13:07:49');
INSERT INTO `audit_logs` VALUES ('1027','9','1','Logout','Logged Out','2025-09-20 13:07:57');
INSERT INTO `audit_logs` VALUES ('1028','12','1','Login','Logged In','2025-09-20 13:08:00');
INSERT INTO `audit_logs` VALUES ('1029','12','1','Add Pawned Item','Added a new pawn item for Customer #53 (Unit: test phone, Category: Cellphone, Amount: ₱1,500.00)','2025-09-20 13:55:15');
INSERT INTO `audit_logs` VALUES ('1030','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: test phone, Interest Amount: ₱90.00, Period: 2025-09-20 to 2025-10-20, Months Covered: 1, New Due Date: 2025-11-20','2025-09-20 13:55:39');
INSERT INTO `audit_logs` VALUES ('1031','12','1','Partial Payment + Tubo','Partial payment recorded. Pawn Item: oppo a5s, Partial ₱1,000.00, Interest ₱180.00, New Principal ₱2,000.00, New Due Date 2025-11-19','2025-09-20 15:45:15');
INSERT INTO `audit_logs` VALUES ('1032','12','1','Logout','Logged Out','2025-09-20 15:52:57');


CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(100) NOT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  `branch_phone` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `interest_rate` decimal(5,2) DEFAULT 0.06,
  `custom_interest_rate1` decimal(5,2) DEFAULT NULL COMMENT 'custom interest for vehicle (8% interest)',
  `custom_interest_rate2` decimal(5,2) DEFAULT NULL,
  `custom_interest_rate3` decimal(5,2) DEFAULT NULL,
  `cash_on_hand` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `branches` VALUES ('1','Calbayog Branch - Main','Navarro St. , Calbayog City, Samar','09123456789','active','0.06','0.08','','','1314.00','2025-08-14 20:57:02');
INSERT INTO `branches` VALUES ('2','Gandara Branch','Gandara, Samar','09181234567','active','0.06','0.08','','','0.00','2025-08-14 20:57:02');


CREATE TABLE `cash_ledger` (
  `ledger_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL,
  `ref_table` varchar(50) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `txn_type` varchar(50) NOT NULL,
  `direction` enum('in','out') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ledger_id`),
  KEY `branch_id` (`branch_id`),
  KEY `ref_table` (`ref_table`,`ref_id`),
  CONSTRAINT `cash_ledger_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=599 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cash_ledger` VALUES ('574','1','branches','1','10000.00','coh_adjustment','in','Add COH Adjustment','set','12','2025-09-18 08:42:27');
INSERT INTO `cash_ledger` VALUES ('575','1','pawned_items','347','5000.00','pawn','out','Pawn Add (ID #347)','iphone 12','12','2025-09-18 08:42:55');
INSERT INTO `cash_ledger` VALUES ('576','1','partial_payments','116','700.00','partial_payment','in','Partial + Tubo (Pawn ID #347)','Partial ₱100.00 | Interest ₱600.00','12','2025-09-18 09:04:55');
INSERT INTO `cash_ledger` VALUES ('577','1','partial_payments','117','500.00','partial_payment','in','Partial Payment (Pawn ID #347)','Partial ₱500.00','12','2025-09-18 09:12:42');
INSERT INTO `cash_ledger` VALUES ('578','1','tubo_payments','101','264.00','tubo_payment','in','Tubo Payment (Pawn ID #347)','Tubo payment of ₱264.00 for Pawn ID #347. Period: 2025-11-05 to 2025-12-05','12','2025-09-18 09:13:41');
INSERT INTO `cash_ledger` VALUES ('579','1','pawned_items','348','5000.00','pawn','out','Pawn Add (ID #348)','Honda Beat Fi v2','12','2025-09-18 09:32:25');
INSERT INTO `cash_ledger` VALUES ('580','1','claims','348','5800.00','claim','in','Claim (ID #175)','Pawn ID #348 claimed with interest + penalty (if any)','12','2025-09-18 09:40:11');
INSERT INTO `cash_ledger` VALUES ('581','1','partial_payments','118','500.00','partial_payment','in','Partial Payment (Pawn ID #347)','Partial ₱500.00','12','2025-09-18 11:04:15');
INSERT INTO `cash_ledger` VALUES ('582','1','partial_payments','119','1000.00','partial_payment','in','Partial Payment (Pawn ID #347)','Partial ₱1,000.00','12','2025-09-18 11:06:59');
INSERT INTO `cash_ledger` VALUES ('583','1','partial_payments','120','500.00','partial_payment','in','Partial Payment (Pawn ID #347)','Partial ₱500.00','12','2025-09-18 11:18:27');
INSERT INTO `cash_ledger` VALUES ('584','1','claims','347','2400.00','claim','in','Claim (ID #176)','Pawn ID #347 claimed with interest + penalty (if any)','12','2025-09-18 11:19:21');
INSERT INTO `cash_ledger` VALUES ('585','1','pawned_items','349','5000.00','pawn','out','Pawn Add (ID #349)','test','12','2025-09-18 11:20:15');
INSERT INTO `cash_ledger` VALUES ('586','1','partial_payments','121','1600.00','partial_payment','in','Partial + Tubo (Pawn ID #349)','Partial ₱1,000.00 | Interest ₱600.00','12','2025-09-18 11:24:43');
INSERT INTO `cash_ledger` VALUES ('587','1','tubo_payments','103','240.00','tubo_payment','in','Tubo Payment (Pawn ID #349)','Tubo payment of ₱240.00 for Pawn ID #349. Period: 2025-11-05 to 2025-12-05','12','2025-09-18 11:25:03');
INSERT INTO `cash_ledger` VALUES ('588','1','pawned_items','350','3000.00','pawn','out','Pawn Add (ID #350)','oppo a5s','12','2025-09-18 11:49:06');
INSERT INTO `cash_ledger` VALUES ('589','1','partial_payments','122','1860.00','partial_payment','in','Partial + Tubo (Pawn ID #350)','Partial ₱1,500.00 | Interest ₱360.00','12','2025-09-18 11:49:49');
INSERT INTO `cash_ledger` VALUES ('590','1','pawned_items','351','3000.00','pawn','out','Pawn Add (ID #351)','oppo a5s','12','2025-09-19 09:53:13');
INSERT INTO `cash_ledger` VALUES ('591','1','pawned_items','352','3000.00','pawn','out','Pawn Add (ID #352)','oppo a5s','12','2025-09-19 10:00:33');
INSERT INTO `cash_ledger` VALUES ('592','1','pawned_items','353','1000.00','pawn','out','Pawn Add (ID #353)','oppo a5s','12','2025-09-20 09:02:04');
INSERT INTO `cash_ledger` VALUES ('593','1','tubo_payments','105','60.00','tubo_payment','in','Tubo Payment (Pawn ID #353)','Tubo payment of ₱60.00 for Pawn ID #353. Period: 2025-09-20 to 2025-10-20','12','2025-09-20 09:02:29');
INSERT INTO `cash_ledger` VALUES ('594','1','tubo_payments','106','120.00','tubo_payment','in','Tubo Payment (Pawn ID #353)','Tubo payment of ₱120.00 for Pawn ID #353. Period: 2025-11-20 to 2026-01-20','12','2025-09-20 09:06:08');
INSERT INTO `cash_ledger` VALUES ('595','1','claims','353','1000.00','claim','in','Claim (ID #177)','Pawn ID #353 claimed with interest + penalty (if any)','12','2025-09-20 09:13:06');
INSERT INTO `cash_ledger` VALUES ('596','1','pawned_items','354','1500.00','pawn','out','Pawn Add (ID #354)','test phone','12','2025-09-20 13:55:15');
INSERT INTO `cash_ledger` VALUES ('597','1','tubo_payments','107','90.00','tubo_payment','in','Tubo Payment (Pawn ID #354)','Tubo payment of ₱90.00 for Pawn ID #354. Period: 2025-09-20 to 2025-10-20','12','2025-09-20 13:55:39');
INSERT INTO `cash_ledger` VALUES ('598','1','partial_payments','123','1180.00','partial_payment','in','Partial + Tubo (Pawn ID #352)','Partial ₱1,000.00 | Interest ₱180.00','12','2025-09-20 15:45:15');


CREATE TABLE `claims` (
  `claim_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`claim_id`),
  KEY `fk_claim_pawn` (`pawn_id`),
  KEY `fk_claim_cashier` (`cashier_id`),
  KEY `idx_claim_branch_date` (`branch_id`,`date_claimed`),
  CONSTRAINT `fk_claim_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_claim_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_claim_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `claims` VALUES ('175','348','1','2025-09-18','2','0.08','800.00','5000.00','0.00','5800.00','12','','uploads/claimants/claimant_348_1758159611.png','2025-09-18 09:40:11');
INSERT INTO `claims` VALUES ('176','347','1','2025-09-18','0','0.06','0.00','2400.00','0.00','2400.00','12','','uploads/claimants/claimant_347_1758165561.png','2025-09-18 11:19:21');
INSERT INTO `claims` VALUES ('177','353','1','2025-09-20','0','0.06','0.00','1000.00','0.00','1000.00','12','','uploads/claimants/claimant_353_1758330786.png','2025-09-20 09:13:06');


CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` VALUES ('51','TEST PAWNER I','09123456789','TEST ADDRESS','','2025-09-03 09:21:04');
INSERT INTO `customers` VALUES ('52','Juan Dela Cruz','09123456789','TEST ADDRESS','','2025-09-04 12:26:41');
INSERT INTO `customers` VALUES ('53','KENNETH SON RAMOS','09123456789','MAHARLIKA HIGHWAY','','2025-09-05 10:09:11');


CREATE TABLE `forfeitures` (
  `forfeiture_id` int(11) NOT NULL AUTO_INCREMENT,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_forfeited` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`forfeiture_id`),
  KEY `fk_forf_pawn` (`pawn_id`),
  KEY `idx_forf_branch_date` (`branch_id`,`date_forfeited`),
  CONSTRAINT `fk_forf_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_forf_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `interest_rates` (
  `rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `rate_percent` decimal(5,2) NOT NULL,
  `effective_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rate_id`),
  KEY `idx_rates_branch_date` (`branch_id`,`effective_date`),
  CONSTRAINT `fk_rates_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `interest_rates` VALUES ('1','1','6.00','2025-01-01','1','2025-08-14 20:57:02');
INSERT INTO `interest_rates` VALUES ('2','2','6.00','2025-01-01','1','2025-08-14 20:57:02');


CREATE TABLE `partial_payments` (
  `pp_id` int(11) NOT NULL AUTO_INCREMENT,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `interest_paid` decimal(12,2) NOT NULL,
  `principal_paid` decimal(12,2) NOT NULL,
  `remaining_principal` decimal(12,2) NOT NULL,
  `status` enum('active','settled') NOT NULL DEFAULT 'active',
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pp_id`),
  KEY `idx_pp_pawn` (`pawn_id`,`created_at`),
  KEY `idx_pp_branch` (`branch_id`,`created_at`),
  KEY `fk_pp_user` (`user_id`),
  CONSTRAINT `fk_pp_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pp_pawn` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `partial_payments` VALUES ('116','347','1','100.00','0.00','100.00','4900.00','settled','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('117','347','1','500.00','0.00','500.00','4400.00','settled','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('118','347','1','500.00','0.00','500.00','3900.00','settled','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('119','347','1','1000.00','0.00','1000.00','2900.00','settled','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('120','347','1','500.00','0.00','500.00','0.00','settled','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('121','349','1','1000.00','0.00','1000.00','4000.00','active','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('122','350','1','1500.00','0.00','1500.00','1500.00','active','12','','2025-09-18 00:00:00');
INSERT INTO `partial_payments` VALUES ('123','352','1','1000.00','0.00','1000.00','2000.00','active','12','','2025-09-20 00:00:00');


CREATE TABLE `pawned_items` (
  `pawn_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `unit_description` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `amount_pawned` decimal(10,2) NOT NULL,
  `original_amount_pawned` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT 0.06,
  `date_pawned` date NOT NULL,
  `current_due_date` date DEFAULT NULL,
  `status` enum('pawned','claimed','forfeited') NOT NULL DEFAULT 'pawned',
  `has_partial_payments` tinyint(4) NOT NULL DEFAULT 0,
  `has_tubo_payments` tinyint(4) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `pass_key` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`pawn_id`),
  KEY `fk_pawn_branch` (`branch_id`),
  KEY `fk_pawn_created_by` (`created_by`),
  KEY `fk_pawn_updated_by` (`updated_by`),
  KEY `idx_pawn_status_branch` (`status`,`branch_id`),
  KEY `idx_pawn_dates` (`date_pawned`),
  CONSTRAINT `fk_pawn_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pawn_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pawn_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pawned_items` VALUES ('347','1','52','iphone 12','Cellphone','2400.00','5000.00','0.06','2025-08-05','2025-12-05','claimed','1','1','test','12345','uploads/pawn_items/pawn_1758156175_68cb558fe7c8f.png','12','12','2025-09-18 08:42:55','2025-09-18 11:19:21','0');
INSERT INTO `pawned_items` VALUES ('348','1','51','Honda Beat Fi v2','Motorcycle','5000.00','5000.00','0.08','2025-08-05','2025-09-05','claimed','0','0','test','','uploads/pawn_items/pawn_1758159145_68cb6129b2fea.png','12','','2025-09-18 09:32:25','2025-09-18 09:40:11','0');
INSERT INTO `pawned_items` VALUES ('349','1','51','test','Cellphone','4000.00','5000.00','0.06','2025-08-05','2025-12-05','pawned','1','1','test','','uploads/pawn_items/pawn_1758165615_68cb7a6ff00ec.png','12','12','2025-09-18 11:20:15','2025-09-18 11:25:03','0');
INSERT INTO `pawned_items` VALUES ('350','1','52','oppo a5s','Cellphone','1500.00','3000.00','0.06','2025-08-05','2025-11-05','pawned','1','1','','pogi12345','uploads/pawn_items/pawn_1758167346_68cb813212781.png','12','12','2025-09-18 11:49:06','2025-09-18 11:49:49','0');
INSERT INTO `pawned_items` VALUES ('351','1','51','oppo a5s','Cellphone','3000.00','3000.00','0.06','2025-07-01','2025-08-01','pawned','0','0','','pogi12345','uploads/pawn_items/pawn_1758246793_68ccb789c6408.png','12','','2025-09-19 09:53:13','','0');
INSERT INTO `pawned_items` VALUES ('352','1','51','oppo a5s','Cellphone','2000.00','3000.00','0.06','2025-09-19','2025-11-19','pawned','1','1','','pogi12345','uploads/pawn_items/pawn_1758247233_68ccb941aae19.png','12','12','2025-09-19 10:00:33','2025-09-20 15:45:15','0');
INSERT INTO `pawned_items` VALUES ('353','1','52','oppo a5s','Cellphone','1000.00','1000.00','0.06','2025-09-20','2026-01-20','claimed','0','1','','pogi12345','uploads/pawn_items/pawn_1758330124_68cdfd0c17bf9.png','12','12','2025-09-20 09:02:04','2025-09-20 09:13:06','0');
INSERT INTO `pawned_items` VALUES ('354','1','53','test phone','Cellphone','1500.00','1500.00','0.06','2025-09-20','2025-11-20','pawned','0','1','','','uploads/pawn_items/pawn_1758347715_68ce41c34b11d.png','12','12','2025-09-20 13:55:15','2025-09-20 13:55:39','0');


CREATE TABLE `remata_sales` (
  `sale_id` int(11) NOT NULL AUTO_INCREMENT,
  `pawn_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date_sold` date NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `buyer_name` varchar(100) DEFAULT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sale_id`),
  KEY `fk_sale_pawn` (`pawn_id`),
  KEY `fk_sale_cashier` (`cashier_id`),
  KEY `idx_sale_branch_date` (`branch_id`,`date_sold`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cash_threshold` decimal(12,2) DEFAULT 0.00,
  `pawn_maturity_reminder_days` int(11) DEFAULT 3,
  `export_format` varchar(20) DEFAULT 'excel',
  `report_info` text DEFAULT NULL,
  `backup_frequency` varchar(20) DEFAULT 'manual',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_timeout` int(11) DEFAULT 15,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` VALUES ('1','10000.00','7','excel','','manual','2025-09-24 13:23:47','120');


CREATE TABLE `tubo_payments` (
  `tubo_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tubo_id`),
  UNIQUE KEY `txn_code` (`txn_code`),
  KEY `cashier_id` (`cashier_id`),
  KEY `pawn_id` (`pawn_id`),
  KEY `branch_id` (`branch_id`),
  KEY `date_paid` (`date_paid`),
  CONSTRAINT `tubo_payments_ibfk_1` FOREIGN KEY (`pawn_id`) REFERENCES `pawned_items` (`pawn_id`),
  CONSTRAINT `tubo_payments_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  CONSTRAINT `tubo_payments_ibfk_3` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tubo_payments` VALUES ('100','347','renewal','1','2025-09-18','2025-08-05','2025-10-05','2','2025-11-05','0.06','600.00','12','','cash','Partial Payment + Tubo','2025-09-18 00:00:00');
INSERT INTO `tubo_payments` VALUES ('101','347','renewal','1','2025-09-18','2025-11-05','2025-12-05','1','2025-12-05','0.06','264.00','12','','cash','','2025-09-18 09:13:41');
INSERT INTO `tubo_payments` VALUES ('102','349','renewal','1','2025-09-18','2025-08-05','2025-10-05','2','2025-11-05','0.06','600.00','12','','cash','Partial Payment + Tubo','2025-09-18 00:00:00');
INSERT INTO `tubo_payments` VALUES ('103','349','renewal','1','2025-09-18','2025-11-05','2025-12-05','1','2025-12-05','0.06','240.00','12','','cash','','2025-09-18 11:25:03');
INSERT INTO `tubo_payments` VALUES ('104','350','renewal','1','2025-09-18','2025-08-05','2025-10-05','2','2025-11-05','0.06','360.00','12','','cash','Partial Payment + Tubo','2025-09-18 00:00:00');
INSERT INTO `tubo_payments` VALUES ('105','353','renewal','1','2025-09-20','2025-09-20','2025-10-20','1','2025-11-20','0.06','60.00','12','','cash','','2025-09-20 09:02:29');
INSERT INTO `tubo_payments` VALUES ('106','353','renewal','1','2025-09-20','2025-11-20','2026-01-20','2','2026-01-20','0.06','120.00','12','','cash','','2025-09-20 09:06:08');
INSERT INTO `tubo_payments` VALUES ('107','354','renewal','1','2025-09-30','2025-09-20','2025-10-20','1','2025-11-20','0.06','90.00','12','','cash','','2025-09-20 13:55:39');
INSERT INTO `tubo_payments` VALUES ('108','352','renewal','1','2025-09-20','2025-09-19','2025-10-19','1','2025-11-19','0.06','180.00','12','','cash','Partial Payment + Tubo','2025-09-20 00:00:00');


CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','cashier') NOT NULL DEFAULT 'cashier',
  `full_name` varchar(100) DEFAULT NULL,
  `status` enum('active','disabled') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_users_branch` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES ('8','','','super_admin','$2y$10$RCfvdDWOKIKaA9yWjuJS6ObLGRyVjE.pHifIFYarTYhr5COZtnLRm','super_admin','Super Admin','active','2025-09-24 13:12:46','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('9','1','','cashier_main','$2y$10$Na123ZSyu.MV.WREwoIgROgaISuBZkQq3akGubIOiAOHepDsuKWxi','cashier','Cashier Main Branch','active','2025-09-20 13:07:49','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('10','2','','cashier_gandara','$2y$10$4rYOgWAS/tDN6Kh5jWF30edW/oisBo1dCWrkeVt5/8wVLXZ5gP/ZG','cashier','Cashier Gandara Branch','active','2025-08-30 13:56:12','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('11','2','','admin_gandara','$2y$10$5PuaTNO.DlygUl9KDBogaeR5sVaEeTAi6P6xiBn0hFZ2aqs2m6/Gu','admin','Admin Gandara Branch','active','2025-09-06 13:08:28','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('12','1','','admin_main','$2y$10$WR6ENbCy3jAK1hYGSutq.Ol/TMyzPd/R9c5CSjrRRTUhpzIiohfj.','admin','Admin Main Branch','active','2025-09-20 13:08:00','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('13','4','','test_user','$2y$10$pYtuAp1CU2L5Tb22TnlEYOx5tq92Ri62ehrF5kAx2tOh0ChEen2aW','admin','test_user','active','2025-08-29 16:04:33','2025-08-29 15:57:29');
INSERT INTO `users` VALUES ('14','1','','test_user2','$2y$10$.ZXBrXhqJD1YTByShtEAs.rDwkwbF0DQTgmPz6EmjJSYp/5z8kMq2','admin','test_user2','','','2025-08-29 15:59:21');
