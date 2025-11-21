<?php
// sslcommerz/success.php

// Temporary debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/function.php';
$config = include __DIR__ . '/../config/sslcommerz.php';

$logFile = __DIR__ . '/ssl_success_debug.log';
function logSuccess($msg) {
    global $logFile;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n", FILE_APPEND);
}

logSuccess("Success page hit. POST: " . print_r($_POST, true));

if (!isset($_POST['status']) || $_POST['status'] !== 'VALID') {
    logSuccess("Payment not valid. Status: " . ($_POST['status'] ?? 'N/A'));
    echo '❌ Payment not valid.';
    exit;
}

if (empty($_SESSION['pending_tenant'])) {
    // Try to recover from backup file using tran_id
    $tran_id_input = $_POST['tran_id'] ?? '';
    $backupFile = __DIR__ . '/../temp_tenants/' . $tran_id_input . '.json';

    if ($tran_id_input && file_exists($backupFile)) {
        logSuccess("Recovering tenant data from backup file: " . $backupFile);
        $_SESSION['pending_tenant'] = json_decode(file_get_contents($backupFile), true);
    } else {
        logSuccess("Session 'pending_tenant' is empty and no backup found. Session ID: " . session_id());
        $_SESSION['status'] = 'No tenant data found after payment. Please register again.';
        header('Location: ../tenant-register.php');
        exit;
    }
}

$tenant = $_SESSION['pending_tenant'];
logSuccess("Pending tenant found: " . $tenant['company_email']);

$amount  = $_POST['amount'] ?? '';
$val_id  = $_POST['val_id'] ?? '';
$tran_id = $_POST['tran_id'] ?? '';

$store_id     = $config['store_id'];
$store_passwd = $config['store_passwd'];

$validation_url = 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id='
    . urlencode($val_id)
    . '&store_id=' . urlencode($store_id)
    . '&store_passwd=' . urlencode($store_passwd)
    . '&v=1&format=json';

$response = @file_get_contents($validation_url);
$result   = $response ? json_decode($response, true) : null;

if (!is_array($result) || !isset($result['status']) || !in_array($result['status'], ['VALID', 'VALIDATED'], true)) {
    logSuccess("Validation API failed. Response: " . $response);
    echo '❌ Payment validation failed!';
    exit;
}

logSuccess("Validation successful. Creating tenant...");

$company_name      = validate($tenant['company_name']);
$company_email     = validate($tenant['company_email']);
$company_phone     = validate($tenant['company_phone']);
$company_address   = validate($tenant['company_address']);
$subscription_plan = validate($tenant['subscription_plan']);

$admin_name        = validate($tenant['admin_name']);
$admin_email       = validate($tenant['admin_email']);
$admin_password    = $tenant['admin_password']; // plain; will be hashed below
$admin_phone       = validate($tenant['admin_phone']);

// Duplicate checks (defensive – main checks already in tenant-register-code.php)
$checkCompanyEmail = "SELECT * FROM tenants WHERE company_email='$company_email' LIMIT 1";
$resultCompany     = mysqli_query($conn, $checkCompanyEmail);
if ($resultCompany && mysqli_num_rows($resultCompany) > 0) {
    $_SESSION['status'] = 'Company email already registered!';
    header('Location: ../tenant-register.php');
    exit;
}

$checkAdminEmail = "SELECT * FROM admins WHERE email='$admin_email' LIMIT 1";
$resultAdmin     = mysqli_query($conn, $checkAdminEmail);
if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
    $_SESSION['status'] = 'Admin email already exists!';
    header('Location: ../tenant-register.php');
    exit;
}

// Generate tenant_id (same strategy as original)
$company_slug = strtolower(trim($company_name));
$company_slug = preg_replace('/[^a-z0-9]+/', '-', $company_slug);
$company_slug = trim($company_slug, '-');
$company_slug = substr($company_slug, 0, 20);

$base_tenant_id = $company_slug;
$tenant_id      = $base_tenant_id;
$counter        = 1;

while (true) {
    $checkTenantId = "SELECT * FROM tenants WHERE tenant_id='$tenant_id' LIMIT 1";
    $resTenantId   = mysqli_query($conn, $checkTenantId);

    if ($resTenantId && mysqli_num_rows($resTenantId) == 0) {
        break;
    }

    $tenant_id = $base_tenant_id . '-' . $counter;
    $counter++;

    if ($counter > 999) {
        $tenant_id = $base_tenant_id . '-' . time();
        break;
    }
}

$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

mysqli_begin_transaction($conn);

try {
    // Insert tenant
    $tenantData = [
        'tenant_id'           => $tenant_id,
        'company_name'        => $company_name,
        'company_email'       => $company_email,
        'company_phone'       => $company_phone,
        'company_address'     => $company_address,
        'subscription_plan'   => $subscription_plan,
        'subscription_status' => 'active',
    ];

    $tenantResult = insert('tenants', $tenantData);
    if (!$tenantResult) {
        logSuccess("Failed to insert tenant: " . mysqli_error($conn));
        throw new Exception('Failed to create tenant');
    }
    logSuccess("Tenant inserted: $tenant_id");

    // Insert admin
    $adminData = [
        'tenant_id' => $tenant_id,
        'name'      => $admin_name,
        'email'     => $admin_email,
        'password'  => $hashed_password,
        'phone'     => $admin_phone,
        'role'      => 'admin',
        'is_ban'    => 0,
    ];

    $adminResult = insert('admins', $adminData);
    if (!$adminResult) {
        logSuccess("Failed to insert admin: " . mysqli_error($conn));
        throw new Exception('Failed to create admin');
    }
    logSuccess("Admin inserted.");

    // Default tenant settings
    $defaultSettings = [
        ['tenant_id' => $tenant_id, 'setting_key' => 'currency',    'setting_value' => 'USD'],
        ['tenant_id' => $tenant_id, 'setting_key' => 'timezone',    'setting_value' => 'UTC'],
        ['tenant_id' => $tenant_id, 'setting_key' => 'date_format', 'setting_value' => 'Y-m-d'],
        ['tenant_id' => $tenant_id, 'setting_key' => 'company_logo','setting_value' => ''],
    ];

    foreach ($defaultSettings as $setting) {
        insert('tenant_settings', $setting);
    }

    mysqli_commit($conn);
    logSuccess("Transaction committed. Logging in user...");

    $_SESSION['loggedIn'] = true;
    $_SESSION['loggedInUser'] = [
        'user_id'   => mysqli_insert_id($conn),
        'name'      => $admin_name,
        'email'     => $admin_email,
        'role'      => 'admin',
        'tenant_id' => $tenant_id,
    ];

    unset($_SESSION['pending_tenant']);

    // Cleanup backup file
    $backupFile = __DIR__ . '/../temp_tenants/' . $tran_id . '.json';
    if (file_exists($backupFile)) {
        unlink($backupFile);
    }

    logSuccess("Redirecting to admin/index.php");
    header('Location: ../admin/index.php');
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    logSuccess("Exception caught: " . $e->getMessage());
    $_SESSION['status'] = 'Registration failed after successful payment: ' . $e->getMessage();
    header('Location: ../tenant-register.php');
    exit;
}
