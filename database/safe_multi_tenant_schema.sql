-- Safe Multi-Tenant SaaS Database Schema for MultiPOS
-- This is a safer version that handles existing database state

-- Step 1: Create tenants table
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 2: Add unique constraint to tenant_id (only if not exists)
ALTER TABLE `tenants` ADD UNIQUE KEY `unique_tenant_id` (`tenant_id`);

-- Step 3: Check and add tenant_id column to admins table
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'admins' 
AND COLUMN_NAME = 'tenant_id' 
AND TABLE_SCHEMA = DATABASE();

SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE `admins` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`',
  'SELECT "Column tenant_id already exists in admins table"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Add index for admins tenant_id
SELECT COUNT(*) INTO @idx_exists 
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_NAME = 'admins' 
AND INDEX_NAME = 'idx_tenant_id' 
AND TABLE_SCHEMA = DATABASE();

SET @sql = IF(@idx_exists = 0, 
  'ALTER TABLE `admins` ADD INDEX `idx_tenant_id` (`tenant_id`)',
  'SELECT "Index idx_tenant_id already exists in admins table"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Check and add tenant_id column to customers table
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'customers' 
AND COLUMN_NAME = 'tenant_id' 
AND TABLE_SCHEMA = DATABASE();

SET @sql = IF(@col_exists = 0, 
  'ALTER TABLE `customers` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default" AFTER `id`',
  'SELECT "Column tenant_id already exists in customers table"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6: Add index for customers tenant_id
SELECT COUNT(*) INTO @idx_exists 
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_NAME = 'customers' 
AND INDEX_NAME = 'idx_tenant_id' 
AND TABLE_SCHEMA = DATABASE();

SET @sql = IF(@idx_exists = 0, 
  'ALTER TABLE `customers` ADD INDEX `idx_tenant_id` (`tenant_id`)',
  'SELECT "Index idx_tenant_id already exists in customers table"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 7: Create tenant settings table
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

-- Step 8: Insert default tenant
INSERT IGNORE INTO `tenants` (`tenant_id`, `company_name`, `company_email`, `subscription_plan`) 
VALUES ('default', 'Default Company', 'admin@default.com', 'basic');

-- Step 9: Update existing records to use default tenant
UPDATE `admins` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL;
UPDATE `customers` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL;
