<?php
include 'db/db.php'; // Include the Oracle connection

if (isset($_POST['client_id'])) {
    $client_id = $_POST['client_id'];

    // Query to fetch client details based on client_id
    $sql = "SELECT CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE 
            FROM CLIENTS 
            WHERE CLIENT_ID = :client_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':client_id', $client_id);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        // Return client details as JSON
        echo json_encode($row);
    } else {
        echo json_encode(array()); // Return empty JSON if no data found
    }
}
?>