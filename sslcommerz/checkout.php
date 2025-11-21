<?php
// sslcommerz/checkout.php

// Temporary debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/function.php';
$config = include __DIR__ . '/../config/sslcommerz.php';

if (empty($_SESSION['pending_tenant'])) {
    $_SESSION['status'] = 'Session expired. Please register again.';
    header('Location: ../tenant-register.php');
    exit;
}

$tenant = $_SESSION['pending_tenant'];

// Map subscription plan to amount (BDT). Adjust values to match your pricing.
function mp_get_plan_amount(string $plan): float
{
    switch ($plan) {
        case 'basic':
            return 2900;   // e.g. 2900 BDT
        case 'premium':
            return 5900;   // e.g. 5900 BDT
        case 'enterprise':
            return 9900;   // e.g. 9900 BDT
        default:
            return 2900;
    }
}

$amount         = mp_get_plan_amount($tenant['subscription_plan'] ?? 'basic');
$transaction_id = uniqid('TENANT_', true);

$store_id     = $config['store_id'];
$store_passwd = $config['store_passwd'];

$post_data = [];
$post_data['store_id']      = $store_id;
$post_data['store_passwd']  = $store_passwd;
$post_data['total_amount']  = $amount;
$post_data['currency']      = $config['currency'];
$post_data['tran_id']       = $transaction_id;
$post_data['success_url']   = $config['success_url'];
$post_data['fail_url']      = $config['fail_url'];
$post_data['cancel_url']    = $config['cancel_url'];

// Backup: Save pending tenant data to file in case session is lost
$tempDir = __DIR__ . '/../temp_tenants';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}
file_put_contents($tempDir . '/' . $transaction_id . '.json', json_encode($tenant));

// Customer / company info
$post_data['cus_name']      = $tenant['company_name'] ?? 'N/A';
$post_data['cus_email']     = $tenant['company_email'] ?? 'no-reply@example.com';
$post_data['cus_add1']      = $tenant['company_address'] ?? 'N/A';
$post_data['cus_city']      = 'N/A';
$post_data['cus_postcode']  = 'N/A';
$post_data['cus_country']   = 'Bangladesh';
$cus_phone = $tenant['company_phone'] ?? '';

// Basic validation/fallback
if (empty($cus_phone) || strlen($cus_phone) < 11) {
    if ($config['is_sandbox']) {
        $cus_phone = '01711111111'; // Valid dummy for sandbox
    } else {
        echo "Error: A valid phone number is required for payment.";
        exit;
    }
}

$post_data['cus_phone']     = $cus_phone;

// Required by SSLCommerz for order/shipping info
$post_data['shipping_method']  = 'NO';               // no physical shipment
$post_data['num_of_item']      = '1';
$post_data['product_name']     = 'MultiPOS Subscription - ' . strtoupper($tenant['subscription_plan'] ?? 'BASIC');
$post_data['product_category'] = 'Software';
$post_data['product_profile']  = 'non-physical-goods';

// Optional parameters that might help with card transactions
$post_data['emi_option'] = 0;

// Improve data quality for Sandbox
if ($config['is_sandbox']) {
    if ($post_data['cus_city'] === 'N/A') $post_data['cus_city'] = 'Dhaka';
    if ($post_data['cus_postcode'] === 'N/A') $post_data['cus_postcode'] = '1000';
}

// Debug logging (Moved to capture ALL data)
$logFile = __DIR__ . '/ssl_debug.log';
$logData = "[" . date('Y-m-d H:i:s') . "] Request to SSLCommerz:\n" . print_r($post_data, true) . "\n-------------------\n";
file_put_contents($logFile, $logData, FILE_APPEND);

$direct_api_url = $config['is_sandbox']
    ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
    : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $direct_api_url);
curl_setopt($handle, CURLOPT_TIMEOUT, 30);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($handle, CURLOPT_POST, 1);
curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

$content = curl_exec($handle);

if ($content === false) {
    $error = curl_error($handle);
    curl_close($handle);
    echo 'Payment gateway connection failed (cURL error): ' . htmlspecialchars($error);
    exit;
}

curl_close($handle);

$sslcz = json_decode($content, true);

if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] !== '') {
    header('Location: ' . $sslcz['GatewayPageURL']);
    exit;
}

// Debug: show raw response so we can see why it failed
header('Content-Type: text/plain');
echo "Payment gateway connection failed! Raw response:\n";
echo $content;
