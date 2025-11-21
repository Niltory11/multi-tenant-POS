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
    
    // Instead of creating tenant immediately, store data in session
    // and redirect to SSLCommerz checkout for payment.

    $_SESSION['pending_tenant'] = [
        'company_name'      => $company_name,
        'company_email'     => $company_email,
        'company_phone'     => $company_phone,
        'company_address'   => $company_address,
        'subscription_plan' => $subscription_plan,
        'admin_name'        => $admin_name,
        'admin_email'       => $admin_email,
        'admin_password'    => $admin_password, // will be hashed after payment success
        'admin_phone'       => $admin_phone,
    ];

    header('Location: sslcommerz/checkout.php');
    exit();

} else {
    $_SESSION['status'] = "Unauthorized Access!";
    header("Location: tenant-register.php");
    exit();
}
?>
