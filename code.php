<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config/function.php');

// Check if user is logged in (for existing admin creation)
if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['status'] = "Please login to create admin accounts!";
    header("Location: login.php");
    exit();
}

// Add Admin (for logged-in users creating additional admins)
if (isset($_POST['saveAdmin'])) {
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = password_hash(validate($_POST['password']), PASSWORD_BCRYPT);
    $phone = validate($_POST['phone']);
    $role = validate($_POST['role']);
    $tenant_id = $_SESSION['loggedInUser']['tenant_id']; // Get from session

    if (!empty($name) && !empty($email) && !empty($password)) {
        // Check if email already exists in the same tenant
        $checkEmail = "SELECT * FROM admins WHERE email='$email' AND tenant_id='$tenant_id' LIMIT 1";
        $result = mysqli_query($conn, $checkEmail);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $_SESSION['status'] = "Email already exists in your organization!";
            header("Location: admins-create.php");
            exit();
        }
        
        $data = [
            'tenant_id' => $tenant_id,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role' => $role,
            'is_ban' => 0
        ];

        $result = insert('admins', $data);
        if ($result) {
            $_SESSION['status'] = 'Admin/Staff Created Successfully!';
            header("Location: admins.php");
            exit();
        } else {
            $_SESSION['status'] = 'Something Went Wrong!';
            header("Location: admins-create.php");
            exit();
        }
    } else {
        $_SESSION['status'] = 'Please fill all required fields.';
        header("Location: admins-create.php");
        exit();
    }
}

// Redirect to login if no valid action
$_SESSION['status'] = "Invalid request!";
header("Location: login.php");
exit();
?>
