<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Hub
if (isset($_POST['add_hub'])) {
    $location_id = $_POST['location_id'];
    $state_name = $_POST['state_name'];
    $location_name = $_POST['location_name'];
    $address = $_POST['address'];
    $location_desc = $_POST['location_desc'];

    // Check if LOCATION_ID already exists
    $check_sql = "SELECT COUNT(*) FROM HUB WHERE LOCATION_ID = :location_id";
    $check_stmt = oci_parse($conn, $check_sql);
    oci_bind_by_name($check_stmt, ':location_id', $location_id);
    oci_execute($check_stmt);
    $row = oci_fetch_assoc($check_stmt);

    if ($row['COUNT(*)'] > 0) {
        echo "<p style='color: red;'>Error: Location ID already exists. Please use a unique Location ID.</p>";
    } else {
        // Insert query with LOCATION_ID
        $sql = "INSERT INTO HUB (LOCATION_ID, STATE_NAME, LOCATION_NAME, ADDRESS, LOCATION_DESC) 
                VALUES (:location_id, :state_name, :location_name, :address, :location_desc)";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':location_id', $location_id);
        oci_bind_by_name($stmt, ':state_name', $state_name);
        oci_bind_by_name($stmt, ':location_name', $location_name);
        oci_bind_by_name($stmt, ':address', $address);
        oci_bind_by_name($stmt, ':location_desc', $location_desc);
        oci_execute($stmt);
        echo "<p style='color: green;'>Hub added successfully!</p>";
    }
}

// Handle Update Hub
if (isset($_POST['update_hub'])) {
    $location_id = $_POST['location_id'];
    $state_name = $_POST['state_name'];
    $location_name = $_POST['location_name'];
    $address = $_POST['address'];
    $location_desc = $_POST['location_desc'];

    $sql = "UPDATE HUB 
            SET STATE_NAME = :state_name, 
                LOCATION_NAME = :location_name, 
                ADDRESS = :address, 
                LOCATION_DESC = :location_desc 
            WHERE LOCATION_ID = :location_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':location_id', $location_id);
    oci_bind_by_name($stmt, ':state_name', $state_name);
    oci_bind_by_name($stmt, ':location_name', $location_name);
    oci_bind_by_name($stmt, ':address', $address);
    oci_bind_by_name($stmt, ':location_desc', $location_desc);
    oci_execute($stmt);
    echo "<p style='color: green;'>Hub updated successfully!</p>";
}

// Handle Delete Hub
if (isset($_POST['delete_hub'])) {
    $location_id = $_POST['location_id'];

    $sql = "DELETE FROM HUB WHERE LOCATION_ID = :location_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':location_id', $location_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Hub deleted successfully!</p>";
}

// Query to fetch hubs
$sql = "SELECT LOCATION_ID, STATE_NAME, LOCATION_NAME, ADDRESS, LOCATION_DESC FROM HUB";
$rsHub = oci_parse($conn, $sql);
oci_execute($rsHub);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Hubs</title>
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
                            <a href="hub.php" class="nav-link active">
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
                            <h1 class="m-0">Hubs</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Hubs List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#add" data-toggle="tab">Add Hub</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Hub</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Hub</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Hubs List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Add Hub Tab -->
                                        <div class="tab-pane active" id="add">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="location_id">Location ID</label>
                                                    <input type="number" class="form-control" name="location_id" placeholder="Location ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="state_name">State Name</label>
                                                    <input type="text" class="form-control" name="state_name" placeholder="State Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="location_name">Location Name</label>
                                                    <input type="text" class="form-control" name="location_name" placeholder="Location Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <input type="text" class="form-control" name="address" placeholder="Address" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="location_desc">Location Description</label>
                                                    <input type="text" class="form-control" name="location_desc" placeholder="Location Description" required>
                                                </div>
                                                <button type="submit" name="add_hub" class="btn btn-primary">Add Hub</button>
                                            </form>
                                        </div>

                                        <!-- Update Hub Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="location_id">Location ID</label>
                                                    <input type="number" class="form-control" name="location_id" placeholder="Location ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="state_name">State Name</label>
                                                    <input type="text" class="form-control" name="state_name" placeholder="State Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="location_name">Location Name</label>
                                                    <input type="text" class="form-control" name="location_name" placeholder="Location Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <input type="text" class="form-control" name="address" placeholder="Address" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="location_desc">Location Description</label>
                                                    <input type="text" class="form-control" name="location_desc" placeholder="Location Description" required>
                                                </div>
                                                <button type="submit" name="update_hub" class="btn btn-info">Update Hub</button>
                                            </form>
                                        </div>

                                        <!-- Delete Hub Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="location_id">Location ID</label>
                                                    <input type="number" class="form-control" name="location_id" placeholder="Location ID" required>
                                                </div>
                                                <button type="submit" name="delete_hub" class="btn btn-danger">Delete Hub</button>
                                            </form>
                                        </div>

                                        <!-- Hubs List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Location ID</th>
                                                        <th>State Name</th>
                                                        <th>Location Name</th>
                                                        <th>Address</th>
                                                        <th>Location Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = oci_fetch_array($rsHub, OCI_ASSOC + OCI_RETURN_LOBS)): ?>
                                                        <tr>
                                                            <td><?php echo $row['LOCATION_ID']; ?></td>
                                                            <td><?php echo $row['STATE_NAME']; ?></td>
                                                            <td><?php echo $row['LOCATION_NAME']; ?></td>
                                                            <td><?php echo $row['ADDRESS']; ?></td>
                                                            <td><?php echo $row['LOCATION_DESC']; ?></td>
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