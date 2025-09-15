

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
) ENGINE=InnoDB AUTO_INCREMENT=937 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_logs` VALUES ('914','12','1','Cash On Hand Adjustment','COH Add: +₱10,000.00 (Old COH: ₱0.00, New COH: ₱10,000.00)','2025-09-15 10:41:20');
INSERT INTO `audit_logs` VALUES ('915','12','1','Add Pawned Item','Added a new pawn item for Customer #52 (Unit: VIVO Phone, Category: Cellphone, Amount: ₱2,000.00)','2025-09-15 10:45:57');
INSERT INTO `audit_logs` VALUES ('916','12','1','Add Pawned Item','Added a new pawn item for Customer #53 (Unit: test, Category: Cellphone, Amount: ₱2,000.00)','2025-09-15 10:52:34');
INSERT INTO `audit_logs` VALUES ('917','12','1','Claim Pawned Item','Claimed pawn item: test, Category: Cellphone, Total Amount Paid: ₱2,120.00','2025-09-15 10:52:48');
INSERT INTO `audit_logs` VALUES ('918','12','1','Add Pawned Item','Added a new pawn item for Customer #52 (Unit: VIVO Phone, Category: Cellphone, Amount: ₱2,000.00)','2025-09-15 11:06:18');
INSERT INTO `audit_logs` VALUES ('919','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone details','2025-09-15 11:24:41');
INSERT INTO `audit_logs` VALUES ('920','12','1','Add Pawned Item','Added a new pawn item for Customer #51 (Unit: nMax, Category: Motorcycle, Amount: ₱5,000.00)','2025-09-15 11:28:18');
INSERT INTO `audit_logs` VALUES ('921','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123 details','2025-09-15 11:37:28');
INSERT INTO `audit_logs` VALUES ('922','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from:1,502.00to ₱1,500.00','2025-09-15 11:40:22');
INSERT INTO `audit_logs` VALUES ('923','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from: 1,500.00to ₱2,000.00','2025-09-15 11:41:40');
INSERT INTO `audit_logs` VALUES ('924','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from:2,000.00 to ₱2,001.00','2025-09-15 11:42:26');
INSERT INTO `audit_logs` VALUES ('925','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from: 2,001.00 to ₱2,000.00','2025-09-15 11:43:40');
INSERT INTO `audit_logs` VALUES ('926','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from: 2,000.00 to ₱1,800.00','2025-09-15 11:44:36');
INSERT INTO `audit_logs` VALUES ('927','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from: 1,800.00 to ₱2,000.00','2025-09-15 11:45:18');
INSERT INTO `audit_logs` VALUES ('928','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from: 2,000.00 to ₱1,800.00','2025-09-15 11:45:43');
INSERT INTO `audit_logs` VALUES ('929','12','1','Edit Pawn Item','Edit pawn item: VIVO Phone123, adjusted amount from: 1,800.00 to ₱2,000.00','2025-09-15 11:46:01');
INSERT INTO `audit_logs` VALUES ('930','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: VIVO Phone123, Interest Amount: ₱120.00, Period: 2025-09-15 to 2025-10-15, Months Covered: 1, New Due Date: 2025-11-15','2025-09-15 13:38:56');
INSERT INTO `audit_logs` VALUES ('931','12','1','Partial Payment','Partial payment recorded. Pawn Item: nMax, Total Amount Paid: ₱1,160.00 (Partial ₱1,000.00 + Interest ₱160.00), New Principal: ₱1,000.00','2025-09-15 13:40:06');
INSERT INTO `audit_logs` VALUES ('932','12','1','Add Pawned Item','Added a new pawn item for Customer #52 (Unit: test, Category: Cellphone, Amount: ₱500.00)','2025-09-15 13:40:50');
INSERT INTO `audit_logs` VALUES ('933','12','1','Add Pawned Item','Added a new pawn item for Customer #53 (Unit: test, Category: Cellphone, Amount: ₱500.00)','2025-09-15 13:49:05');
INSERT INTO `audit_logs` VALUES ('934','12','1','Partial Payment','Partial payment recorded. Pawn Item: test, Total Amount Paid: ₱130.00 (Partial ₱100.00 + Interest ₱30.00), New Principal: ₱400.00','2025-09-15 13:52:22');
INSERT INTO `audit_logs` VALUES ('935','12','1','Tubo Payment','Tubo payment recorded. Pawn Item: VIVO Phone, Interest Amount: ₱120.00, Period: 2025-08-01 to 2025-09-01, Months Covered: 1, New Due Date: 2025-10-01','2025-09-15 13:56:46');


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

INSERT INTO `branches` VALUES ('1','Calbayog Branch - Main','Navarro St. , Calbayog City, Samar','09123456789','active','0.06','0.08','','','4650.00','2025-08-14 20:57:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=535 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cash_ledger` VALUES ('508','1','branches','1','10000.00','coh_adjustment','in','Add COH Adjustment','set','12','2025-09-15 10:41:20');
INSERT INTO `cash_ledger` VALUES ('509','1','pawned_items','330','2000.00','pawn','out','Pawn Add (ID #330)','VIVO Phone','12','2025-09-15 10:45:57');
INSERT INTO `cash_ledger` VALUES ('510','1','pawned_items','331','2000.00','pawn','out','Pawn Add (ID #331)','test','12','2025-09-15 10:52:34');
INSERT INTO `cash_ledger` VALUES ('511','1','claims','331','2120.00','claim','in','Claim (ID #168)','Pawn ID #331 claimed with interest + penalty (if any)','12','2025-09-15 10:52:48');
INSERT INTO `cash_ledger` VALUES ('512','1','pawned_items','332','2000.00','pawn','out','Pawn Add (ID #332)','VIVO Phone','12','2025-09-15 11:06:18');
INSERT INTO `cash_ledger` VALUES ('513','1','pawned_items','333','5000.00','pawn','out','Pawn Add (ID #333)','nMax','12','2025-09-15 11:28:18');
INSERT INTO `cash_ledger` VALUES ('514','1','pawned_items','333','4000.00','pawn_edit','in','Pawn Edit (ID #333)','Pawn amount adjusted from ₱5,000.00 to ₱1,000.00','12','2025-09-15 11:30:21');
INSERT INTO `cash_ledger` VALUES ('515','1','pawned_items','333','1000.00','pawn_edit','out','Pawn Edit (ID #333)','Pawn amount adjusted from ₱1,000.00 to ₱2,000.00','12','2025-09-15 11:31:28');
INSERT INTO `cash_ledger` VALUES ('516','1','pawned_items','332','1500.00','pawn_edit','in','Pawn Edit (ID #332)','Pawn amount adjusted from ₱2,000.00 to ₱500.00','12','2025-09-15 11:33:08');
INSERT INTO `cash_ledger` VALUES ('517','1','pawned_items','332','500.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱500.00 to ₱1,000.00','12','2025-09-15 11:35:02');
INSERT INTO `cash_ledger` VALUES ('518','1','pawned_items','332','500.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,000.00 to ₱1,500.00','12','2025-09-15 11:35:17');
INSERT INTO `cash_ledger` VALUES ('519','1','pawned_items','332','1.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,500.00 to ₱1,501.00','12','2025-09-15 11:36:05');
INSERT INTO `cash_ledger` VALUES ('520','1','pawned_items','332','1.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,501.00 to ₱1,502.00','12','2025-09-15 11:36:33');
INSERT INTO `cash_ledger` VALUES ('521','1','pawned_items','332','2.00','pawn_edit','in','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,502.00 to ₱1,500.00','12','2025-09-15 11:40:22');
INSERT INTO `cash_ledger` VALUES ('522','1','pawned_items','332','500.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,500.00 to ₱2,000.00','12','2025-09-15 11:41:40');
INSERT INTO `cash_ledger` VALUES ('523','1','pawned_items','332','1.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱2,000.00 to ₱2,001.00','12','2025-09-15 11:42:26');
INSERT INTO `cash_ledger` VALUES ('524','1','pawned_items','332','1.00','pawn_edit','in','Pawn Edit (ID #332)','Pawn amount adjusted from ₱2,001.00 to ₱2,000.00','12','2025-09-15 11:43:40');
INSERT INTO `cash_ledger` VALUES ('525','1','pawned_items','332','200.00','pawn_edit','in','Pawn Edit (ID #332)','Pawn amount adjusted from ₱2,000.00 to ₱1,800.00','12','2025-09-15 11:44:36');
INSERT INTO `cash_ledger` VALUES ('526','1','pawned_items','332','200.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,800.00 to ₱2,000.00','12','2025-09-15 11:45:18');
INSERT INTO `cash_ledger` VALUES ('527','1','pawned_items','332','200.00','pawn_edit','in','Pawn Edit (ID #332)','Pawn amount adjusted from ₱2,000.00 to ₱1,800.00','12','2025-09-15 11:45:43');
INSERT INTO `cash_ledger` VALUES ('528','1','pawned_items','332','200.00','pawn_edit','out','Pawn Edit (ID #332)','Pawn amount adjusted from ₱1,800.00 to ₱2,000.00','12','2025-09-15 11:46:01');
INSERT INTO `cash_ledger` VALUES ('529','1','tubo_payments','91','120.00','tubo_payment','in','Tubo Payment (Pawn ID #332)','Tubo payment of ₱120.00 for Pawn ID #332. Period: 2025-09-15 to 2025-10-15','12','2025-09-15 13:38:55');
INSERT INTO `cash_ledger` VALUES ('530','1','partial_payments','103','1160.00','partial_payment','in','Partial Payment (Pawn ID #333)','Partial payment: Principal ₱1,000.00 | Interest ₱160.00','12','2025-09-15 13:40:06');
INSERT INTO `cash_ledger` VALUES ('531','1','pawned_items','334','500.00','pawn','out','Pawn Add (ID #334)','test','12','2025-09-15 13:40:50');
INSERT INTO `cash_ledger` VALUES ('532','1','pawned_items','335','500.00','pawn','out','Pawn Add (ID #335)','test','12','2025-09-15 13:49:05');
INSERT INTO `cash_ledger` VALUES ('533','1','partial_payments','104','130.00','partial_payment','in','Partial Payment (Pawn ID #335)','Partial payment: Principal ₱100.00 | Interest ₱30.00','12','2025-09-15 13:52:22');
INSERT INTO `cash_ledger` VALUES ('534','1','tubo_payments','92','120.00','tubo_payment','in','Tubo Payment (Pawn ID #330)','Tubo payment of ₱120.00 for Pawn ID #330. Period: 2025-08-01 to 2025-09-01','12','2025-09-15 13:56:46');


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
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `claims` VALUES ('168','331','1','2025-09-15','1','0.06','120.00','2000.00','0.00','2120.00','12','','uploads/claimants/claimant_331_1757904768.png','2025-09-15 10:52:48');


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
INSERT INTO `customers` VALUES ('52','Ace','09123456789','TEST ADDRESS','','2025-09-04 12:26:41');
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
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `partial_payments` VALUES ('103','333','1','1160.00','160.00','1000.00','1000.00','active','12','','2025-09-20 00:00:00');
INSERT INTO `partial_payments` VALUES ('104','335','1','130.00','30.00','100.00','400.00','active','12','','2025-09-15 00:00:00');


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
) ENGINE=InnoDB AUTO_INCREMENT=336 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pawned_items` VALUES ('330','1','52','VIVO Phone','Cellphone','2000.00','2000.00','0.06','2025-08-01','2025-10-01','pawned','0','1','test','test','uploads/pawn_items/pawn_1757904357_68c77de5ea103.png','12','12','2025-09-15 10:45:57','2025-09-15 13:56:46','0');
INSERT INTO `pawned_items` VALUES ('331','1','53','test','Cellphone','2000.00','2000.00','0.06','2025-09-15','2025-10-15','claimed','0','0','test','','uploads/pawn_items/pawn_1757904754_68c77f72c0933.png','12','','2025-09-15 10:52:34','2025-09-15 10:52:48','0');
INSERT INTO `pawned_items` VALUES ('332','1','52','VIVO Phone123','Cellphone','2000.00','2000.00','0.06','2025-09-15','2025-11-15','pawned','0','1','test','pogi12345','uploads/pawn_items/pawn_1757905578_68c782aa0071f.png','12','12','2025-09-15 11:06:18','2025-09-15 13:38:55','0');
INSERT INTO `pawned_items` VALUES ('333','1','51','nMax','Motorcycle','1000.00','2000.00','0.08','2025-09-15','2025-11-15','pawned','1','0','test','','uploads/pawn_items/pawn_1757906898_68c787d23aa8b.png','12','12','2025-09-15 11:28:18','2025-09-15 13:40:06','0');
INSERT INTO `pawned_items` VALUES ('334','1','52','test','Cellphone','500.00','500.00','0.06','2025-07-01','2025-08-01','pawned','0','0','test','','uploads/pawn_items/pawn_1757914850_68c7a6e2de0c4.png','12','','2025-09-15 13:40:50','','0');
INSERT INTO `pawned_items` VALUES ('335','1','53','test','Cellphone','400.00','500.00','0.06','2025-09-14','2025-11-14','pawned','1','0','test','','uploads/pawn_items/pawn_1757915345_68c7a8d12f57d.png','12','12','2025-09-15 13:49:05','2025-09-15 13:52:22','0');


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

INSERT INTO `settings` VALUES ('1','10000.00','7','excel','','manual','2025-09-11 11:14:21','120');


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
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tubo_payments` VALUES ('91','332','renewal','1','2025-09-15','2025-09-15','2025-10-15','1','2025-11-15','0.06','120.00','12','','cash','','2025-09-15 13:38:55');
INSERT INTO `tubo_payments` VALUES ('92','330','renewal','1','2025-09-15','2025-08-01','2025-09-01','1','2025-10-01','0.06','120.00','12','','cash','','2025-09-15 13:56:46');


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

