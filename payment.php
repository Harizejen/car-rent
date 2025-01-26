<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Payment
if (isset($_POST['add_payment'])) {
    $payment_id = $_POST['payment_id'];
    $payment_method = $_POST['payment_method'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $booking_id = $_POST['booking_id'];
    $status_id = $_POST['status_id'];

    // Check if PAYMENT_ID already exists
    $check_sql = "SELECT COUNT(*) FROM PAYMENTS WHERE PAYMENT_ID = :payment_id";
    $check_stmt = oci_parse($conn, $check_sql);
    oci_bind_by_name($check_stmt, ':payment_id', $payment_id);
    oci_execute($check_stmt);
    $row = oci_fetch_assoc($check_stmt);

    if ($row['COUNT(*)'] > 0) {
        echo "<p style='color: red;'>Error: Payment ID already exists. Please use a unique Payment ID.</p>";
    } else {
        // Insert query with PAYMENT_ID
        $sql = "INSERT INTO PAYMENTS (PAYMENT_ID, PAYMENT_METHOD, PAYMENT_DATE, AMOUNT, BOOKING_ID, STATUS_ID) 
                VALUES (:payment_id, :payment_method, TO_DATE(:payment_date, 'DD/MM/YYYY'), :amount, :booking_id, :status_id)";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':payment_id', $payment_id);
        oci_bind_by_name($stmt, ':payment_method', $payment_method);
        oci_bind_by_name($stmt, ':payment_date', $payment_date);
        oci_bind_by_name($stmt, ':amount', $amount);
        oci_bind_by_name($stmt, ':booking_id', $booking_id);
        oci_bind_by_name($stmt, ':status_id', $status_id);
        oci_execute($stmt);
        echo "<p style='color: green;'>Payment added successfully!</p>";
    }
}

// Handle Update Payment
if (isset($_POST['update_payment'])) {
    $payment_id = $_POST['payment_id'];
    $payment_method = $_POST['payment_method'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $booking_id = $_POST['booking_id'];
    $status_id = $_POST['status_id'];

    $sql = "UPDATE PAYMENTS 
            SET PAYMENT_METHOD = :payment_method, 
                PAYMENT_DATE = TO_DATE(:payment_date, 'DD/MM/YYYY'), 
                AMOUNT = :amount, 
                BOOKING_ID = :booking_id, 
                STATUS_ID = :status_id 
            WHERE PAYMENT_ID = :payment_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':payment_id', $payment_id);
    oci_bind_by_name($stmt, ':payment_method', $payment_method);
    oci_bind_by_name($stmt, ':payment_date', $payment_date);
    oci_bind_by_name($stmt, ':amount', $amount);
    oci_bind_by_name($stmt, ':booking_id', $booking_id);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Payment updated successfully!</p>";
}

// Handle Delete Payment
if (isset($_POST['delete_payment'])) {
    $payment_id = $_POST['payment_id'];

    $sql = "DELETE FROM PAYMENTS WHERE PAYMENT_ID = :payment_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':payment_id', $payment_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Payment deleted successfully!</p>";
}

// Query to fetch payments
$sql = "SELECT PAYMENT_ID, PAYMENT_METHOD, PAYMENT_DATE, AMOUNT, BOOKING_ID, STATUS_ID FROM PAYMENTS";
$rsPayment = oci_parse($conn, $sql);
oci_execute($rsPayment);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="dashboard.php" class="nav-link">Home</a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="dashboard.php" class="brand-link">
                <span class="brand-text font-weight-light">Admin</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="driver.php" class="nav-link">
                                <i class="nav-icon fas fa-car"></i>
                                <p>Drivers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="client.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Clients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="booking.php" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Bookings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="vehicle.php" class="nav-link">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>Vehicles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="status.php" class="nav-link">
                                <i class="nav-icon fas fa-info-circle"></i>
                                <p>Status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="payment.php" class="nav-link active">
                                <i class="nav-icon fas fa-money-bill"></i>
                                <p>Payments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="hub.php" class="nav-link">
                                <i class="nav-icon fas fa-map-marker-alt"></i>
                                <p>Hubs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="feedback.php" class="nav-link">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Feedbacks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logoutAdmin.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Payments</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Payments List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <!-- <li class="nav-item">
                                            <a class="nav-link active" href="#add" data-toggle="tab">Add Payment</a>
                                        </li> -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Payment</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Payment</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Payments List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Add Payment Tab -->
                                        <!-- <div class="tab-pane active" id="add">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="payment_id">Payment ID</label>
                                                    <input type="number" class="form-control" name="payment_id" placeholder="Payment ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_method">Payment Method</label>
                                                    <input type="text" class="form-control" name="payment_method" placeholder="Payment Method" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_date">Payment Date</label>
                                                    <input type="text" class="form-control" name="payment_date" placeholder="Payment Date (DD/MM/YYYY)" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount">Amount</label>
                                                    <input type="number" step="0.01" class="form-control" name="amount" placeholder="Amount" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID</label>
                                                    <input type="number" class="form-control" name="booking_id" placeholder="Booking ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <button type="submit" name="add_payment" class="btn btn-primary">Add Payment</button>
                                            </form>
                                        </div> -->

                                        <!-- Update Payment Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="payment_id">Payment ID</label>
                                                    <input type="number" class="form-control" name="payment_id" placeholder="Payment ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_method">Payment Method</label>
                                                    <input type="text" class="form-control" name="payment_method" placeholder="Payment Method" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_date">Payment Date</label>
                                                    <input type="text" class="form-control" name="payment_date" placeholder="Payment Date (DD/MM/YYYY)" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount">Amount</label>
                                                    <input type="number" step="0.01" class="form-control" name="amount" placeholder="Amount" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID</label>
                                                    <input type="number" class="form-control" name="booking_id" placeholder="Booking ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <button type="submit" name="update_payment" class="btn btn-info">Update Payment</button>
                                            </form>
                                        </div>

                                        <!-- Delete Payment Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="payment_id">Payment ID</label>
                                                    <input type="number" class="form-control" name="payment_id" placeholder="Payment ID" required>
                                                </div>
                                                <button type="submit" name="delete_payment" class="btn btn-danger">Delete Payment</button>
                                            </form>
                                        </div>

                                        <!-- Payments List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Payment ID</th>
                                                        <th>Payment Method</th>
                                                        <th>Payment Date</th>
                                                        <th>Amount</th>
                                                        <th>Booking ID</th>
                                                        <th>Status ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = oci_fetch_assoc($rsPayment)): ?>
                                                        <tr>
                                                            <td><?php echo $row['PAYMENT_ID']; ?></td>
                                                            <td><?php echo $row['PAYMENT_METHOD']; ?></td>
                                                            <td><?php echo $row['PAYMENT_DATE']; ?></td>
                                                            <td><?php echo $row['AMOUNT']; ?></td>
                                                            <td><?php echo $row['BOOKING_ID']; ?></td>
                                                            <td><?php echo $row['STATUS_ID']; ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2025 <a href="#">ADMIN</a>.</strong>
            All rights reserved.
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>
</html>