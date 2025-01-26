<?php
session_start();
require '../db/db.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}

// CSRF Validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['dashboard_csrf_token']) {
    die("CSRF validation failed.");
}

$bookingId = $_POST['booking_id'];
$clientId = $_SESSION['client_id'];

try {
    // 1. Verify booking ownership and status
    $checkStmt = oci_parse($conn, "
        SELECT b.BOOKING_ID 
        FROM CARRENTAL.BOOKINGS b
        WHERE b.BOOKING_ID = :booking_id
        AND b.CLIENT_ID = :client_id
        AND b.STATUS_ID IN (10, 11, 12)
    ");
    oci_bind_by_name($checkStmt, ":booking_id", $bookingId);
    oci_bind_by_name($checkStmt, ":client_id", $clientId);
    oci_execute($checkStmt);

    if (!oci_fetch($checkStmt)) {
        $_SESSION['error'] = "Cannot cancel this booking.";
        header("Location: ../userDashboard.php");
        exit();
    }

    // 2. Get Cancelled status ID
    $statusStmt = oci_parse($conn, "
        SELECT STATUS_ID FROM CARRENTAL.STATUS 
        WHERE STATUS_TYPE = 'BOOKING' AND STATUS_DESC_TMP = 'Cancelled'
    ");
    oci_execute($statusStmt);
    $cancelledStatus = oci_fetch_assoc($statusStmt);

    if (!$cancelledStatus) {
        $_SESSION['error'] = "System error: Cancelled status not found.";
        header("Location: ../userDashboard.php");
        exit();
    }

    // 3. Update booking status
    $updateStmt = oci_parse($conn, "
        UPDATE CARRENTAL.BOOKINGS 
        SET STATUS_ID = :status_id 
        WHERE BOOKING_ID = :booking_id
    ");
    oci_bind_by_name($updateStmt, ":status_id", $cancelledStatus['STATUS_ID']);
    oci_bind_by_name($updateStmt, ":booking_id", $bookingId);

    if (!oci_execute($updateStmt)) {
        $e = oci_error($updateStmt);
        error_log("Cancellation failed: ".$e['message']);
        $_SESSION['error'] = "Cancellation failed. Please try again.";
    } else {
        $_SESSION['message'] = "Booking #$bookingId cancelled successfully.";
    }

    header("Location: ../userDashboard.php");
    exit();

} catch (Exception $e) {
    error_log("Cancellation error: ".$e->getMessage());
    $_SESSION['error'] = "An error occurred.";
    header("Location: ../userDashboard.php");
    exit();
}
?>