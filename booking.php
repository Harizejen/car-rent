<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Booking
if (isset($_POST['add_booking'])) {
    $booking_id = $_POST['booking_id'];
    $booking_date = $_POST['booking_date'];
    $pickup_location_id = $_POST['pickup_location_id'];
    $dropoff_location_id = $_POST['dropoff_location_id'];
    $client_id = $_POST['client_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $status_id = $_POST['status_id'];
    $duration = $_POST['duration'];

    // Check if BOOKING_ID already exists
    $check_sql = "SELECT COUNT(*) FROM BOOKINGS WHERE BOOKING_ID = :booking_id";
    $check_stmt = oci_parse($conn, $check_sql);
    oci_bind_by_name($check_stmt, ':booking_id', $booking_id);
    oci_execute($check_stmt);
    $row = oci_fetch_assoc($check_stmt);

    if ($row['COUNT(*)'] > 0) {
        echo "<p style='color: red;'>Error: Booking ID already exists. Please use a unique Booking ID.</p>";
    } else {
        // Insert query with BOOKING_ID
        $sql = "INSERT INTO BOOKINGS (BOOKING_ID, BOOKING_DATE, PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, CLIENT_ID, VEHICLE_ID, STATUS_ID, DURATION) 
                VALUES (:booking_id, TO_DATE(:booking_date, 'DD/MM/YYYY'), :pickup_location_id, :dropoff_location_id, :client_id, :vehicle_id, :status_id, :duration)";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':booking_id', $booking_id);
        oci_bind_by_name($stmt, ':booking_date', $booking_date);
        oci_bind_by_name($stmt, ':pickup_location_id', $pickup_location_id);
        oci_bind_by_name($stmt, ':dropoff_location_id', $dropoff_location_id);
        oci_bind_by_name($stmt, ':client_id', $client_id);
        oci_bind_by_name($stmt, ':vehicle_id', $vehicle_id);
        oci_bind_by_name($stmt, ':status_id', $status_id);
        oci_bind_by_name($stmt, ':duration', $duration);
        oci_execute($stmt);
        echo "<p style='color: green;'>Booking added successfully!</p>";
    }
}

// Handle Update Booking
if (isset($_POST['update_booking'])) {
    $booking_id = $_POST['booking_id'];
    $booking_date = $_POST['booking_date'];
    $pickup_location_id = $_POST['pickup_location_id'];
    $dropoff_location_id = $_POST['dropoff_location_id'];
    $client_id = $_POST['client_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $status_id = $_POST['status_id'];
    $duration = $_POST['duration'];

    $sql = "UPDATE BOOKINGS 
            SET BOOKING_DATE = TO_DATE(:booking_date, 'DD/MM/YYYY'), 
                PICKUP_LOCATION_ID = :pickup_location_id, 
                DROPOFF_LOCATION_ID = :dropoff_location_id, 
                CLIENT_ID = :client_id, 
                VEHICLE_ID = :vehicle_id, 
                STATUS_ID = :status_id, 
                DURATION = :duration 
            WHERE BOOKING_ID = :booking_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':booking_id', $booking_id);
    oci_bind_by_name($stmt, ':booking_date', $booking_date);
    oci_bind_by_name($stmt, ':pickup_location_id', $pickup_location_id);
    oci_bind_by_name($stmt, ':dropoff_location_id', $dropoff_location_id);
    oci_bind_by_name($stmt, ':client_id', $client_id);
    oci_bind_by_name($stmt, ':vehicle_id', $vehicle_id);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_bind_by_name($stmt, ':duration', $duration);
    oci_execute($stmt);
    echo "<p style='color: green;'>Booking updated successfully!</p>";
}

// Handle Delete Booking
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];

    $sql = "DELETE FROM BOOKINGS WHERE BOOKING_ID = :booking_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':booking_id', $booking_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Booking deleted successfully!</p>";
}

