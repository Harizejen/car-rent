<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Feedback
if (isset($_POST['add_feedback'])) {
    $feedback_id = $_POST['feedback_id'];
    $comments = $_POST['comments'];
    $rating_value = $_POST['rating_value'];
    $booking_id = $_POST['booking_id'];
    $driver_id = $_POST['driver_id'];

    // Check if FEEDBACK_ID already exists
    $check_sql = "SELECT COUNT(*) FROM FEEDBACKS WHERE FEEDBACK_ID = :feedback_id";
    $check_stmt = oci_parse($conn, $check_sql);
    oci_bind_by_name($check_stmt, ':feedback_id', $feedback_id);
    oci_execute($check_stmt);
    $row = oci_fetch_assoc($check_stmt);

    if ($row['COUNT(*)'] > 0) {
        echo "<p style='color: red;'>Error: Feedback ID already exists. Please use a unique Feedback ID.</p>";
    } else {
        // Insert query with FEEDBACK_ID
        $sql = "INSERT INTO FEEDBACKS (FEEDBACK_ID, COMMENTS, RATINGVALUE, BOOKING_ID, DRIVER_ID) 
                VALUES (:feedback_id, :comments, :rating_value, :booking_id, :driver_id)";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':feedback_id', $feedback_id);
        oci_bind_by_name($stmt, ':comments', $comments);
        oci_bind_by_name($stmt, ':rating_value', $rating_value);
        oci_bind_by_name($stmt, ':booking_id', $booking_id);
        oci_bind_by_name($stmt, ':driver_id', $driver_id);
        oci_execute($stmt);
        echo "<p style='color: green;'>Feedback added successfully!</p>";
    }
}

// Handle Update Feedback
if (isset($_POST['update_feedback'])) {
    $feedback_id = $_POST['feedback_id'];
    $comments = $_POST['comments'];
    $rating_value = $_POST['rating_value'];
    $booking_id = $_POST['booking_id'];
    $driver_id = $_POST['driver_id'];

    $sql = "UPDATE FEEDBACKS 
            SET COMMENTS = :comments, 
                RATINGVALUE = :rating_value, 
                BOOKING_ID = :booking_id, 
                DRIVER_ID = :driver_id 
            WHERE FEEDBACK_ID = :feedback_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':feedback_id', $feedback_id);
    oci_bind_by_name($stmt, ':comments', $comments);
    oci_bind_by_name($stmt, ':rating_value', $rating_value);
    oci_bind_by_name($stmt, ':booking_id', $booking_id);
    oci_bind_by_name($stmt, ':driver_id', $driver_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Feedback updated successfully!</p>";
}

// Handle Delete Feedback
if (isset($_POST['delete_feedback'])) {
    $feedback_id = $_POST['feedback_id'];

    $sql = "DELETE FROM FEEDBACKS WHERE FEEDBACK_ID = :feedback_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':feedback_id', $feedback_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Feedback deleted successfully!</p>";
}

// Query to fetch feedback
$sql = "SELECT FEEDBACK_ID, COMMENTS, RATINGVALUE, BOOKING_ID, DRIVER_ID FROM FEEDBACKS";
$rsFeedback = oci_parse($conn, $sql);
oci_execute($rsFeedback);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Feedback</title>
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
                            <a href="payment.php" class="nav-link">
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
                            <a href="feedback.php" class="nav-link active">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Feedbacks</p>
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
                            <h1 class="m-0">Feedbacks</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Feedback List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <!-- <li class="nav-item">
                                            <a class="nav-link active" href="#add" data-toggle="tab">Add Feedback</a>
                                        </li> -->
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Feedback</a>
                                        </li> -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Feedback</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Feedback List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Add Feedback Tab -->
                                        <!-- <div class="tab-pane active" id="add">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="feedback_id">Feedback ID</label>
                                                    <input type="number" class="form-control" name="feedback_id" placeholder="Feedback ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="comments">Comments</label>
                                                    <input type="text" class="form-control" name="comments" placeholder="Comments" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="rating_value">Rating Value</label>
                                                    <input type="number" class="form-control" name="rating_value" placeholder="Rating Value" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID</label>
                                                    <input type="number" class="form-control" name="booking_id" placeholder="Booking ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_id">Driver ID</label>
                                                    <input type="number" class="form-control" name="driver_id" placeholder="Driver ID" required>
                                                </div>
                                                <button type="submit" name="add_feedback" class="btn btn-primary">Add Feedback</button>
                                            </form>
                                        </div> -->

                                        <!-- Update Feedback Tab -->
                                        <!-- <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="feedback_id">Feedback ID</label>
                                                    <input type="number" class="form-control" name="feedback_id" placeholder="Feedback ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="comments">Comments</label>
                                                    <input type="text" class="form-control" name="comments" placeholder="Comments" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="rating_value">Rating Value</label>
                                                    <input type="number" class="form-control" name="rating_value" placeholder="Rating Value" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID</label>
                                                    <input type="number" class="form-control" name="booking_id" placeholder="Booking ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_id">Driver ID</label>
                                                    <input type="number" class="form-control" name="driver_id" placeholder="Driver ID" required>
                                                </div>
                                                <button type="submit" name="update_feedback" class="btn btn-info">Update Feedback</button>
                                            </form>
                                        </div> -->

                                        <!-- Delete Feedback Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="feedback_id">Feedback ID</label>
                                                    <input type="number" class="form-control" name="feedback_id" placeholder="Feedback ID" required>
                                                </div>
                                                <button type="submit" name="delete_feedback" class="btn btn-danger">Delete Feedback</button>
                                            </form>
                                        </div>

                                        <!-- Feedback List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Feedback ID</th>
                                                        <th>Comments</th>
                                                        <th>Rating Value</th>
                                                        <th>Booking ID</th>
                                                        <th>Driver ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = oci_fetch_array($rsFeedback, OCI_ASSOC + OCI_RETURN_LOBS)): ?>
                                                        <tr>
                                                            <td><?php echo $row['FEEDBACK_ID']; ?></td>
                                                            <td><?php echo $row['COMMENTS']; ?></td>
                                                            <td><?php echo $row['RATINGVALUE']; ?></td>
                                                            <td><?php echo $row['BOOKING_ID']; ?></td>
                                                            <td><?php echo $row['DRIVER_ID']; ?></td>
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