INSERT INTO `users` VALUES ('8','','','super_admin','$2y$10$RCfvdDWOKIKaA9yWjuJS6ObLGRyVjE.pHifIFYarTYhr5COZtnLRm','super_admin','Super Admin','active','2025-09-15 14:17:24','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('9','1','','cashier_main','$2y$10$Na123ZSyu.MV.WREwoIgROgaISuBZkQq3akGubIOiAOHepDsuKWxi','cashier','Cashier Main Branch','active','2025-08-29 10:58:55','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('10','2','','cashier_gandara','$2y$10$4rYOgWAS/tDN6Kh5jWF30edW/oisBo1dCWrkeVt5/8wVLXZ5gP/ZG','cashier','Cashier Gandara Branch','active','2025-08-30 13:56:12','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('11','2','','admin_gandara','$2y$10$5PuaTNO.DlygUl9KDBogaeR5sVaEeTAi6P6xiBn0hFZ2aqs2m6/Gu','admin','Admin Gandara Branch','active','2025-09-06 13:08:28','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('12','1','','admin_main','$2y$10$WR6ENbCy3jAK1hYGSutq.Ol/TMyzPd/R9c5CSjrRRTUhpzIiohfj.','admin','Admin Main Branch','active','2025-09-15 09:56:24','2025-08-14 21:36:07');
INSERT INTO `users` VALUES ('13','4','','test_user','$2y$10$pYtuAp1CU2L5Tb22TnlEYOx5tq92Ri62ehrF5kAx2tOh0ChEen2aW','admin','test_user','active','2025-08-29 16:04:33','2025-08-29 15:57:29');
INSERT INTO `users` VALUES ('14','1','','test_user2','$2y$10$.ZXBrXhqJD1YTByShtEAs.rDwkwbF0DQTgmPz6EmjJSYp/5z8kMq2','admin','test_user2','','','2025-08-29 15:59:21');
