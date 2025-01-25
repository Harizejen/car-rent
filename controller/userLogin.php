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

    if (empty($_POST['email']) || empty($_POST['password'])) {
        throw new Exception("Email and password are required");
    }

    $query = "SELECT CLIENT_ID, CLIENT_NAME, PASSWORD 
            FROM CARRENTAL.CLIENTS 
            WHERE EMAIL = :email";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":email", $_POST['email']);
    oci_execute($stmt);

    if ($user = oci_fetch_assoc($stmt)) {
        if (password_verify($_POST['password'], $user['PASSWORD'])) {
            $_SESSION['client_id'] = $user['CLIENT_ID'];
            $_SESSION['client_name'] = $user['CLIENT_NAME'];
            session_regenerate_id(true);
            header("Location: ../index.php");
            exit;
        } else {
            throw new Exception("Invalid credentials");
        }
    } else {
        throw new Exception("User not found");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../index.php");
    exit;
}
?>