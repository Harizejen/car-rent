<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);
require_once '../db/db.php';

// CSRF Validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Security validation failed";
    header("Location: ../index.php");
    exit;
}

if (!isset($_SESSION['client_id'])) {
    $_SESSION['error'] = "You must be logged in to make a booking.";
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['booking'])) {
    try {
        // Retrieve and validate form data
        $client_id = $_SESSION['client_id'];
        $client_pnum = trim($_POST['client_pnum']);
        $client_type = trim($_POST['client_type']);
        $pickup_location = trim($_POST['pickup_location']);
        $dropoff_location = trim($_POST['dropoff_location']);
        $vehicle_id = trim($_POST['vehicle_id']);
        $booking_date = trim($_POST['booking_date']);
        $return_date = trim($_POST['return_date']);

        // Validate required fields
        $required_fields = [
            'Phone Number' => $client_pnum,
            'Client Type' => $client_type,
            'Pickup Location' => $pickup_location,
            'Dropoff Location' => $dropoff_location,
            'Vehicle' => $vehicle_id,
            'Booking Date' => $booking_date,
            'Return Date' => $return_date
        ];

        foreach ($required_fields as $field => $value) {
            if (empty($value)) {
                throw new Exception("$field is required.");
            }
        }

        // Validate phone number format
        if (!preg_match('/^[0-9]{10,15}$/', $client_pnum)) {
            throw new Exception("Invalid phone number format. Use 10-15 digits.");
        }

        // Validate and calculate duration
        $bookingDate = new DateTime($booking_date);
        $returnDate = new DateTime($return_date);
        $today = new DateTime('today');

        // Ensure booking date is not in the past
        if ($bookingDate < $today) {
            throw new Exception("Booking date cannot be in the past.");
        }

        // Validate return date
        if ($returnDate <= $bookingDate) {
            throw new Exception("Return date must be after booking date.");
        }

        // Calculate duration in days
        $interval = $bookingDate->diff($returnDate);
        $duration = (float) $interval->days;

        // Update client information
        $update_client_sql = "UPDATE CLIENTS 
                            SET CLIENT_PNUM = :pnum, 
                                CLIENT_TYPE = :ctype 
                            WHERE CLIENT_ID = :cid";

        $stmt_client = oci_parse($conn, $update_client_sql);
        oci_bind_by_name($stmt_client, ":pnum", $client_pnum);
        oci_bind_by_name($stmt_client, ":ctype", $client_type);
        oci_bind_by_name($stmt_client, ":cid", $client_id);

        if (!oci_execute($stmt_client)) {
            $e = oci_error($stmt_client);
            throw new Exception("Client update failed: " . $e['message']);
        }

        // Check vehicle availability (date range overlap check)
        $check_availability_sql = "SELECT COUNT(*) AS count FROM BOOKINGS 
                                  WHERE VEHICLE_ID = :vid 
                                  AND (
                                      (TO_DATE(:bdate, 'YYYY-MM-DD') BETWEEN BOOKING_DATE AND BOOKING_DATE + DURATION - 1)
                                      OR 
                                      (TO_DATE(:rdate, 'YYYY-MM-DD') BETWEEN BOOKING_DATE AND BOOKING_DATE + DURATION - 1)
                                      OR 
                                      (BOOKING_DATE BETWEEN TO_DATE(:bdate, 'YYYY-MM-DD') AND TO_DATE(:rdate, 'YYYY-MM-DD'))
                                  )";

        $stmt_check = oci_parse($conn, $check_availability_sql);
        oci_bind_by_name($stmt_check, ":vid", $vehicle_id);
        oci_bind_by_name($stmt_check, ":bdate", $booking_date);
        oci_bind_by_name($stmt_check, ":rdate", $return_date);
        oci_execute($stmt_check);
        $row = oci_fetch_assoc($stmt_check);

        if ($row['COUNT'] > 0) {
            throw new Exception("This vehicle is already booked for the selected dates.");
        }

        // Insert new booking with calculated duration
        $insert_sql = "INSERT INTO BOOKINGS (
            BOOKING_ID, CLIENT_ID, VEHICLE_ID, 
            PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, BOOKING_DATE, 
            DURATION, STATUS_ID
          ) VALUES (
            BOOKING_SEQ.NEXTVAL, :cid, :vid, 
            :ploc, :dloc, TO_DATE(:bdate, 'YYYY-MM-DD'), 
            :dur, 1
          )
          RETURNING BOOKING_ID INTO :new_booking_id";

        // Then bind the output variable
        $stmt_insert = oci_parse($conn, $insert_sql);
        oci_bind_by_name($stmt_insert, ":cid", $client_id);
        oci_bind_by_name($stmt_insert, ":vid", $vehicle_id);
        oci_bind_by_name($stmt_insert, ":ploc", $pickup_location);
        oci_bind_by_name($stmt_insert, ":dloc", $dropoff_location);
        oci_bind_by_name($stmt_insert, ":bdate", $booking_date);
        oci_bind_by_name($stmt_insert, ":dur", $duration);
        oci_bind_by_name($stmt_insert, ":new_booking_id", $new_booking_id, -1, SQLT_INT);

        if (oci_execute($stmt_insert)) {
            oci_commit($conn);
            // $new_booking_id now contains the generated ID
            header("Location: ../paymentPage.php?booking_id=" . $new_booking_id);
            exit;
        } else {
            $e = oci_error($stmt_insert);
            throw new Exception("Booking failed: " . $e['message']);
        }
    } catch (Exception $e) {
        error_log("Booking Error: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../userDashboard.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: ../userDashboard.php");
    exit;
}
?>