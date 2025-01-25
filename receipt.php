<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);
require_once 'db/db.php';

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center bg-success text-white">
                        <h1>Payment Confirmation</h1>
                    </div>
                    <div class="card-body">
                        <!-- Error Message -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger text-center">
                                <?= htmlspecialchars($_SESSION['error']); ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Receipt Details -->
                        <h2 class="text-center mb-4">Receipt #<?= htmlspecialchars($payment['PAYMENT_ID']) ?></h2>
                        <p><strong>Payment Date:</strong> <?= date('Y-m-d H:i', strtotime($payment['PAYMENT_DATE'])) ?>
                        </p>
                        <p><strong>Amount Paid:</strong> RM<?= number_format($payment['AMOUNT'], 2) ?></p>
                        <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment['PAYMENT_METHOD']) ?></p>
                        <p><strong>Booking Period:</strong> <?= htmlspecialchars($payment['DURATION']) ?> days</p>
                        <p><strong>Booking Date:</strong> <?= htmlspecialchars($payment['BOOKING_DATE']) ?></p>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary">Return to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>