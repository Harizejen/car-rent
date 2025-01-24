<?php

$host = "localhost:1521/xe"; // Replace with your Oracle service name
$username = "CARRENTAL"; // Oracle username
$password = "system"; // Oracle password

// Establish connection
$conn = oci_connect($username, $password, $host);

// Check if the connection was successful
if (!$conn) {
    $e = oci_error(); // Get the error message
    echo "Connection failed: " . htmlentities($e['message']);
    exit; // Stop execution if the connection fails
}

// Set the NLS_DATE_FORMAT for the session
$sql = "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'";
$stmt = oci_parse($conn, $sql); // Prepare the SQL statement
if (!oci_execute($stmt)) { // Execute the prepared statement
    $e = oci_error($stmt);
    echo "Error setting date format: " . htmlentities($e['message']);
}

// You can now use $conn for further database operations
?>