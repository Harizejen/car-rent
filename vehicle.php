<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Vehicle
if (isset($_POST['add_vehicle'])) {
    $vehicle_name = $_POST['vehicle_name'];
    $license_plate = $_POST['license_plate'];
    $vehicle_type = $_POST['vehicle_type'];
    $location_id = $_POST['location_id'];
    $driver_id = $_POST['driver_id'];
    $status_id = $_POST['status_id'];

    // Insert query without VEHICLE_ID
    $sql = "INSERT INTO VEHICLE (VEHICLE_NAME, LICENSE_PLATE, VEHICLE_TYPE, LOCATION_ID, DRIVER_ID, STATUS_ID) 
            VALUES (:vehicle_name, :license_plate, :vehicle_type, :location_id, :driver_id, :status_id)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':vehicle_name', $vehicle_name);
    oci_bind_by_name($stmt, ':license_plate', $license_plate);
    oci_bind_by_name($stmt, ':vehicle_type', $vehicle_type);
    oci_bind_by_name($stmt, ':location_id', $location_id);
    oci_bind_by_name($stmt, ':driver_id', $driver_id);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Vehicle added successfully!</p>";
}

// Handle Update Vehicle
if (isset($_POST['update_vehicle'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $vehicle_name = $_POST['vehicle_name'];
    $license_plate = $_POST['license_plate'];
    $vehicle_type = $_POST['vehicle_type'];
    $location_id = $_POST['location_id'];
    $driver_id = $_POST['driver_id'];
    $status_id = $_POST['status_id'];

    $sql = "UPDATE VEHICLE 
            SET VEHICLE_NAME = :vehicle_name, 
                LICENSE_PLATE = :license_plate, 
                VEHICLE_TYPE = :vehicle_type, 
                LOCATION_ID = :location_id, 
                DRIVER_ID = :driver_id, 
                STATUS_ID = :status_id 
            WHERE VEHICLE_ID = :vehicle_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':vehicle_id', $vehicle_id);
    oci_bind_by_name($stmt, ':vehicle_name', $vehicle_name);
    oci_bind_by_name($stmt, ':license_plate', $license_plate);
    oci_bind_by_name($stmt, ':vehicle_type', $vehicle_type);
    oci_bind_by_name($stmt, ':location_id', $location_id);
    oci_bind_by_name($stmt, ':driver_id', $driver_id);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Vehicle updated successfully!</p>";
}

// Handle Delete Vehicle
if (isset($_POST['delete_vehicle'])) {
    $vehicle_id = $_POST['vehicle_id'];

    $sql = "DELETE FROM VEHICLE WHERE VEHICLE_ID = :vehicle_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':vehicle_id', $vehicle_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Vehicle deleted successfully!</p>";
}

// Query to fetch vehicles
$sql = "SELECT VEHICLE_ID, VEHICLE_NAME, LICENSE_PLATE, VEHICLE_TYPE, LOCATION_ID, DRIVER_ID, STATUS_ID FROM VEHICLE";
$rsVehicle = oci_parse($conn, $sql);
oci_execute($rsVehicle);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Vehicles</title>
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
                            <a href="vehicle.php" class="nav-link active">
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
                            <h1 class="m-0">Vehicles</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Vehicles List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#add" data-toggle="tab">Add Vehicle</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Vehicle</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Vehicle</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Vehicles List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Add Vehicle Tab -->
                                        <div class="tab-pane active" id="add">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="vehicle_name">Vehicle Name</label>
                                                    <input type="text" class="form-control" name="vehicle_name" placeholder="Vehicle Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="license_plate">License Plate</label>
                                                    <input type="text" class="form-control" name="license_plate" placeholder="License Plate" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="vehicle_type">Vehicle Type</label>
                                                    <input type="text" class="form-control" name="vehicle_type" placeholder="Vehicle Type" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="location_id">Location ID</label>
                                                    <input type="number" class="form-control" name="location_id" placeholder="Location ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_id">Driver ID</label>
                                                    <input type="number" class="form-control" name="driver_id" placeholder="Driver ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <button type="submit" name="add_vehicle" class="btn btn-primary">Add Vehicle</button>
                                            </form>
                                        </div>

                                        <!-- Update Vehicle Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="vehicle_id">Vehicle ID</label>
                                                    <input type="number" class="form-control" name="vehicle_id" placeholder="Vehicle ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="vehicle_name">Vehicle Name</label>
                                                    <input type="text" class="form-control" name="vehicle_name" placeholder="Vehicle Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="license_plate">License Plate</label>
                                                    <input type="text" class="form-control" name="license_plate" placeholder="License Plate" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="vehicle_type">Vehicle Type</label>
                                                    <input type="text" class="form-control" name="vehicle_type" placeholder="Vehicle Type" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="location_id">Location ID</label>
                                                    <input type="number" class="form-control" name="location_id" placeholder="Location ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="driver_id">Driver ID</label>
                                                    <input type="number" class="form-control" name="driver_id" placeholder="Driver ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <button type="submit" name="update_vehicle" class="btn btn-info">Update Vehicle</button>
                                            </form>
                                        </div>

                                        <!-- Delete Vehicle Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="vehicle_id">Vehicle ID</label>
                                                    <input type="number" class="form-control" name="vehicle_id" placeholder="Vehicle ID" required>
                                                </div>
                                                <button type="submit" name="delete_vehicle" class="btn btn-danger">Delete Vehicle</button>
                                            </form>
                                        </div>

                                        <!-- Vehicles List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Vehicle ID</th>
                                                        <th>Vehicle Name</th>
                                                        <th>License Plate</th>
                                                        <th>Vehicle Type</th>
                                                        <th>Location ID</th>
                                                        <th>Driver ID</th>
                                                        <th>Status ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = oci_fetch_assoc($rsVehicle)): ?>
                                                        <tr>
                                                            <td><?php echo $row['VEHICLE_ID']; ?></td>
                                                            <td><?php echo $row['VEHICLE_NAME']; ?></td>
                                                            <td><?php echo $row['LICENSE_PLATE']; ?></td>
                                                            <td><?php echo $row['VEHICLE_TYPE']; ?></td>
                                                            <td><?php echo $row['LOCATION_ID']; ?></td>
                                                            <td><?php echo $row['DRIVER_ID']; ?></td>
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