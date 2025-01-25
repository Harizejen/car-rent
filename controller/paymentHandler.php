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
    $_SESSION['error'] = "You must be logged in to complete payment";
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['booking_id'], $_POST['payment_method'], $_POST['amount'])) {
    try {
        $client_id = $_SESSION['client_id'];
        $booking_id = $_POST['booking_id'];
        $payment_method = trim($_POST['payment_method']);
        $amount = (float) $_POST['amount'];

        // Validate payment method
        $allowed_methods = ['Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer'];
        if (!in_array($payment_method, $allowed_methods)) {
            throw new Exception("Invalid payment method");
        }

        // Validate amount matches session-stored amount
        if ($amount != $_SESSION['payment_amount']) {
            throw new Exception("Payment amount mismatch");
        }

        // Disable auto-commit to control transactions manually
        // oci_set_autocommit($conn, false);

        // Insert payment
        $payment_sql = "INSERT INTO PAYMENTS (
                        PAYMENT_ID, PAYMENT_METHOD, PAYMENT_DATE, 
                        AMOUNT, BOOKING_ID
                      ) VALUES (
                        PAYMENT_SEQ.NEXTVAL, :method, SYSDATE, 
                        :amount, :bid
                      )";

        $stmt_payment = oci_parse($conn, $payment_sql);
        oci_bind_by_name($stmt_payment, ":method", $payment_method);
        oci_bind_by_name($stmt_payment, ":amount", $amount);
        oci_bind_by_name($stmt_payment, ":bid", $booking_id);
        oci_execute($stmt_payment); // Transaction starts
        if (!oci_execute($stmt_payment)) {
            $e = oci_error($stmt_payment);
            throw new Exception("Payment failed: " . $e['message']);
        }

        // Update booking status to "Paid" (STATUS_ID = 5)
        $update_booking_sql = "UPDATE BOOKINGS SET STATUS_ID = 5 WHERE BOOKING_ID = :bid";
        $stmt_booking = oci_parse($conn, $update_booking_sql);
        oci_bind_by_name($stmt_booking, ":bid", $booking_id);
        oci_execute($stmt_booking);
        if (!oci_execute($stmt_booking)) {
            $e = oci_error($stmt_booking);
            throw new Exception("Status update failed: " . $e['message']);
        }

        // Commit both operations
        oci_commit($conn);

        // Retrieve the generated payment ID from the PAYMENTS table
        $get_payment_sql = "SELECT PAYMENT_ID 
                            FROM PAYMENTS 
                            WHERE BOOKING_ID = :bid 
                            ORDER BY PAYMENT_ID DESC 
                            FETCH FIRST 1 ROWS ONLY";

        $stmt_payment_id = oci_parse($conn, $get_payment_sql);
        oci_bind_by_name($stmt_payment_id, ":bid", $booking_id);
        oci_execute($stmt_payment_id);
        $payment_row = oci_fetch_assoc($stmt_payment_id);

        if (!$payment_row) {
            throw new Exception("Payment successful, but receipt generation failed. Please contact support with Booking ID: $booking_id");
        }

        $payment_id = $payment_row['PAYMENT_ID'];
        $_SESSION['payment_id'] = $payment_id;
        header("Location: ../receipt.php");
        exit;

    } catch (Exception $e) {
        oci_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../paymentPage.php?booking_id=" . $booking_id);
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request";
    header("Location: ../index.php");
    exit;
}
?>