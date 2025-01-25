<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

include '../db/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception("Security validation failed");
    }

    // Validate required fields
    $required = ['name', 'pnum', 'type', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("All fields are required");
        }
    }

    // Hash password
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare insert statement
    $query = "INSERT INTO CARRENTAL.CLIENTS 
            (CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE, EMAIL, PASSWORD) 
            VALUES (:name, :pnum, :type, :email, :password)
            RETURNING CLIENT_ID INTO :client_id";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":name", $_POST['name']);
    oci_bind_by_name($stmt, ":pnum", $_POST['pnum']);
    oci_bind_by_name($stmt, ":type", $_POST['type']);
    oci_bind_by_name($stmt, ":email", $_POST['email']);
    oci_bind_by_name($stmt, ":password", $hashed_password);
    oci_bind_by_name($stmt, ":client_id", $client_id, 32);

    if (!oci_execute($stmt)) {
        throw new Exception("Registration failed");
    }

    $_SESSION['client_id'] = $client_id;
    $_SESSION['client_name'] = $_POST['name'];
    session_regenerate_id(true);
    header("Location: ../index.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../index.php");
    exit;
}
?>