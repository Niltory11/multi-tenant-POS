<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config/function.php';

if (isset($_POST['registerTenant'])) {
    // Validate and sanitize input data
    $company_name = validate($_POST['company_name']);
    $company_email = validate($_POST['company_email']);
    $company_phone = validate($_POST['company_phone']);
    $company_address = validate($_POST['company_address']);
    $subscription_plan = validate($_POST['subscription_plan']);
    
    $admin_name = validate($_POST['admin_name']);
    $admin_email = validate($_POST['admin_email']);
    $admin_password = validate($_POST['admin_password']);
    $admin_phone = validate($_POST['admin_phone']);
    
    // Check if required fields are filled
    if (empty($company_name) || empty($company_email) || empty($subscription_plan) || 
        empty($admin_name) || empty($admin_email) || empty($admin_password)) {
        $_SESSION['status'] = "Please fill all required fields!";
        header("Location: tenant-register.php");
        exit();
    }
    
    // Check if company email already exists
    $checkCompanyEmail = "SELECT * FROM tenants WHERE company_email='$company_email' LIMIT 1";
    $result = mysqli_query($conn, $checkCompanyEmail);
    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['status'] = "Company email already registered!";
        header("Location: tenant-register.php");
        exit();
    }
    
    // Check if admin email already exists
    $checkAdminEmail = "SELECT * FROM admins WHERE email='$admin_email' LIMIT 1";
    $result = mysqli_query($conn, $checkAdminEmail);
    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['status'] = "Admin email already exists!";
        header("Location: tenant-register.php");
        exit();
    }
    
    // Generate user-friendly tenant ID from company name
    $company_slug = strtolower(trim($company_name));
    $company_slug = preg_replace('/[^a-z0-9]+/', '-', $company_slug);
    $company_slug = trim($company_slug, '-');
    $company_slug = substr($company_slug, 0, 20); // Limit length
    
    // Check if slug already exists and add suffix if needed
    $base_tenant_id = $company_slug;
    $tenant_id = $base_tenant_id;
    $counter = 1;
    
    while (true) {
        $checkTenantId = "SELECT * FROM tenants WHERE tenant_id='$tenant_id' LIMIT 1";
        $result = mysqli_query($conn, $checkTenantId);
        
        if ($result && mysqli_num_rows($result) == 0) {
            break; // Tenant ID is unique
        }
        
        $tenant_id = $base_tenant_id . '-' . $counter;
        $counter++;
        
        if ($counter > 999) { // Safety limit
            $tenant_id = $base_tenant_id . '-' . time();
            break;
        }
    }
    
    // Hash the admin password
    $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert tenant data
        $tenantData = [
            'tenant_id' => $tenant_id,
            'company_name' => $company_name,
            'company_email' => $company_email,
            'company_phone' => $company_phone,
            'company_address' => $company_address,
            'subscription_plan' => $subscription_plan,
            'subscription_status' => 'active'
        ];
        
        $tenantResult = insert('tenants', $tenantData);
        if (!$tenantResult) {
            throw new Exception("Failed to create tenant");
        }
        
        // Insert admin data
        $adminData = [
            'tenant_id' => $tenant_id,
            'name' => $admin_name,
            'email' => $admin_email,
            'password' => $hashed_password,
            'phone' => $admin_phone,
            'role' => 'admin',
            'is_ban' => 0
        ];
        
        $adminResult = insert('admins', $adminData);
        if (!$adminResult) {
            throw new Exception("Failed to create admin");
        }
        
        // Create default tenant settings
        $defaultSettings = [
            ['tenant_id' => $tenant_id, 'setting_key' => 'currency', 'setting_value' => 'USD'],
            ['tenant_id' => $tenant_id, 'setting_key' => 'timezone', 'setting_value' => 'UTC'],
            ['tenant_id' => $tenant_id, 'setting_key' => 'date_format', 'setting_value' => 'Y-m-d'],
            ['tenant_id' => $tenant_id, 'setting_key' => 'company_logo', 'setting_value' => '']
        ];
        
        foreach ($defaultSettings as $setting) {
            insert('tenant_settings', $setting);
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Auto-login the admin
        $_SESSION['loggedIn'] = true;
        $_SESSION['loggedInUser'] = [
            'user_id' => mysqli_insert_id($conn),
            'name' => $admin_name,
            'email' => $admin_email,
            'role' => 'admin',
            'tenant_id' => $tenant_id
        ];
        
        // Redirect to admin dashboard
        header("Location: admin/index.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['status'] = "Registration failed: " . $e->getMessage();
        header("Location: tenant-register.php");
        exit();
    }
    
} else {
    $_SESSION['status'] = "Unauthorized Access!";
    header("Location: tenant-register.php");
    exit();
}
?>
