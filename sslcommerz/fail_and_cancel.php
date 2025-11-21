<?php
// sslcommerz/fail_and_cancel.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optionally clear pending tenant data so user starts over
// unset($_SESSION['pending_tenant']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
</head>
<body>
    <h2>❌ Payment failed or cancelled.</h2>
    <p>Please try again. If the problem persists, contact support.</p>
    <p><a href="../tenant-register.php">Back to Registration</a></p>
</body>
</html>
