<?php

$host = "localhost:1521/xe";
$username = "CARRENTAL";
$password = "system";

// Establish connection
$conn = oci_connect($username, $password, $host);

if (!$conn) {
    $e = oci_error();
    die("Database connection failed: " . $e['message']);
}

// Set date format
$stmt = oci_parse($conn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
oci_execute($stmt);
?>