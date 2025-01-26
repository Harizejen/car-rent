<?php
include 'db/db.php'; // Include the Oracle connection

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Query to fetch booking details based on booking_id
    $sql = "SELECT BOOKING_DATE, PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, CLIENT_ID, VEHICLE_ID, STATUS_ID, DURATION 
            FROM BOOKINGS 
            WHERE BOOKING_ID = :booking_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':booking_id', $booking_id);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        // Return booking details as JSON
        echo json_encode($row);
    } else {
        echo json_encode(array()); // Return empty JSON if no data found
    }
}
?>