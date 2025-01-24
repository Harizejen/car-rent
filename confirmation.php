<?php
include 'db/db.php';
session_start();
if(!isset($_SESSION['booking_id'])) header("Location: index.php");

$booking = $conn->query("
    SELECT b.*, c.*, v.VEHICLE_NAME, h1.LOCATION_NAME as PICKUP_LOC, h2.LOCATION_NAME as DROPOFF_LOC 
    FROM CARRENTAL.BOOKINGS b
    JOIN CARRENTAL.CLIENTS c ON b.CLIENT_ID = c.CLIENT_ID
    JOIN CARRENTAL.VEHICLE v ON b.VEHICLE_ID = v.VEHICLE_ID
    JOIN CARRENTAL.HUB h1 ON b.PICKUP_LOCATION_ID = h1.LOCATION_ID
    JOIN CARRENTAL.HUB h2 ON b.DROPOFF_LOCATION_ID = h2.LOCATION_ID
    WHERE b.BOOKING_ID = ".$_SESSION['booking_id']
)->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <!-- Include your CSS files -->
</head>
<body>
    <div class="container">
        <h2>Booking Confirmation #<?= $booking['BOOKING_ID'] ?></h2>
        <p>Vehicle: <?= $booking['VEHICLE_NAME'] ?></p>
        <p>Pickup: <?= $booking['PICKUP_LOC'] ?> on <?= $booking['BOOKING_DATE'] ?></p>
        <p>Dropoff: <?= $booking['DROPOFF_LOC'] ?></p>
        <p>Duration: <?= $booking['DURATION'] ?> days</p>
        <a href="index.php" class="btn btn-primary">Return Home</a>
    </div>
</body>
</html>