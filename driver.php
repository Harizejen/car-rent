<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Driver
if (isset($_POST['add_driver'])) {
    $driver_name = $_POST['driver_name'];
    $driver_pnum = $_POST['driver_pnum'];
    $rating = $_POST['rating'];
    $license_num = $_POST['license_num'];
    $status_id = $_POST['status_id'];
    $rate = $_POST['rate'];
    $onleave_rate = $_POST['onleave_rate'];

    // Insert query without DRIVER_ID
    $sql = "INSERT INTO DRIVERS (DRIVER_NAME, DRIVER_PNUM, RATING, LICENSE_NUM, STATUS_ID, RATE, ONLEAVE_RATE) 
            VALUES (:driver_name, :driver_pnum, :rating, :license_num, :status_id, :rate, :onleave_rate)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':driver_name', $driver_name);
    oci_bind_by_name($stmt, ':driver_pnum', $driver_pnum);
    oci_bind_by_name($stmt, ':rating', $rating);
    oci_bind_by_name($stmt, ':license_num', $license_num);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_bind_by_name($stmt, ':rate', $rate);
    oci_bind_by_name($stmt, ':onleave_rate', $onleave_rate);
    oci_execute($stmt);
    echo "<p style='color: green;'>Driver added successfully!</p>";
}

// Handle Update Driver
if (isset($_POST['update_driver'])) {
    $driver_id = $_POST['driver_id'];
    $driver_name = $_POST['driver_name'];
    $driver_pnum = $_POST['driver_pnum'];
    $rating = $_POST['rating'];
    $license_num = $_POST['license_num'];
    $status_id = $_POST['status_id'];
    $rate = $_POST['rate'];
    $onleave_rate = $_POST['onleave_rate'];

    $sql = "UPDATE DRIVERS 
            SET DRIVER_NAME = :driver_name, 
                DRIVER_PNUM = :driver_pnum, 
                RATING = :rating, 
                LICENSE_NUM = :license_num, 
                STATUS_ID = :status_id, 
                RATE = :rate, 
                ONLEAVE_RATE = :onleave_rate 
            WHERE DRIVER_ID = :driver_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':driver_id', $driver_id);
    oci_bind_by_name($stmt, ':driver_name', $driver_name);
    oci_bind_by_name($stmt, ':driver_pnum', $driver_pnum);
    oci_bind_by_name($stmt, ':rating', $rating);
    oci_bind_by_name($stmt, ':license_num', $license_num);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_bind_by_name($stmt, ':rate', $rate);
    oci_bind_by_name($stmt, ':onleave_rate', $onleave_rate);
    oci_execute($stmt);
    echo "<p style='color: green;'>Driver updated successfully!</p>";
}

// Handle Delete Driver
if (isset($_POST['delete_driver'])) {
    $driver_id = $_POST['driver_id'];

    $sql = "DELETE FROM DRIVERS WHERE DRIVER_ID = :driver_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':driver_id', $driver_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Driver deleted successfully!</p>";
}

// Query to fetch drivers
$sql = "SELECT DRIVER_ID, DRIVER_NAME, DRIVER_PNUM, RATING, LICENSE_NUM, STATUS_ID, RATE, ONLEAVE_RATE FROM DRIVERS";
$rsDriver = oci_parse($conn, $sql);
oci_execute($rsDriver);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Drivers</title>
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
                            <a href="driver.php" class="nav-link active">
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
                            <a href="feedback.php" class="nav-link">
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
                            <h1 class="m-0">Drivers</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Drivers List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#add" data-toggle="tab">Add Driver</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Driver</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Driver</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Drivers List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Add Driver Tab -->
                                        <div class="tab-pane active" id="add">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="driver_name">Driver Name</label>
                                                    <input type="text" class="form-control" name="driver_name" placeholder="Driver Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_pnum">Driver Phone Number</label>
                                                    <input type="text" class="form-control" name="driver_pnum" placeholder="Driver Phone Number" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="rating">Rating</label>
                                                    <input type="number" step="0.1" class="form-control" name="rating" placeholder="Rating" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="license_num">License Number</label>
                                                    <input type="text" class="form-control" name="license_num" placeholder="License Number" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="rate">Rate</label>
                                                    <input type="number" class="form-control" name="rate" placeholder="Rate" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="onleave_rate">On Leave Rate</label>
                                                    <input type="number" class="form-control" name="onleave_rate" placeholder="On Leave Rate" required>
                                                </div>
                                                <button type="submit" name="add_driver" class="btn btn-primary">Add Driver</button>
                                            </form>
                                        </div>

                                        <!-- Update Driver Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="driver_id">Driver ID</label>
                                                    <input type="number" class="form-control" name="driver_id" placeholder="Driver ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_name">Driver Name</label>
                                                    <input type="text" class="form-control" name="driver_name" placeholder="Driver Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_pnum">Driver Phone Number</label>
                                                    <input type="text" class="form-control" name="driver_pnum" placeholder="Driver Phone Number" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="rating">Rating</label>
                                                    <input type="number" step="0.1" class="form-control" name="rating" placeholder="Rating" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="license_num">License Number</label>
                                                    <input type="text" class="form-control" name="license_num" placeholder="License Number" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="rate">Rate</label>
                                                    <input type="number" class="form-control" name="rate" placeholder="Rate" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="onleave_rate">On Leave Rate</label>
                                                    <input type="number" class="form-control" name="onleave_rate" placeholder="On Leave Rate" required>
                                                </div>
                                                <button type="submit" name="update_driver" class="btn btn-info">Update Driver</button>
                                            </form>
                                        </div>

                                        <!-- Delete Driver Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="driver_id">Driver ID</label>
                                                    <input type="number" class="form-control" name="driver_id" placeholder="Driver ID" required>
                                                </div>
                                                <button type="submit" name="delete_driver" class="btn btn-danger">Delete Driver</button>
                                            </form>
                                        </div>

                                        <!-- Drivers List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Driver ID</th>
                                                        <th>Driver Name</th>
                                                        <th>Driver Phone Number</th>
                                                        <th>Rating</th>
                                                        <th>License Number</th>
                                                        <th>Status ID</th>
                                                        <th>Rate</th>
                                                        <th>On Leave Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = oci_fetch_assoc($rsDriver)): ?>
                                                        <tr>
                                                            <td><?php echo $row['DRIVER_ID']; ?></td>
                                                            <td><?php echo $row['DRIVER_NAME']; ?></td>
                                                            <td><?php echo $row['DRIVER_PNUM']; ?></td>
                                                            <td><?php echo $row['RATING']; ?></td>
                                                            <td><?php echo $row['LICENSE_NUM']; ?></td>
                                                            <td><?php echo $row['STATUS_ID']; ?></td>
                                                            <td><?php echo $row['RATE']; ?></td>
                                                            <td><?php echo $row['ONLEAVE_RATE']; ?></td>
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