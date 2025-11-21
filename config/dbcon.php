<?php
$host = "localhost";
$port = 3307;
$user = "root"; // Replace with your username
$password = " "; // Replace with your password if needed
$dbname = "multi-pos";

// Create a connection
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Note: do not echo anything here; it breaks JSON responses and redirects.
?>
