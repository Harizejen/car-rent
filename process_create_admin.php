<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the Oracle database connection file
include 'db/db.php'; // Ensure this file uses OCI

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validate input
    if (empty($username) || empty($password) || empty($email)) {
        header("Location: create_admin.php?error=All fields are required.");
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new admin into the database
    $sql = "INSERT INTO ADMIN (USERNAME, PASSWORD_HASH, EMAIL) VALUES (:username, :password_hash, :email)";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        header("Location: create_admin.php?error=Prepare failed: " . $e['message']);
        exit();
    }

    // Bind parameters
    oci_bind_by_name($stmt, ":username", $username);
    oci_bind_by_name($stmt, ":password_hash", $password_hash);
    oci_bind_by_name($stmt, ":email", $email);

    // Execute the query
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        header("Location: create_admin.php?error=Execute failed: " . $e['message']);
        exit();
    }

    // Success: Redirect with a success message
    header("Location: create_admin.php?success=1");
    exit();
}

// Close the connection
oci_close($conn);
?>