<?php
include 'db/db.php'; // Include the Oracle connection

// Handle Add Client
if (isset($_POST['add_client'])) {
    $client_id = $_POST['client_id'];
    $client_name = $_POST['client_name'];
    $client_pnum = $_POST['client_pnum'];
    $client_type = $_POST['client_type'];

    // Check if CLIENT_ID already exists
    $check_sql = "SELECT COUNT(*) FROM CLIENTS WHERE CLIENT_ID = :client_id";
    $check_stmt = oci_parse($conn, $check_sql);
    oci_bind_by_name($check_stmt, ':client_id', $client_id);
    oci_execute($check_stmt);
    $row = oci_fetch_assoc($check_stmt);

    if ($row['COUNT(*)'] > 0) {
        echo "<p style='color: red;'>Error: Client ID already exists. Please use a unique Client ID.</p>";
    } else {
        // Insert query with CLIENT_ID
        $sql = "INSERT INTO CLIENTS (CLIENT_ID, CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE) 
                VALUES (:client_id, :client_name, :client_pnum, :client_type)";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':client_id', $client_id);
        oci_bind_by_name($stmt, ':client_name', $client_name);
        oci_bind_by_name($stmt, ':client_pnum', $client_pnum);
        oci_bind_by_name($stmt, ':client_type', $client_type);
        oci_execute($stmt);
        echo "<p style='color: green;'>Client added successfully!</p>";
    }
}

// Handle Update Client
if (isset($_POST['update_client'])) {
    $client_id = $_POST['client_id'];
    $client_name = $_POST['client_name'];
    $client_pnum = $_POST['client_pnum'];
    $client_type = $_POST['client_type'];

    $sql = "UPDATE CLIENTS 
            SET CLIENT_NAME = :client_name, 
                CLIENT_PNUM = :client_pnum, 
                CLIENT_TYPE = :client_type 
            WHERE CLIENT_ID = :client_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':client_id', $client_id);
    oci_bind_by_name($stmt, ':client_name', $client_name);
    oci_bind_by_name($stmt, ':client_pnum', $client_pnum);
    oci_bind_by_name($stmt, ':client_type', $client_type);
    oci_execute($stmt);
    echo "<p style='color: green;'>Client updated successfully!</p>";
}

// Handle Delete Client
if (isset($_POST['delete_client'])) {
    $client_id = $_POST['client_id'];

    $sql = "DELETE FROM CLIENTS WHERE CLIENT_ID = :client_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':client_id', $client_id);
    oci_execute($stmt);
    echo "<p style='color: green;'>Client deleted successfully!</p>";
}

// Query to fetch clients
$sql = "SELECT CLIENT_ID, CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE FROM CLIENTS";
$rsClient = oci_parse($conn, $sql);
oci_execute($rsClient); // Execute the query for the first time
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Clients</title>
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
                            <a href="client.php" class="nav-link active">
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
                            <h1 class="m-0">Clients</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Tabs for Add, Update, Delete, and Clients List -->
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#update" data-toggle="tab">Update Client</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#delete" data-toggle="tab">Delete Client</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#list" data-toggle="tab">Clients List</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Update Client Tab -->
                                        <div class="tab-pane" id="update">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="client_id">Client ID</label>
                                                    <select class="form-control" id="client_id" name="client_id" required>
                                                        <option value="">Select Client ID</option>
                                                        <?php 
                                                        // Reset the result set pointer
                                                        oci_execute($rsClient);
                                                        while ($row = oci_fetch_assoc($rsClient)): ?>
                                                            <option value="<?php echo $row['CLIENT_ID']; ?>"><?php echo $row['CLIENT_ID']; ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="client_name">Client Name</label>
                                                    <input type="text" class="form-control" id="client_name" name="client_name" placeholder="Client Name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="client_pnum">Client Phone Number</label>
                                                    <input type="text" class="form-control" id="client_pnum" name="client_pnum" placeholder="Client Phone Number" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="client_type">Client Type</label>
                                                    <input type="text" class="form-control" id="client_type" name="client_type" placeholder="Client Type" required>
                                                </div>
                                                <button type="submit" name="update_client" class="btn btn-info">Update Client</button>
                                            </form>
                                        </div>

                                        <!-- Delete Client Tab -->
                                        <div class="tab-pane" id="delete">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label for="client_id">Client ID</label>
                                                    <input type="number" class="form-control" name="client_id" placeholder="Client ID" required>
                                                </div>
                                                <button type="submit" name="delete_client" class="btn btn-danger">Delete Client</button>
                                            </form>
                                        </div>

                                        <!-- Clients List Tab -->
                                        <div class="tab-pane" id="list">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Client ID</th>
                                                        <th>Client Name</th>
                                                        <th>Client Phone Number</th>
                                                        <th>Client Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    // Reset the result set pointer before fetching data for the table
                                                    oci_execute($rsClient);
                                                    while ($row = oci_fetch_assoc($rsClient)): ?>
                                                        <tr>
                                                            <td><?php echo $row['CLIENT_ID']; ?></td>
                                                            <td><?php echo $row['CLIENT_NAME']; ?></td>
                                                            <td><?php echo $row['CLIENT_PNUM']; ?></td>
                                                            <td><?php echo $row['CLIENT_TYPE']; ?></td>
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
            $('#client_id').change(function() {
                var clientId = $(this).val();
                if (clientId) {
                    $.ajax({
                        url: 'get_client_details.php', // PHP script to fetch client details
                        type: 'POST',
                        data: { client_id: clientId },
                        success: function(response) {
                            var clientDetails = JSON.parse(response);
                            if (clientDetails) {
                                // Populate the form fields with the fetched data
                                $('#client_name').val(clientDetails.CLIENT_NAME);
                                $('#client_pnum').val(clientDetails.CLIENT_PNUM);
                                $('#client_type').val(clientDetails.CLIENT_TYPE);
                            } else {
                                // Clear the fields if no data is found
                                $('#client_name').val('');
                                $('#client_pnum').val('');
                                $('#client_type').val('');
                            }
                        }
                    });
                } else {
                    // Clear the fields if no client is selected
                    $('#client_name').val('');
                    $('#client_pnum').val('');
                    $('#client_type').val('');
                }
            });
        });
    </script>
</body>
</html>