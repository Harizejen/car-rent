<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);
require_once '../db/db.php';

if (!isset($_SESSION['client_id']) || !isset($_SESSION['payment_id'])) {
    $_SESSION['error'] = "Invalid receipt access";
    header("Location: ../index.php");
    exit;
}

$payment_id = $_SESSION['payment_id'];
unset($_SESSION['payment_id']);

try {
    $sql = "SELECT p.*, b.BOOKING_DATE, b.DURATION 
            FROM PAYMENTS p
            JOIN BOOKINGS b ON p.BOOKING_ID = b.BOOKING_ID
            WHERE p.PAYMENT_ID = :pid";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":pid", $payment_id);
    oci_execute($stmt);
    $payment = oci_fetch_assoc($stmt);

    if (!$payment) {
        throw new Exception("Payment record not found");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
</head>

<body>
    <h1>Payment Confirmation</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error'];
        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <h2>Receipt #<?= $payment['PAYMENT_ID'] ?></h2>
    <p>Payment Date: <?= date('Y-m-d H:i', strtotime($payment['PAYMENT_DATE'])) ?></p>
    <p>Amount Paid: $<?= number_format($payment['AMOUNT'], 2) ?></p>
    <p>Payment Method: <?= htmlspecialchars($payment['PAYMENT_METHOD']) ?></p>
    <p>Booking Period: <?= $payment['DURATION'] ?> days</p>
    <p>Booking Date: <?= $payment['BOOKING_DATE'] ?></p>

    <p><a href="../index.php">Return to Dashboard</a></p>
</body>

</html>