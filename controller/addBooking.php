<?php
include 'db/db.php'; // Include your database connection file

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $pickupLocation = $_POST['pickup_location'];
    $dropoffLocation = $_POST['dropoff_location'];
    $bookingDate = $_POST['booking_date'];
    $vehicleId = $_POST['vehicle_id'];
    $clientId = 1; // Replace with actual client ID, possibly from session or another source
    $statusId = 1; // Assuming 1 is the default status for a new booking

    // Prepare the SQL statement
    $sql = "INSERT INTO BOOKINGS (BOOKING_DATE, PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, CLIENT_ID, VEHICLE_ID, STATUS_ID) 
              VALUES (:booking_date, :pickup_location, :dropoff_location, :client_id, :vehicle_id, :status_id)";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':booking_date', $bookingDate);
    $stmt->bindParam(':pickup_location', $pickupLocation);
    $stmt->bindParam(':dropoff_location', $dropoffLocation);
    $stmt->bindParam(':client_id', $clientId);
    $stmt->bindParam(':vehicle_id', $vehicleId);
    $stmt->bindParam(':status_id', $statusId);

    // Execute the statement
    if ($stmt->execute()) {
      echo "Booking successfully created!";
    } else {
      echo "Error: " . $stmt->errorInfo()[2];
    }
  } else {
    echo "Invalid request method.";
  }
?>