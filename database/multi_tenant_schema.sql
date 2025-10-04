-- Multi-Tenant SaaS Database Schema for MultiPOS
-- This schema adds tenant isolation to the existing POS system

-- Create tenants table (with error handling)
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(50) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `company_phone` varchar(20),
  `company_address` text,
  `subscription_plan` enum('basic','premium','enterprise') DEFAULT 'basic',
  `subscription_status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tenant_id` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add tenant_id column to existing tables (with error handling)
-- Admins table
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_NAME = 'admins' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Column tenant_id already exists in admins table"',
  'ALTER TABLE `admins` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for admins tenant_id
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_NAME = 'admins' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Index idx_tenant_id already exists in admins table"',
  'ALTER TABLE `admins` ADD INDEX `idx_tenant_id` (`tenant_id`)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Customers table
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_NAME = 'customers' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Column tenant_id already exists in customers table"',
  'ALTER TABLE `customers` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for customers tenant_id
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_NAME = 'customers' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Index idx_tenant_id already exists in customers table"',
  'ALTER TABLE `customers` ADD INDEX `idx_tenant_id` (`tenant_id`)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Categories table (if exists)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories' AND TABLE_SCHEMA = DATABASE()) > 0,
  (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'categories' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
    'SELECT "Column tenant_id already exists in categories table"',
    'ALTER TABLE `categories` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`'
  )),
  'SELECT "Categories table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for categories tenant_id (if table exists)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories' AND TABLE_SCHEMA = DATABASE()) > 0,
  (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'categories' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
    'SELECT "Index idx_tenant_id already exists in categories table"',
    'ALTER TABLE `categories` ADD INDEX `idx_tenant_id` (`tenant_id`)'
  )),
  'SELECT "Categories table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Products table (if exists)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'products' AND TABLE_SCHEMA = DATABASE()) > 0,
  (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'products' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
    'SELECT "Column tenant_id already exists in products table"',
    'ALTER TABLE `products` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`'
  )),
  'SELECT "Products table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for products tenant_id (if table exists)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'products' AND TABLE_SCHEMA = DATABASE()) > 0,
  (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'products' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
    'SELECT "Index idx_tenant_id already exists in products table"',
    'ALTER TABLE `products` ADD INDEX `idx_tenant_id` (`tenant_id`)'
  )),
  'SELECT "Products table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Orders table (if exists)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'orders' AND TABLE_SCHEMA = DATABASE()) > 0,
  (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'orders' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
    'SELECT "Column tenant_id already exists in orders table"',
    'ALTER TABLE `orders` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`'
  )),
  'SELECT "Orders table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for orders tenant_id (if table exists)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'orders' AND TABLE_SCHEMA = DATABASE()) > 0,
  (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'orders' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
    'SELECT "Index idx_tenant_id already exists in orders table"',
    'ALTER TABLE `orders` ADD INDEX `idx_tenant_id` (`tenant_id`)'
  )),
  'SELECT "Orders table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create tenant settings table
CREATE TABLE IF NOT EXISTS `tenant_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tenant_setting` (`tenant_id`, `setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default tenant for existing data migration
INSERT INTO `tenants` (`tenant_id`, `company_name`, `company_email`, `subscription_plan`) 
VALUES ('default', 'Default Company', 'admin@default.com', 'basic')
ON DUPLICATE KEY UPDATE `company_name` = VALUES(`company_name`);

-- Update existing records to use default tenant (with error handling)
UPDATE `admins` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL;
UPDATE `customers` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL;

-- Update categories if table exists
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories' AND TABLE_SCHEMA = DATABASE()) > 0,
  'UPDATE `categories` SET `tenant_id` = "default" WHERE `tenant_id` = "" OR `tenant_id` IS NULL',
  'SELECT "Categories table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update products if table exists
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'products' AND TABLE_SCHEMA = DATABASE()) > 0,
  'UPDATE `products` SET `tenant_id` = "default" WHERE `tenant_id` = "" OR `tenant_id` IS NULL',
  'SELECT "Products table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update orders if table exists
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'orders' AND TABLE_SCHEMA = DATABASE()) > 0,
  'UPDATE `orders` SET `tenant_id` = "default" WHERE `tenant_id` = "" OR `tenant_id` IS NULL',
  'SELECT "Orders table does not exist"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
