<?php
include 'db/db.php'; // Include the database connection file

// Function to create a new booking
function createBooking($bookingDate, $pickupLocationId, $dropoffLocationId, $clientId, $vehicleId, $statusId, $duration) {
    global $dbconn;
    $sql = "INSERT INTO CARRENTAL.BOOKINGS (BOOKING_ID, BOOKING_DATE, PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, CLIENT_ID, VEHICLE_ID, STATUS_ID, DURATION) 
            VALUES (BOOKING_SEQ.NEXTVAL, :bookingDate, :pickupLocationId, :dropoffLocationId, :clientId, :vehicleId, :statusId, :duration)";
    $stmt = oci_parse($dbconn, $sql);
    
    oci_bind_by_name($stmt, ':bookingDate', $bookingDate);
    oci_bind_by_name($stmt, ':pickupLocationId', $pickupLocationId);
    oci_bind_by_name($stmt, ':dropoffLocationId', $dropoffLocationId);
    oci_bind_by_name($stmt, ':clientId', $clientId);
    oci_bind_by_name($stmt, ':vehicleId', $vehicleId);
    oci_bind_by_name($stmt, ':statusId', $statusId);
    oci_bind_by_name($stmt, ':duration', $duration);
    
    if (oci_execute($stmt)) {
        echo "Booking created successfully.<br>";
    } else {
        $e = oci_error($stmt);
        echo "Error creating booking: " . htmlentities($e['message']);
    }
}

// Function to read all bookings
function readBookings() {
    global $dbconn;
    $sql = "SELECT * FROM CARRENTAL.BOOKINGS";
    $stmt = oci_parse($dbconn, $sql);
    
    oci_execute($stmt);
    
    while ($row = oci_fetch_assoc($stmt)) {
        echo "Booking ID: " . $row['BOOKING_ID'] . ", Booking Date: " . $row['BOOKING_DATE'] . ", Duration: " . $row['DURATION'] . "<br>";
    }
}

// Function to update a booking
function updateBooking($bookingId, $bookingDate, $pickupLocationId, $dropoffLocationId, $clientId, $vehicleId, $statusId, $duration) {
    global $dbconn;
    $sql = "UPDATE CARRENTAL.BOOKINGS SET BOOKING_DATE = :bookingDate, PICKUP_LOCATION_ID = :pickupLocationId, 
            DROPOFF_LOCATION_ID = :dropoffLocationId, CLIENT_ID = :clientId, VEHICLE_ID = :vehicleId, 
            STATUS_ID = :statusId, DURATION = :duration WHERE BOOKING_ID = :bookingId";
    $stmt = oci_parse($dbconn, $sql);
    
    oci_bind_by_name($stmt, ':bookingDate', $bookingDate);
    oci_bind_by_name($stmt, ':pickupLocationId', $pickupLocationId);
    oci_bind_by_name($stmt, ':dropoffLocationId', $dropoffLocationId);
    oci_bind_by_name($stmt, ':clientId', $clientId);
    oci_bind_by_name($stmt, ':vehicleId', $vehicleId);
    oci_bind_by_name($stmt, ':statusId', $statusId);
    oci_bind_by_name($stmt, ':duration', $duration);
    oci_bind_by_name($stmt, ':bookingId', $bookingId);
    
    if (oci_execute($stmt)) {
        echo "Booking updated successfully.<br>";
    } else {
        $e = oci_error($stmt);
        echo "Error updating booking: " . htmlentities($e['message']);
    }
}

// Function to delete a booking
function deleteBooking($bookingId) {
    global $dbconn;
    $sql = "DELETE FROM CARRENTAL.BOOKINGS WHERE BOOKING_ID = :bookingId";
    $stmt = oci_parse($dbconn, $sql);
    
    oci_bind_by_name($stmt, ':bookingId', $bookingId);
    
    if (oci_execute($stmt)) {
        echo "Booking deleted successfully.<br>";
    } else {
        $e = oci_error($stmt);
        echo "Error deleting booking: " . htmlentities($e['message']);
    }
}

// Example usage
// Uncomment the following lines to test the functions
createBooking('2023-10-01', 1, 2, 1, 1, 1, 3.5);
readBookings();
updateBooking(1, '2023-10-02', 1, 2, 1, 1, 1, 4.0);
deleteBooking(1);
?>
