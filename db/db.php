<?php

$host = "localhost:1521/xe"; // Replace with your Oracle service name
$username = "CARRENTAL"; // Oracle username
$password = "SYSTEM"; // Oracle password

// Establish connection
$conn = oci_connect($username, $password, $host);

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}
?>
