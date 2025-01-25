<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Status
if (isset($_POST['add_status'])) {
    $status_type = $_POST['status_type'];
    $status_desc = $_POST['status_desc'];

    // Insert query without STATUS_ID
    $sql = "INSERT INTO STATUS (STATUS_TYPE, STATUS_DESC) 
            VALUES (:status_type, :status_desc)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':status_type', $status_type);
    oci_bind_by_name($stmt, ':status_desc', $status_desc);
    oci_execute($stmt);
    echo "<p style='color: green;'>Status added successfully!</p>";
}

// Handle Update Status
if (isset($_POST['update_status'])) {
    $status_id = $_POST['status_id'];
    $status_type = $_POST['status_type'];
    $status_desc = $_POST['status_desc'];

    $sql = "UPDATE STATUS 
            SET STATUS_TYPE = :status_type, 
                STATUS_DESC = :status_desc 
            WHERE STATUS_ID = :status_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_bind_by_name($stmt, ':status_type', $status_type);
    oci_bind_by_name($stmt, ':status_desc', $status_desc);
    oci_execute($stmt);
    echo "<p style='color: green;'>Status updated successfully!</p>";
}

// Handle Delete Status
if (isset($_POST['delete_status'])) {
    $status_id = $_POST['status_id'];

    $sql = "DELETE FROM STATUS WHERE STATUS_ID = :status_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':status_id', $status_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Status deleted successfully!</p>";
}

// Query to fetch statuses
$sql = "SELECT STATUS_ID, STATUS_TYPE, STATUS_DESC FROM STATUS";
$rsStatus = oci_parse($conn, $sql);
oci_execute($rsStatus);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Status</title>
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
                            <a href="status.php" class="nav-link active">
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
                            <h1 class="m-0">Status</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Status List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#add" data-toggle="tab">Add Status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Status</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Status List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Add Status Tab -->
                                        <div class="tab-pane active" id="add">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="status_type">Status Type</label>
                                                    <input type="text" class="form-control" name="status_type" placeholder="Status Type" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_desc">Status Description</label>
                                                    <input type="text" class="form-control" name="status_desc" placeholder="Status Description" required>
                                                </div>
                                                <button type="submit" name="add_status" class="btn btn-primary">Add Status</button>
                                            </form>
                                        </div>

                                        <!-- Update Status Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_type">Status Type</label>
                                                    <input type="text" class="form-control" name="status_type" placeholder="Status Type" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="status_desc">Status Description</label>
                                                    <input type="text" class="form-control" name="status_desc" placeholder="Status Description" required>
                                                </div>
                                                <button type="submit" name="update_status" class="btn btn-info">Update Status</button>
                                            </form>
                                        </div>

                                        <!-- Delete Status Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="status_id">Status ID</label>
                                                    <input type="number" class="form-control" name="status_id" placeholder="Status ID" required>
                                                </div>
                                                <button type="submit" name="delete_status" class="btn btn-danger">Delete Status</button>
                                            </form>
                                        </div>

                                        <!-- Status List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Status ID</th>
                                                        <th>Status Type</th>
                                                        <th>Status Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = oci_fetch_array($rsStatus, OCI_ASSOC + OCI_RETURN_LOBS)): ?>
                                                        <tr>
                                                            <td><?php echo $row['STATUS_ID']; ?></td>
                                                            <td><?php echo $row['STATUS_TYPE']; ?></td>
                                                            <td><?php echo $row['STATUS_DESC']; ?></td>
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