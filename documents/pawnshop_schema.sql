
-- Pawnshop Information Management System
-- MySQL schema (InnoDB, utf8mb4) + sample data
-- Safe to run on a new empty database.

-- 1) Create database
CREATE DATABASE IF NOT EXISTS pawnshop_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE pawnshop_db;

-- 2) Branches
DROP TABLE IF EXISTS branches;
CREATE TABLE branches (
  branch_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_name VARCHAR(100) NOT NULL,
  branch_address VARCHAR(255) DEFAULT NULL,
  branch_phone VARCHAR(30) DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3) Users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('super_admin','admin','cashier') NOT NULL DEFAULT 'cashier',
  full_name VARCHAR(100) DEFAULT NULL,
  status ENUM('active','disabled') DEFAULT 'active',
  last_login DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4) Interest rate history (snapshot rate is stored on pawned_items at transaction time)
DROP TABLE IF EXISTS interest_rates;
CREATE TABLE interest_rates (
  rate_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NULL,
  rate_percent DECIMAL(5,2) NOT NULL, -- e.g., 6.00 for 6%
  effective_date DATE NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rates_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_rates_branch_date (branch_id, effective_date)
) ENGINE=InnoDB;

-- 5) Pawned items (category stored directly here; no categories table)
DROP TABLE IF EXISTS pawned_items;
CREATE TABLE pawned_items (
  pawn_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NOT NULL,
  owner_name VARCHAR(100) NOT NULL,
  contact_no VARCHAR(20) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  unit_description VARCHAR(255) NOT NULL,
  category VARCHAR(50) DEFAULT NULL,
  amount_pawned DECIMAL(10,2) NOT NULL,
  interest_rate DECIMAL(5,2) DEFAULT 6.00, -- snapshot of monthly rate at time of pawn
  interest_amount DECIMAL(10,2) DEFAULT NULL, -- filled at claim time; NULL while pawned
  date_pawned DATE NOT NULL,
  date_claimed DATE DEFAULT NULL,
  date_forfeited DATE DEFAULT NULL,
  status ENUM('pawned','claimed','forfeited') NOT NULL DEFAULT 'pawned',
  notes TEXT DEFAULT NULL,
  created_by INT NULL,
  updated_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pawn_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pawn_created_by
    FOREIGN KEY (created_by) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_pawn_updated_by
    FOREIGN KEY (updated_by) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_pawn_status_branch (status, branch_id),
  INDEX idx_pawn_owner (owner_name),
  INDEX idx_pawn_dates (date_pawned, date_claimed, date_forfeited)
) ENGINE=InnoDB;

