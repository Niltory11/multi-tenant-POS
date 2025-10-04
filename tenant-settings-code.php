<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config/function.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedInUser']['role'] !== 'admin') {
    $_SESSION['status'] = "Access denied! Admin privileges required.";
    header("Location: login.php");
    exit();
}

if (isset($_POST['updateTenant'])) {
    $tenant_id = $_SESSION['loggedInUser']['tenant_id'];
    
    // Validate and sanitize input data
    $company_name = validate($_POST['company_name']);
    $company_email = validate($_POST['company_email']);
    $company_phone = validate($_POST['company_phone']);
    $company_address = validate($_POST['company_address']);
    $subscription_plan = validate($_POST['subscription_plan']);
    
    // Check if required fields are filled
    if (empty($company_name) || empty($company_email)) {
        $_SESSION['status'] = "Company name and email are required!";
        header("Location: tenant-settings.php");
        exit();
    }
    
    // Check if company email already exists for another tenant
    $checkEmail = "SELECT * FROM tenants WHERE company_email='$company_email' AND tenant_id != '$tenant_id' LIMIT 1";
    $result = mysqli_query($conn, $checkEmail);
    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['status'] = "Company email already exists for another company!";
        header("Location: tenant-settings.php");
        exit();
    }
    
    try {
        // Update tenant data
        $updateData = [
            'company_name' => $company_name,
            'company_email' => $company_email,
            'company_phone' => $company_phone,
            'company_address' => $company_address,
            'subscription_plan' => $subscription_plan
        ];
        
        // Custom update for tenants table using tenant_id instead of id
        $updateString = "";
        foreach ($updateData as $column => $value) {
            $updateString .= "`$column` = '" . mysqli_real_escape_string($conn, $value) . "', ";
        }
        $updateString = rtrim($updateString, ', ');
        
        $query = "UPDATE tenants SET $updateString WHERE tenant_id = '$tenant_id'";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            $_SESSION['status'] = 'Company information updated successfully!';
            header("Location: tenant-settings.php");
            exit();
        } else {
            $_SESSION['status'] = 'Failed to update company information!';
            header("Location: tenant-settings.php");
            exit();
        }
        
    } catch (Exception $e) {
        $_SESSION['status'] = "Update failed: " . $e->getMessage();
        header("Location: tenant-settings.php");
        exit();
    }
    
} else {
    $_SESSION['status'] = "Invalid request!";
    header("Location: tenant-settings.php");
    exit();
}
?>
