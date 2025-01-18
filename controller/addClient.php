<?php
include 'db/db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $clientName = $_POST['clientName'];
    $clientPNum = $_POST['clientPNum'];
    $clientType = $_POST['clientType'];

    // Prepare the SQL statement
    $sql = "INSERT INTO CLIENTS (CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE) 
            VALUES (:client_name, :client_pnum, :client_type)";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':client_name', $clientName);
    $stmt->bindParam(':client_pnum', $clientPNum);
    $stmt->bindParam(':client_type', $clientType);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Client successfully registered!";
        // Optionally, you can redirect back to the booking page or show a success message
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request method.";
}
?>