-- 6) Claims (immutable audit record of each claim transaction)
DROP TABLE IF EXISTS claims;
CREATE TABLE claims (
  claim_id INT AUTO_INCREMENT PRIMARY KEY,
  pawn_id INT NOT NULL,
  branch_id INT NOT NULL,
  date_claimed DATE NOT NULL,
  months INT NOT NULL, -- months charged (min 1)
  interest_rate DECIMAL(5,2) NOT NULL,
  interest_amount DECIMAL(10,2) NOT NULL,
  principal_amount DECIMAL(10,2) NOT NULL,
  multa_amount DECIMAL(10,2) DEFAULT 0.00, -- optional penalty
  total_paid DECIMAL(10,2) NOT NULL,
  cashier_id INT NULL,
  notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_claim_pawn
    FOREIGN KEY (pawn_id) REFERENCES pawned_items(pawn_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_claim_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_claim_cashier
    FOREIGN KEY (cashier_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_claim_branch_date (branch_id, date_claimed)
) ENGINE=InnoDB;

-- 7) Tubo / Interest-only payments (optional partials)
DROP TABLE IF EXISTS tubo_payments;
CREATE TABLE tubo_payments (
  tubo_id INT AUTO_INCREMENT PRIMARY KEY,
  pawn_id INT NOT NULL,
  branch_id INT NOT NULL,
  date_paid DATE NOT NULL,
  months_covered INT NOT NULL,
  interest_rate DECIMAL(5,2) NOT NULL,
  interest_amount DECIMAL(10,2) NOT NULL,
  cashier_id INT NULL,
  notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tubo_pawn
    FOREIGN KEY (pawn_id) REFERENCES pawned_items(pawn_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_tubo_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_tubo_cashier
    FOREIGN KEY (cashier_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_tubo_branch_date (branch_id, date_paid)
) ENGINE=InnoDB;

-- 8) Forfeitures (event log when an item is forfeited)
DROP TABLE IF EXISTS forfeitures;
CREATE TABLE forfeitures (
  forfeiture_id INT AUTO_INCREMENT PRIMARY KEY,
  pawn_id INT NOT NULL,
  branch_id INT NOT NULL,
  date_forfeited DATE NOT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_forf_pawn
    FOREIGN KEY (pawn_id) REFERENCES pawned_items(pawn_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_forf_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_forf_branch_date (branch_id, date_forfeited)
) ENGINE=InnoDB;

-- 9) Remata sales (selling forfeited items)
DROP TABLE IF EXISTS remata_sales;
CREATE TABLE remata_sales (
  sale_id INT AUTO_INCREMENT PRIMARY KEY,
  pawn_id INT NOT NULL,
  branch_id INT NOT NULL,
  date_sold DATE NOT NULL,
  selling_price DECIMAL(10,2) NOT NULL,
  buyer_name VARCHAR(100) DEFAULT NULL,
  cashier_id INT NULL,
  notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sale_pawn
    FOREIGN KEY (pawn_id) REFERENCES pawned_items(pawn_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_sale_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_sale_cashier
    FOREIGN KEY (cashier_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_sale_branch_date (branch_id, date_sold)
) ENGINE=InnoDB;

-- 10) Cash ledger (unified money movement)
DROP TABLE IF EXISTS cash_ledger;
CREATE TABLE cash_ledger (
  ledger_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_id INT NOT NULL,
  txn_type ENUM('pawn_disbursement','claim_receipt','tubo_interest','cash_adjustment','remata_sale','refund','other') NOT NULL,
  direction ENUM('in','out') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  ref_table VARCHAR(50) DEFAULT NULL,
  ref_id INT DEFAULT NULL,
  notes VARCHAR(255) DEFAULT NULL,
  user_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ledger_branch
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_ledger_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_ledger_branch_date (branch_id, created_at)
) ENGINE=InnoDB;

-- 11) Audit log
DROP TABLE IF EXISTS audit_log;
CREATE TABLE audit_log (
  audit_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(50) NOT NULL,
  table_name VARCHAR(50) NOT NULL,
  record_id INT NOT NULL,
  before_json JSON DEFAULT NULL,
  after_json JSON DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 12) Sample data

-- Branches
INSERT INTO branches (branch_name, branch_address, branch_phone) VALUES
('Main Branch','123 Central Ave, City','09171234567'),
('West Branch','45 West St, City','09181234567');

-- Users (password hash here is bcrypt for 'admin123' and 'cashier123' placeholders)
INSERT INTO users (branch_id, username, password_hash, role, full_name) VALUES
(NULL, 'superadmin', '$2y$10$9E2m9bYg3l5V5hZpE8jEeu3Zq0o7jdnT3T0Gm1lG7f3v8xX4o1nBi', 'super_admin', 'Super Admin'),
(1, 'admin_main', '$2y$10$9E2m9bYg3l5V5hZpE8jEeu3Zq0o7jdnT3T0Gm1lG7f3v8xX4o1nBi', 'admin', 'Main Admin'),
(2, 'cashier_west', '$2y$10$9E2m9bYg3l5V5hZpE8jEeu3Zq0o7jdnT3T0Gm1lG7f3v8xX4o1nBi', 'cashier', 'West Cashier');

-- Interest rates
INSERT INTO interest_rates (branch_id, rate_percent, effective_date, is_active) VALUES
(1, 6.00, '2025-01-01', 1),
(2, 6.00, '2025-01-01', 1);

-- Pawned items (some pawned, some claimed, some forfeited)
INSERT INTO pawned_items
(branch_id, owner_name, contact_no, address, unit_description, category, amount_pawned, interest_rate, interest_amount, date_pawned, date_claimed, date_forfeited, status, notes, created_by)
VALUES
(1,'Juan Dela Cruz','09170000001','City','iPhone 12 128GB','Gadgets',15000.00,6.00,NULL,'2025-07-10',NULL,NULL,'pawned',NULL,1),
(1,'Maria Santos','09170000002','City','Samsung 55" TV','Appliances',22000.00,6.00,1320.00,'2025-06-01','2025-07-01',NULL,'claimed','1 month min interest',1),
(2,'Pedro Reyes','09170000003','City','Gold Ring 18k','Jewelry',12000.00,6.00,NULL,'2025-05-15',NULL,'2025-07-20','forfeited','Unclaimed >60 days',2);

-- Claims (audit record for the claimed item above)
INSERT INTO claims
(pawn_id, branch_id, date_claimed, months, interest_rate, interest_amount, principal_amount, multa_amount, total_paid, cashier_id, notes)
VALUES
(2, 1, '2025-07-01', 1, 6.00, 1320.00, 22000.00, 0.00, 23320.00, 1, 'Claimed with 1-month min interest');

-- Forfeitures (event log for forfeited item above)
INSERT INTO forfeitures (pawn_id, branch_id, date_forfeited, reason, notes)
VALUES
(3, 2, '2025-07-20', 'Unclaimed after maturity', 'Moved to remata list');

-- Remata sale example
INSERT INTO remata_sales (pawn_id, branch_id, date_sold, selling_price, buyer_name, cashier_id, notes)
VALUES
(3, 2, '2025-08-01', 15000.00, 'Walk-in Buyer', 3, 'Sold at counter');

-- Cash ledger examples
INSERT INTO cash_ledger (branch_id, txn_type, direction, amount, ref_table, ref_id, notes, user_id) VALUES
(1, 'pawn_disbursement', 'out', 15000.00, 'pawned_items', 1, 'Pawn payout for iPhone 12', 1),
(1, 'claim_receipt', 'in', 23320.00, 'claims', 1, 'Claim payment for Samsung 55" TV', 1),
(2, 'cash_adjustment', 'in', 50000.00, NULL, NULL, 'Initial float', 3),
(2, 'remata_sale', 'in', 15000.00, 'remata_sales', 1, 'Sale of forfeited Gold Ring', 3);
