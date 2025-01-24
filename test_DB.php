<?php
include 'db/db.php';
$testQuery = "SELECT SYSDATE FROM DUAL";
$stmt = oci_parse($conn, $testQuery);
if (oci_execute($stmt)) {
    $row = oci_fetch_assoc($stmt);
    echo "Database connection successful! Current date: " . $row['SYSDATE'];
} else {
    $e = oci_error($stmt);
    echo "Connection failed: " . $e['message'];
}
?>