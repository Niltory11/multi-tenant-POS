-- Migration script to add tenant_id to expenses and order_items tables
-- This fixes the issue where dashboard shows data from all tenants

-- Add tenant_id column to expenses table
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_NAME = 'expenses' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Column tenant_id already exists in expenses table"',
  'ALTER TABLE `expenses` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for expenses tenant_id
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_NAME = 'expenses' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Index idx_tenant_id already exists in expenses table"',
  'ALTER TABLE `expenses` ADD INDEX `idx_tenant_id` (`tenant_id`)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add tenant_id column to order_items table
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_NAME = 'order_items' AND COLUMN_NAME = 'tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Column tenant_id already exists in order_items table"',
  'ALTER TABLE `order_items` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT "default"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for order_items tenant_id
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
   WHERE TABLE_NAME = 'order_items' AND INDEX_NAME = 'idx_tenant_id' AND TABLE_SCHEMA = DATABASE()) > 0,
  'SELECT "Index idx_tenant_id already exists in order_items table"',
  'ALTER TABLE `order_items` ADD INDEX `idx_tenant_id` (`tenant_id`)'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing records to use default tenant
UPDATE `expenses` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL;
UPDATE `order_items` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL;

-- Show completion message
SELECT 'Migration completed successfully! Added tenant_id to expenses and order_items tables.' as status;
