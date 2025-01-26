<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare Oracle statement
    $stmt = oci_parse($conn, "SELECT ADMIN_ID, PASSWORD_HASH FROM ADMIN WHERE USERNAME = :username");
    
    // Bind parameters
    oci_bind_by_name($stmt, ":username", $username);
    
    // Execute statement
    oci_execute($stmt);
    
    // Fetch result
    $row = oci_fetch_assoc($stmt);
    
    if ($row && password_verify($password, $row['PASSWORD_HASH'])) {
        // Login successful
        $_SESSION['admin_id'] = $row['ADMIN_ID'];
        header("Location: dashboard.php");
        exit();
    } else {
        // Login failed
        header("Location: adminLogin.php?error=1");
        exit();
    }

    // Free statement resource
    oci_free_statement($stmt);
}

// Close connection
oci_close($conn);
?>