// Query to fetch bookings
$sql = "SELECT BOOKING_ID, BOOKING_DATE, PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, CLIENT_ID, VEHICLE_ID, STATUS_ID, DURATION FROM BOOKINGS";
$rsBook = oci_parse($conn, $sql);
oci_execute($rsBook); // Execute the query for the first time
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bookings</title>
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
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
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
                            <a href="booking.php" class="nav-link active">
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
                            <h1 class="m-0">Bookings</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Bookings List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Booking</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Booking</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Bookings List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Update Booking Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID</label>
                                                    <select class="form-control" id="booking_id" name="booking_id" required>
                                                        <option value="">Select Booking ID</option>
                                                        <?php 
                                                        // Reset the result set pointer
                                                        oci_execute($rsBook);
                                                        while ($row = oci_fetch_assoc($rsBook)): ?>
                                                            <option value="<?php echo $row['BOOKING_ID']; ?>"><?php echo $row['BOOKING_ID']; ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="booking_date">Booking Date</label>
                                                    <input type="text" class="form-control" id="booking_date" name="booking_date" placeholder="Booking Date (DD/MM/YYYY)" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="pickup_location_id">Pickup Location ID</label>
                                                    <input type="number" class="form-control" id="pickup_location_id" name="pickup_location_id" placeholder="Pickup Location ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="dropoff_location_id">Dropoff Location ID</label>
                                                    <input type="number" class="form-control" id="dropoff_location_id" name="dropoff_location_id" placeholder="Dropoff Location ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="client_id">Client ID</label>
                                                    <input type="number" class="form-control" id="client_id" name="client_id" placeholder="Client ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="vehicle_id">Vehicle ID</label>
                                                    <input type="number" class="form-control" id="vehicle_id" name="vehicle_id" placeholder="Vehicle ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" id="status_id" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="duration">Duration</label>
                                                    <input type="number" class="form-control" id="duration" name="duration" placeholder="Duration" required>
                                                </div>
                                                <button type="submit" name="update_booking" class="btn btn-info">Update Booking</button>
                                            </form>
                                        </div>

                                        <!-- Delete Booking Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="booking_id">Booking ID</label>
                                                    <input type="number" class="form-control" name="booking_id" placeholder="Booking ID" required>
                                                </div>
                                                <button type="submit" name="delete_booking" class="btn btn-danger">Delete Booking</button>
                                            </form>
                                        </div>

                                        <!-- Bookings List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Booking ID</th>
                                                        <th>Booking Date</th>
                                                        <th>Pickup Location ID</th>
                                                        <th>Dropoff Location ID</th>
                                                        <th>Client ID</th>
                                                        <th>Vehicle ID</th>
                                                        <th>Status ID</th>
                                                        <th>Duration</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    // Reset the result set pointer before fetching data for the table
                                                    oci_execute($rsBook);
                                                    while ($row = oci_fetch_assoc($rsBook)): ?>
                                                        <tr>
                                                            <td><?php echo $row['BOOKING_ID']; ?></td>
                                                            <td><?php echo $row['BOOKING_DATE']; ?></td>
                                                            <td><?php echo $row['PICKUP_LOCATION_ID']; ?></td>
                                                            <td><?php echo $row['DROPOFF_LOCATION_ID']; ?></td>
                                                            <td><?php echo $row['CLIENT_ID']; ?></td>
                                                            <td><?php echo $row['VEHICLE_ID']; ?></td>
                                                            <td><?php echo $row['STATUS_ID']; ?></td>
                                                            <td><?php echo $row['DURATION']; ?></td>
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
    <script>
        $(document).ready(function() {
            $('#booking_id').change(function() {
                var bookingId = $(this).val();
                if (bookingId) {
                    $.ajax({
                        url: 'get_booking_details.php', // PHP script to fetch booking details
                        type: 'POST',
                        data: { booking_id: bookingId },
                        success: function(response) {
                            var bookingDetails = JSON.parse(response);
                            if (bookingDetails) {
                                // Populate the form fields with the fetched data
                                $('#booking_date').val(bookingDetails.BOOKING_DATE);
                                $('#pickup_location_id').val(bookingDetails.PICKUP_LOCATION_ID);
                                $('#dropoff_location_id').val(bookingDetails.DROPOFF_LOCATION_ID);
                                $('#client_id').val(bookingDetails.CLIENT_ID);
                                $('#vehicle_id').val(bookingDetails.VEHICLE_ID);
                                $('#status_id').val(bookingDetails.STATUS_ID);
                                $('#duration').val(bookingDetails.DURATION);
                            } else {
                                // Clear the fields if no data is found
                                $('#booking_date').val('');
                                $('#pickup_location_id').val('');
                                $('#dropoff_location_id').val('');
                                $('#client_id').val('');
                                $('#vehicle_id').val('');
                                $('#status_id').val('');
                                $('#duration').val('');
                            }
                        }
                    });
                } else {
                    // Clear the fields if no booking is selected
                    $('#booking_date').val('');
                    $('#pickup_location_id').val('');
                    $('#dropoff_location_id').val('');
                    $('#client_id').val('');
                    $('#vehicle_id').val('');
                    $('#status_id').val('');
                    $('#duration').val('');
                }
            });
        });
    </script>
</body>
</html>