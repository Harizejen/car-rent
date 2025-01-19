<?php

$host = "localhost:1521/xe"; // Replace with your Oracle service name
$username = "CARRENTAL"; // Oracle username
$password = "system"; // Oracle password

// Establish connection
$conn = oci_connect($username, $password, $host);

oci_execute($conn,"ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
// Check if the connection was successful
if (!$dbconn) {
    $e = oci_error(); // Get the error message
    echo "Connection failed: " . htmlentities($e['message']);
    exit; // Stop execution if the connection fails
}
?>