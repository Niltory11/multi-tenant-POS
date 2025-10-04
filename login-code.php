<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config/function.php';

if (isset($_POST['loginBtn'])) {
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Query to fetch user based on email only (auto-detect role)
        $query = "SELECT * FROM admins WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];
            $userRole = $row['role']; // Get role from database
            
            // Check if role exists and is not empty
            if (empty($userRole) || $userRole === null) {
                $_SESSION['status'] = "User role not found in database!";
                header("Location: login.php");
                exit();
            }

            // Check password
            if (!password_verify($password, $hashedPassword)) {
                $_SESSION['status'] = "Invalid Password!";
                header("Location: login.php");
                exit();
            }

            // Check if the user is banned
            if ($row['is_ban'] == 1) {
                $_SESSION['status'] = "Your account has been banned. Contact the admin.";
                header("Location: login.php");
                exit();
            }

            // Successful login
            $_SESSION['loggedIn'] = true;
            $_SESSION['loggedInUser'] = [
                'user_id' => $row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'role' => $userRole, // Use role from database
            ];

            // Redirect based on role from database
            switch (strtolower(trim($userRole))) {
                case 'admin':
                    header("Location: admin/index.php");
                    break;
                case 'manager':
                    header("Location: manager/index.php");
                    break;
                case 'salesman':
                    header("Location: salesman/index.php");
                    break;
                default:
                    $_SESSION['status'] = "Invalid role in database! Role found: '" . $userRole . "'";
                    header("Location: login.php");
            }
            exit();
        } else {
            $_SESSION['status'] = "Invalid Email or Account not found.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "All fields are mandatory!";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['status'] = "Unauthorized Access!";
    header("Location: login.php");
    exit();
}
?>
