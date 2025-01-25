<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);
require_once 'db/db.php';

if (!isset($_SESSION['client_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page.";
    header("Location: index.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    $_SESSION['error'] = "Invalid booking reference";
    header("Location: index.php");
    exit;
}

$booking_id = $_GET['booking_id'];
$client_id = $_SESSION['client_id'];

try {
    // Get booking details and calculate amount using RATE_PER_DAY
    $sql = "SELECT b.BOOKING_ID, b.DURATION, v.RATE_PER_DAY 
            FROM BOOKINGS b
            JOIN VEHICLE v ON b.VEHICLE_ID = v.VEHICLE_ID
            WHERE b.BOOKING_ID = :bid AND b.CLIENT_ID = :cid";

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":bid", $booking_id);
    oci_bind_by_name($stmt, ":cid", $client_id);
    oci_execute($stmt);
    $booking = oci_fetch_assoc($stmt);

    if (!$booking) {
        throw new Exception("Booking not found or access denied $booking_id");
    }

    $amount = $booking['DURATION'] * $booking['RATE_PER_DAY']; // Use RATE_PER_DAY
    $_SESSION['payment_amount'] = $amount;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: index.php");
    exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h1>Complete Your Payment</h1>
                    </div>
                    <div class="card-body">
                        <!-- Error Message -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger text-center">
                                <?= htmlspecialchars($_SESSION['error']); ?>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Payment Details -->
                        <p class="text-center fs-5">Total Amount: <strong>RM<?= number_format($amount, 2) ?></strong>
                        </p>

                        <!-- Payment Form -->
                        <form action="controller/paymentHandler.php" method="post">
                            <input type="hidden" name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking_id) ?>">
                            <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Select Method</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Debit Card">Debit Card</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Submit Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>