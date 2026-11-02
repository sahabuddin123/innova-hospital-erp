-- ============================================================================
-- LIFECARE HOSPITAL MANAGEMENT SYSTEM - LIVE SERVER DATABASE MIGRATION
-- Generated on: 2026-11-02 09:36:07
-- Safe, Idempotent: Uses CREATE TABLE IF NOT EXISTS & INSERT IGNORE
-- ============================================================================

-- ============================================================================
-- SECTION 1: CREATE MISSING SYSTEM, AUDIT & TRANSACTION TABLES
-- ============================================================================

-- Table: `user_login_logs`
CREATE TABLE IF NOT EXISTS `user_login_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_reference` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_time` int NOT NULL,
  `login_datetime` datetime NOT NULL,
  `logout_time` int DEFAULT NULL,
  `logout_datetime` datetime DEFAULT NULL,
  `last_activity_time` int DEFAULT NULL,
  `session_duration` int DEFAULT '0',
  `login_status` enum('SUCCESS','FAILED') COLLATE utf8mb4_unicode_ci DEFAULT 'SUCCESS',
  `failure_reason` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logout_type` enum('MANUAL','EXPIRED','AUTOMATIC','FORCED','INCOMPLETE') COLLATE utf8mb4_unicode_ci DEFAULT 'INCOMPLETE',
  `session_status` enum('ACTIVE','LOGGED_OUT','EXPIRED','INCOMPLETE') COLLATE utf8mb4_unicode_ci DEFAULT 'INCOMPLETE',
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_system` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `is_suspicious` tinyint(1) DEFAULT '0',
  `suspicious_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_historical` tinyint(1) DEFAULT '0',
  `hospital_id` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_ull_session` (`session_reference`),
  KEY `idx_ull_user` (`user_id`),
  KEY `idx_ull_username` (`username`),
  KEY `idx_ull_role` (`role`),
  KEY `idx_ull_status` (`login_status`),
  KEY `idx_ull_sess_status` (`session_status`),
  KEY `idx_ull_login_time` (`login_time`),
  KEY `idx_ull_ip` (`ip_address`),
  KEY `idx_ull_suspicious` (`is_suspicious`),
  KEY `idx_ull_historical` (`is_historical`),
  KEY `idx_ull_hosp` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `user_activity_logs`
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity_uuid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_reference` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` longtext COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `is_suspicious` tinyint(1) DEFAULT '0',
  `suspicious_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_historical` tinyint(1) DEFAULT '0',
  `created_at` int NOT NULL,
  `created_at_formatted` datetime NOT NULL,
  `hospital_id` int DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_uuid` (`activity_uuid`),
  KEY `idx_ual_user` (`user_id`),
  KEY `idx_ual_module` (`module_name`),
  KEY `idx_ual_action` (`action_type`),
  KEY `idx_ual_record` (`record_id`),
  KEY `idx_ual_created` (`created_at`),
  KEY `idx_ual_suspicious` (`is_suspicious`),
  KEY `idx_ual_historical` (`is_historical`),
  KEY `idx_ual_hosp` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `financial_audit_logs`
CREATE TABLE IF NOT EXISTS `financial_audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `audit_uuid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT '0.00',
  `previous_value` longtext COLLATE utf8mb4_unicode_ci,
  `new_value` longtext COLLATE utf8mb4_unicode_ci,
  `change_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason_for_change` text COLLATE utf8mb4_unicode_ci,
  `is_suspicious` tinyint(1) DEFAULT '0',
  `suspicious_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_historical` tinyint(1) DEFAULT '0',
  `user_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_time` int NOT NULL,
  `created_at_formatted` datetime NOT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hospital_id` int DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `audit_uuid` (`audit_uuid`),
  KEY `idx_fal_module` (`module_name`),
  KEY `idx_fal_action` (`action_type`),
  KEY `idx_fal_record` (`record_id`),
  KEY `idx_fal_ref` (`transaction_reference`),
  KEY `idx_fal_user` (`user_id`),
  KEY `idx_fal_date` (`date_time`),
  KEY `idx_fal_suspicious` (`is_suspicious`),
  KEY `idx_fal_hosp` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `role_permissions`
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` mediumint unsigned NOT NULL,
  `group_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `can_view` tinyint(1) NOT NULL DEFAULT '0',
  `can_create` tinyint(1) NOT NULL DEFAULT '0',
  `can_update` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete` tinyint(1) NOT NULL DEFAULT '0',
  `can_print` tinyint(1) NOT NULL DEFAULT '0',
  `can_export_pdf` tinyint(1) NOT NULL DEFAULT '0',
  `can_export_excel` tinyint(1) NOT NULL DEFAULT '0',
  `can_export_csv` tinyint(1) NOT NULL DEFAULT '0',
  `can_view_details` tinyint(1) NOT NULL DEFAULT '0',
  `can_view_sensitive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_group_perm_key` (`group_id`,`permission_group`,`report_key`),
  KEY `idx_group_name` (`group_name`),
  KEY `idx_permission_group` (`permission_group`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `permission_audit_logs`
CREATE TABLE IF NOT EXISTS `permission_audit_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` mediumint unsigned NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `action_changed` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int unsigned NOT NULL,
  `changed_by_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_permission_group` (`permission_group`),
  KEY `idx_changed_at` (`changed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `referral_commission_ledger`
