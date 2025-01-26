<?php
include 'db/db.php'; // Include the Oracle connection

if (isset($_POST['driver_id'])) {
    $driver_id = $_POST['driver_id'];

    // Query to fetch driver details based on driver_id
    $sql = "SELECT DRIVER_NAME, DRIVER_PNUM, RATING, LICENSE_NUM, STATUS_ID, RATE, ONLEAVE_RATE 
            FROM DRIVERS 
            WHERE DRIVER_ID = :driver_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':driver_id', $driver_id);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        // Return driver details as JSON
        echo json_encode($row);
    } else {
        echo json_encode(array()); // Return empty JSON if no data found
    }
}
?>