<?php
// Database Migration Script for Multi-Tenant SaaS
// This script safely migrates your existing database to support multi-tenancy

require_once('../config/dbcon.php');

echo "<h2>Multi-Tenant Database Migration</h2>";
echo "<hr>";

// Function to execute SQL with error handling
function executeSQL($conn, $sql, $description) {
    echo "<p><strong>$description:</strong> ";
    
    if ($conn->query($sql)) {
        echo "<span style='color: green;'>✓ Success</span></p>";
        return true;
    } else {
        echo "<span style='color: red;'>✗ Error: " . $conn->error . "</span></p>";
        return false;
    }
}

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result->num_rows > 0;
}

// Function to check if table exists
function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result->num_rows > 0;
}

try {
    echo "<h3>Step 1: Creating tenants table</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS `tenants` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    executeSQL($conn, $sql, "Creating tenants table");

    echo "<h3>Step 2: Adding tenant_id to existing tables</h3>";
    
    // Add tenant_id to admins table
    if (!columnExists($conn, 'admins', 'tenant_id')) {
        executeSQL($conn, "ALTER TABLE `admins` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT 'default' AFTER `id`", 
                   "Adding tenant_id to admins table");
        executeSQL($conn, "ALTER TABLE `admins` ADD INDEX `idx_tenant_id` (`tenant_id`)", 
                   "Adding index to admins tenant_id");
    } else {
        echo "<p><strong>Admins table:</strong> <span style='color: orange;'>⚠ tenant_id column already exists</span></p>";
    }
    
    // Add tenant_id to customers table
    if (!columnExists($conn, 'customers', 'tenant_id')) {
        executeSQL($conn, "ALTER TABLE `customers` ADD COLUMN `tenant_id` varchar(50) NOT NULL DEFAULT 'default' AFTER `id`", 
                   "Adding tenant_id to customers table");
        executeSQL($conn, "ALTER TABLE `customers` ADD INDEX `idx_tenant_id` (`tenant_id`)", 
                   "Adding index to customers tenant_id");
    } else {
        echo "<p><strong>Customers table:</strong> <span style='color: orange;'>⚠ tenant_id column already exists</span></p>";
    }

    echo "<h3>Step 3: Creating tenant settings table</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS `tenant_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tenant_id` varchar(50) NOT NULL,
        `setting_key` varchar(100) NOT NULL,
        `setting_value` text,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_tenant_setting` (`tenant_id`, `setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    executeSQL($conn, $sql, "Creating tenant_settings table");

    echo "<h3>Step 4: Inserting default tenant</h3>";
    executeSQL($conn, "INSERT IGNORE INTO `tenants` (`tenant_id`, `company_name`, `company_email`, `subscription_plan`) VALUES ('default', 'Default Company', 'admin@default.com', 'basic')", 
               "Inserting default tenant");

    echo "<h3>Step 5: Updating existing data</h3>";
    executeSQL($conn, "UPDATE `admins` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL", 
               "Updating existing admins");
    executeSQL($conn, "UPDATE `customers` SET `tenant_id` = 'default' WHERE `tenant_id` = '' OR `tenant_id` IS NULL", 
               "Updating existing customers");

    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Migration completed successfully!</h3>";
    echo "<p>Your database is now ready for multi-tenant SaaS functionality.</p>";
    echo "<p><a href='../index.php'>← Back to Application</a></p>";

} catch (Exception $e) {
    echo "<hr>";
    echo "<h3 style='color: red;'>❌ Migration failed!</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