CREATE TABLE IF NOT EXISTS `referral_commission_ledger` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) NOT NULL,
  `referrer_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `invoice_item_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `item_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `earned_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_reference` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Earned','Reversed','Cancelled') NOT NULL DEFAULT 'Pending',
  `hospital_id` int NOT NULL,
  `created_date` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `referrer_id` (`referrer_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

-- Table: `referral_wallet`
CREATE TABLE IF NOT EXISTS `referral_wallet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `referrer_id` int NOT NULL,
  `total_earned` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pending_commission` decimal(10,2) NOT NULL DEFAULT '0.00',
  `available_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pending_withdrawal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_withdrawn` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hospital_id` int NOT NULL,
  `updated_at` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referrer_id` (`referrer_id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table: `referral_wallet_transactions`
CREATE TABLE IF NOT EXISTS `referral_wallet_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `referrer_id` int NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `source` enum('commission','reversal','withdrawal','withdrawal_refund','adjustment') NOT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `hospital_id` int NOT NULL,
  `created_at` int NOT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referrer_id` (`referrer_id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Table: `referral_audit_log`
CREATE TABLE IF NOT EXISTS `referral_audit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(100) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `ip_address` varchar(50) DEFAULT NULL,
  `hospital_id` int NOT NULL,
  `created_at` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Table: `acc_audit_trail`
CREATE TABLE IF NOT EXISTS `acc_audit_trail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_time` int NOT NULL,
  `user_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `journal_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hospital_id` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_audit_dt` (`date_time`),
  KEY `idx_audit_action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `payment_items`
CREATE TABLE IF NOT EXISTS `payment_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `item_id` int NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `doctor_commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `doctor_commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `referral_commission_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `referral_commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hospital_id` int NOT NULL,
  `created_at` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `item_id` (`item_id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;

-- ============================================================================
-- SECTION 2: ENSURE REQUIRED COLUMNS ON EXISTING TABLES
-- ============================================================================

-- Table: payment_category
ALTER TABLE `payment_category` ADD COLUMN IF NOT EXISTS `payment_category` varchar(1000) DEFAULT NULL;
ALTER TABLE `payment_category` ADD COLUMN IF NOT EXISTS `payment_category_name` varchar(1000) DEFAULT NULL;
ALTER TABLE `payment_category` ADD COLUMN IF NOT EXISTS `r_commission` decimal(5,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `payment_category` ADD COLUMN IF NOT EXISTS `delivery_days` int NOT NULL DEFAULT '0';
ALTER TABLE `payment_category` ADD COLUMN IF NOT EXISTS `is_hormone` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `payment_category` ADD COLUMN IF NOT EXISTS `status` varchar(20) NOT NULL DEFAULT 'Active';

-- Table: payment
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `gross_total` decimal(10,2) DEFAULT '0.00';
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `flat_discount` varchar(100) DEFAULT NULL;
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `hospital_amount` decimal(10,2) DEFAULT '0.00';
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `doctor_amount` decimal(10,2) DEFAULT '0.00';
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `referral_amount` decimal(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `referrer` int DEFAULT NULL;
ALTER TABLE `payment` ADD COLUMN IF NOT EXISTS `referrer_name` varchar(255) DEFAULT NULL;

-- Table: lab
ALTER TABLE `lab` ADD COLUMN IF NOT EXISTS `test_status` varchar(500) DEFAULT NULL;
ALTER TABLE `lab` ADD COLUMN IF NOT EXISTS `test_status_date` varchar(500) DEFAULT NULL;
ALTER TABLE `lab` ADD COLUMN IF NOT EXISTS `delivery_status` varchar(500) DEFAULT NULL;
ALTER TABLE `lab` ADD COLUMN IF NOT EXISTS `delivery_status_date` varchar(500) DEFAULT NULL;
ALTER TABLE `lab` ADD COLUMN IF NOT EXISTS `receiver_name` varchar(500) DEFAULT NULL;

-- ============================================================================
-- SECTION 3: DEFAULT SEED DATA FOR ROLE-BASED ACCESS CONTROL (role_permissions)
-- ============================================================================

INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('1', '1', 'superadmin', 'financial_dashboard', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('2', '1', 'superadmin', 'income_collection', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('3', '1', 'superadmin', 'expense_accounts', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('4', '1', 'superadmin', 'doctor_commission', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('5', '1', 'superadmin', 'referral_commission', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('6', '1', 'superadmin', 'profit_loss', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('7', '1', 'superadmin', 'diagnostic_performance', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('8', '1', 'superadmin', 'financial_audit_logs', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('9', '1', 'superadmin', 'user_activity_reports', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('10', '2', 'members', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('11', '2', 'members', 'income_collection', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('12', '2', 'members', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('13', '2', 'members', 'doctor_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('14', '2', 'members', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('15', '2', 'members', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('16', '2', 'members', 'diagnostic_performance', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('17', '2', 'members', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('18', '2', 'members', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('19', '3', 'Accountant', 'financial_dashboard', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('20', '3', 'Accountant', 'income_collection', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('21', '3', 'Accountant', 'expense_accounts', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('22', '3', 'Accountant', 'doctor_commission', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('23', '3', 'Accountant', 'referral_commission', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('24', '3', 'Accountant', 'profit_loss', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:54', '2026-11-02 14:22:54');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('25', '3', 'Accountant', 'diagnostic_performance', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('26', '3', 'Accountant', 'financial_audit_logs', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('27', '3', 'Accountant', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('28', '4', 'Doctor', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('29', '4', 'Doctor', 'income_collection', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('30', '4', 'Doctor', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('31', '4', 'Doctor', 'doctor_commission', 'all', '1', '0', '0', '0', '1', '1', '0', '0', '1', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('32', '4', 'Doctor', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('33', '4', 'Doctor', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('34', '4', 'Doctor', 'diagnostic_performance', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('35', '4', 'Doctor', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('36', '4', 'Doctor', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('37', '5', 'Patient', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('38', '5', 'Patient', 'income_collection', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('39', '5', 'Patient', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('40', '5', 'Patient', 'doctor_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('41', '5', 'Patient', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('42', '5', 'Patient', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('43', '5', 'Patient', 'diagnostic_performance', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('44', '5', 'Patient', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('45', '5', 'Patient', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('46', '6', 'Nurse', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('47', '6', 'Nurse', 'income_collection', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('48', '6', 'Nurse', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('49', '6', 'Nurse', 'doctor_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('50', '6', 'Nurse', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('51', '6', 'Nurse', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('52', '6', 'Nurse', 'diagnostic_performance', 'all', '1', '0', '0', '0', '1', '1', '0', '0', '1', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('53', '6', 'Nurse', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('54', '6', 'Nurse', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('55', '7', 'Pharmacist', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('56', '7', 'Pharmacist', 'income_collection', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('57', '7', 'Pharmacist', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('58', '7', 'Pharmacist', 'doctor_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('59', '7', 'Pharmacist', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('60', '7', 'Pharmacist', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('61', '7', 'Pharmacist', 'diagnostic_performance', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('62', '7', 'Pharmacist', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('63', '7', 'Pharmacist', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('64', '8', 'Laboratorist', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('65', '8', 'Laboratorist', 'income_collection', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('66', '8', 'Laboratorist', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('67', '8', 'Laboratorist', 'doctor_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('68', '8', 'Laboratorist', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('69', '8', 'Laboratorist', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('70', '8', 'Laboratorist', 'diagnostic_performance', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('71', '8', 'Laboratorist', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('72', '8', 'Laboratorist', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('73', '10', 'Receptionist', 'financial_dashboard', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('74', '10', 'Receptionist', 'income_collection', 'all', '1', '0', '0', '0', '1', '1', '0', '0', '1', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('75', '10', 'Receptionist', 'expense_accounts', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('76', '10', 'Receptionist', 'doctor_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('77', '10', 'Receptionist', 'referral_commission', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('78', '10', 'Receptionist', 'profit_loss', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('79', '10', 'Receptionist', 'diagnostic_performance', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('80', '10', 'Receptionist', 'financial_audit_logs', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('81', '10', 'Receptionist', 'user_activity_reports', 'all', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('82', '11', 'admin', 'financial_dashboard', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('83', '11', 'admin', 'income_collection', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('84', '11', 'admin', 'expense_accounts', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('85', '11', 'admin', 'doctor_commission', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('86', '11', 'admin', 'referral_commission', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('87', '11', 'admin', 'profit_loss', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('88', '11', 'admin', 'diagnostic_performance', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('89', '11', 'admin', 'financial_audit_logs', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');
INSERT IGNORE INTO `role_permissions` (`id`, `group_id`, `group_name`, `permission_group`, `report_key`, `can_view`, `can_create`, `can_update`, `can_delete`, `can_print`, `can_export_pdf`, `can_export_excel`, `can_export_csv`, `can_view_details`, `can_view_sensitive`, `created_at`, `updated_at`) VALUES ('90', '11', 'admin', 'user_activity_reports', 'all', '1', '0', '0', '0', '1', '1', '1', '1', '1', '1', '2026-11-02 14:22:55', '2026-11-02 14:22:55');

-- ============================================================================
-- SECTION 4: CONSOLIDATE & STANDARDIZE DIAGNOSTIC CATEGORIES
-- (Pathology, Echo, Digital X-Ray, USG, ECG, Nebulization, Hormone)
-- ============================================================================

-- 1. Update payment_category items:
-- Merge Blood Test, Blood Examination, Stool Examination, Urine Examination into Pathology (ID 13)
UPDATE `payment_category` SET `payment_category` = '13', `payment_category_name` = 'Pathology' WHERE `payment_category` IN ('11', '13', '14', '15');

-- Merge Hormone & Others into Hormone (ID 21)
UPDATE `payment_category` SET `payment_category` = '21', `payment_category_name` = 'Hormone' WHERE `payment_category` IN ('20', '21');

-- Rename Ultrasonography items to USG (ID 17)
UPDATE `payment_category` SET `payment_category` = '17', `payment_category_name` = 'USG' WHERE `payment_category` = '17';

-- Standardize names for other categories
UPDATE `payment_category` SET `payment_category_name` = 'Digital X-Ray' WHERE `payment_category` = '16';
UPDATE `payment_category` SET `payment_category_name` = 'ECG' WHERE `payment_category` = '18';
UPDATE `payment_category` SET `payment_category_name` = 'Nebulization' WHERE `payment_category` = '19';
UPDATE `payment_category` SET `payment_category_name` = 'Echo' WHERE `payment_category` = '22';

-- 2. Update category master table (Ensuring the 7 standardized categories exist):
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (13, 'Pathology', 'Pathology', '98') ON DUPLICATE KEY UPDATE `category` = 'Pathology', `description` = 'Pathology';
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (16, 'Digital X-Ray', 'Digital X-Ray', '98') ON DUPLICATE KEY UPDATE `category` = 'Digital X-Ray', `description` = 'Digital X-Ray';
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (17, 'USG', 'USG', '98') ON DUPLICATE KEY UPDATE `category` = 'USG', `description` = 'USG';
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (18, 'ECG', 'ECG', '98') ON DUPLICATE KEY UPDATE `category` = 'ECG', `description` = 'ECG';
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (19, 'Nebulization', 'Nebulization', '98') ON DUPLICATE KEY UPDATE `category` = 'Nebulization', `description` = 'Nebulization';
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (21, 'Hormone', 'Hormone', '98') ON DUPLICATE KEY UPDATE `category` = 'Hormone', `description` = 'Hormone';
INSERT INTO `category` (`id`, `category`, `description`, `hospital_id`) VALUES (22, 'Echo', 'Echo', '98') ON DUPLICATE KEY UPDATE `category` = 'Echo', `description` = 'Echo';

-- 3. Delete redundant merged category headers for hospital 98:
DELETE FROM `category` WHERE `id` IN (11, 14, 15, 20) AND `hospital_id` = '98';