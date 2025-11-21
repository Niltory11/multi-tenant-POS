<?php

// SSLCommerz configuration for tenant subscription payments
// Fill in your real store credentials from the SSLCommerz dashboard/email.

return [
    // Store credentials
'store_id'     => 'space690dbf2e094ab',          // from SSLCommerz
    'store_passwd' => 'space690dbf2e094ab@ssl',      // API / Secret key

    // Environment
    'is_sandbox'   => true,  // true for sandbox/testing, false for live
    'currency'     => 'BDT',

    // Callback URLs (adjust to your actual domain / path)
    'success_url'  => 'http://localhost/multiPOS/sslcommerz/success.php',
    'fail_url'     => 'http://localhost/multiPOS/sslcommerz/fail_and_cancel.php',
    'cancel_url'   => 'http://localhost/multiPOS/sslcommerz/fail_and_cancel.php',
    'ipn_url'      => 'http://localhost/multiPOS/sslcommerz/sslcommerz_ipn.php',
];
