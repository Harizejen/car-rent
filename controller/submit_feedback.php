<?php
include 'db/db.php';
session_start();

if (!isset($_SESSION['client_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

$bookingId = $_POST['booking_id'];
$driverId = $_POST['driver_id'];
$rating = $_POST['rating'];
$comments = $_POST['comments'];

// Verify the booking belongs to the client
$checkQuery = "SELECT b.BOOKING_ID 
               FROM CARRENTAL.BOOKINGS b
               WHERE b.BOOKING_ID = :booking_id
               AND b.CLIENT_ID = :client_id";

$stmt = oci_parse($conn, $checkQuery);
oci_bind_by_name($stmt, ":booking_id", $bookingId);
oci_bind_by_name($stmt, ":client_id", $_SESSION['client_id']);
oci_execute($stmt);

if (!oci_fetch_assoc($stmt)) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

// Insert feedback
$insertQuery = "INSERT INTO CARRENTAL.FEEDBACKS (FEEDBACK_ID, COMMENTS, RATINGVALUE, BOOKING_ID, DRIVER_ID)
                VALUES (FEEDBACKS_SEQ.NEXTVAL, :comments, :rating, :booking_id, :driver_id)";

$stmt = oci_parse($conn, $insertQuery);
oci_bind_by_name($stmt, ":comments", $comments);
oci_bind_by_name($stmt, ":rating", $rating);
oci_bind_by_name($stmt, ":booking_id", $bookingId);
oci_bind_by_name($stmt, ":driver_id", $driverId);

if (oci_execute($stmt)) {
    header("Location: dashboard.php");
} else {
    header("Location: dashboard.php?error=feedback");
}
exit();
?>