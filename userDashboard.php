<?php
include 'db/db.php';
session_start();
if (!isset($_SESSION['client_id']))
  header("Location: login.php");

// Fetch user data
$userQuery = "SELECT * FROM CARRENTAL.CLIENTS WHERE CLIENT_ID = :client_id";
$stmt = oci_parse($conn, $userQuery);
oci_bind_by_name($stmt, ":client_id", $_SESSION['client_id']);
oci_execute($stmt);
$user = oci_fetch_assoc($stmt);

// Fetch rides (bookings) with driver and location details
$rideQuery = "SELECT 
                b.BOOKING_ID,
                b.BOOKING_DATE,
                h1.LOCATION_NAME AS PICKUP_LOCATION,
                h2.LOCATION_NAME AS DROPOFF_LOCATION,
                v.VEHICLE_NAME,
                v.VEHICLE_TYPE,
                d.DRIVER_ID,
                d.DRIVER_NAME,
                d.RATING AS DRIVER_RATING,
                s.STATUS_TYPE,
                p.AMOUNT AS FARE,
                p.PAYMENT_METHOD,
                f.FEEDBACK_ID,
                f.RATINGVALUE,
                f.COMMENTS
             FROM CARRENTAL.BOOKINGS b
             JOIN CARRENTAL.HUB h1 ON b.PICKUP_LOCATION_ID = h1.LOCATION_ID
             JOIN CARRENTAL.HUB h2 ON b.DROPOFF_LOCATION_ID = h2.LOCATION_ID
             JOIN CARRENTAL.VEHICLE v ON b.VEHICLE_ID = v.VEHICLE_ID
             JOIN CARRENTAL.DRIVERS d ON v.DRIVER_ID = d.DRIVER_ID
             JOIN CARRENTAL.STATUS s ON b.STATUS_ID = s.STATUS_ID
             LEFT JOIN CARRENTAL.PAYMENTS p ON b.BOOKING_ID = p.BOOKING_ID
             LEFT JOIN CARRENTAL.FEEDBACKS f ON b.BOOKING_ID = f.BOOKING_ID
             WHERE b.CLIENT_ID = :client_id
             ORDER BY b.BOOKING_DATE DESC";

$stmt = oci_parse($conn, $rideQuery);
oci_bind_by_name($stmt, ":client_id", $_SESSION['client_id']);
oci_execute($stmt);

$rides = [];
while ($row = oci_fetch_assoc($stmt)) {
  $rides[] = $row;
}

// Calculate stats
$totalRides = count($rides);
$upcomingRides = array_filter($rides, fn($ride) => $ride['STATUS_TYPE'] === 'Booked');
$averageRatingQuery = "SELECT AVG(f.RATINGVALUE) AS AVG_RATING 
                       FROM CARRENTAL.FEEDBACKS f
                       JOIN CARRENTAL.BOOKINGS b ON f.BOOKING_ID = b.BOOKING_ID
                       WHERE b.CLIENT_ID = :client_id";
$stmt = oci_parse($conn, $averageRatingQuery);
oci_bind_by_name($stmt, ":client_id", $_SESSION['client_id']);
oci_execute($stmt);
$avgRating = oci_fetch_assoc($stmt)['AVG_RATING'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Existing head content remains the same -->
  <!-- header.php -->
  <meta charset="utf-8">
  <title>Cental - Car Rent Website Template</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Lato:wght@400;700&display=swap"
    rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="css/style.css" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dashboard-nav .nav-link {
      color: #333;
      font-weight: 500;
      padding: 10px 15px;
      border-radius: 5px;
      margin-bottom: 5px;
      transition: background 0.3s;
    }

    .dashboard-nav .nav-link.active,
    .dashboard-nav .nav-link:hover {
      background: #007bff;
      color: #fff;
    }

    .booking-card {
      background: #fff;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
      background: #007bff;
    }

    .btn-primary {
      background: #007bff;
      border: none;
    }

    .btn-outline-danger {
      border: 1px solid #dc3545;
      color: #dc3545;
    }

    .btn-outline-primary {
      border: 1px solid #007bff;
      color: #007bff;
    }

    .btn-outline-success {
      border: 1px solid #28a745;
      color: #28a745;
    }

    .badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
    }

    .ride-type-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
    }

    .standard-ride {
      background: #e2e8f0;
      color: #1a202c;
    }

    .premium-ride {
      background: #f6e05e;
      color: #744210;
    }

    .suv-ride {
      background: #68d391;
      color: #22543d;
    }
  </style>
</head>

<body>
  <div class="container mx-auto py-5">
    <!-- Header -->
    <div class="flex justify-between items-center mb-5 glass-card p-4">
      <div>
        <h1 class="text-2xl font-bold mb-0">Welcome, <?= $user['CLIENT_NAME'] ?>! 🚖</h1>
        <p class="text-gray-600"><?= $user['CLIENT_TYPE'] ?> Account</p>
      </div>
      <div class="flex items-center">
        <img src="https://storage.googleapis.com/a1aa/image/Ygi0tZpmxqLRA11Xza7WyJ1V8Ng94TgJdCrosDex0n7wJXEKA.jpg"
          class="rounded-full w-12 h-12" alt="Profile">
        <a href="logout.php" class="btn btn-outline-danger ml-3">Logout</a>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
      <div class="stat-card">
        <div class="flex items-center">
          <div class="bg-blue-500 text-white rounded-full p-3 mr-3">
            <i class="fas fa-taxi fa-2x"></i>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-0"><?= $totalRides ?></h3>
            <p class="text-gray-500 mb-0">Total Rides</p>
          </div>
        </div>
      </div>
      <div class="stat-card">
        <div class="flex items-center">
          <div class="bg-green-500 text-white rounded-full p-3 mr-3">
            <i class="fas fa-star fa-2x"></i>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-0"><?= number_format($avgRating, 1) ?></h3>
            <p class="text-gray-500 mb-0">Average Rating</p>
          </div>
        </div>
      </div>
      <div class="stat-card">
        <div class="flex items-center">
          <div class="bg-purple-500 text-white rounded-full p-3 mr-3">
            <i class="fas fa-clock fa-2x"></i>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-0"><?= count($upcomingRides) ?></h3>
            <p class="text-gray-500 mb-0">Upcoming Rides</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
      <!-- Sidebar -->
      <div class="col-span-1">
        <div class="dashboard-nav">
          <nav class="flex flex-col">
            <a class="nav-link active" href="#"><i class="fas fa-home mr-2"></i> Dashboard</a>
            <a class="nav-link" href="#"><i class="fas fa-history mr-2"></i> Ride History</a>
            <a class="nav-link" href="#"><i class="fas fa-wallet mr-2"></i> Wallet</a>
          </nav>
        </div>
      </div>

      <!-- Content Area -->
      <div class="col-span-3">
        <!-- Book Ride Form -->
        <div class="glass-card p-4 mb-4">
          <h4 class="text-xl font-bold mb-4">🚕 Book a Ride</h4>
          <form action="book_ride.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 font-bold mb-2">Pickup Location</label>
                <select class="w-full p-2 border rounded" name="pickup_location" required>
                  <?php
                  $hubQuery = "SELECT * FROM CARRENTAL.HUB";
                  $stmt = oci_parse($conn, $hubQuery);
                  oci_execute($stmt);
                  while ($hub = oci_fetch_assoc($stmt)): ?>
                    <option value="<?= $hub['LOCATION_ID'] ?>">
                      <?= $hub['LOCATION_NAME'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-bold mb-2">Destination</label>
                <select class="w-full p-2 border rounded" name="dropoff_location" required>
                  <?php
                  oci_execute($stmt); // Re-use hub query
                  while ($hub = oci_fetch_assoc($stmt)): ?>
                    <option value="<?= $hub['LOCATION_ID'] ?>">
                      <?= $hub['LOCATION_NAME'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-bold mb-2">Vehicle Type</label>
                <select class="w-full p-2 border rounded" name="vehicle_type" required>
                  <option value="Hatchback">Standard (Myvi)</option>
                  <option value="Sedan">Premium (Vios)</option>
                  <option value="SUV">SUV (X70)</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-bold mb-2">Payment Method</label>
                <select class="w-full p-2 border rounded" name="payment_method" required>
                  <option value="Cash">Cash</option>
                  <option value="Touch 'n Go">Touch 'n Go</option>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-primary mt-4 w-full">
              <i class="fas fa-car mr-2"></i> Request Ride
            </button>
          </form>
        </div>

        <!-- Ride History -->
        <div class="glass-card p-4">
          <h4 class="text-xl font-bold mb-4">📝 Ride History</h4>
          <div class="grid grid-cols-1 gap-4">
            <?php foreach ($rides as $ride): ?>
              <div class="booking-card p-4">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <div class="flex items-center mb-2">
                      <span class="ride-type-badge <?= strtolower($ride['VEHICLE_TYPE']) ?>-ride">
                        <?= $ride['VEHICLE_TYPE'] ?>
                      </span>
                      <span class="ml-2 text-sm text-gray-500">
                        <?= date('D, M j H:i', strtotime($ride['BOOKING_DATE'])) ?>
                      </span>
                    </div>
                    <h3 class="text-lg font-semibold">
                      <?= $ride['PICKUP_LOCATION'] ?> → <?= $ride['DROPOFF_LOCATION'] ?>
                    </h3>
                    <p class="text-gray-600 text-sm">
                      Driver: <?= $ride['DRIVER_NAME'] ?>
                      (⭐ <?= $ride['DRIVER_RATING'] ?>)
                    </p>
                  </div>
                  <span class="badge <?= match ($ride['STATUS_TYPE']) {
                    'Completed' => 'bg-green-500',
                    'Booked' => 'bg-blue-500',
                    'Payment Pending' => 'bg-yellow-500',
                    default => 'bg-gray-500'
                  } ?> text-white px-3 py-1 rounded-full">
                    <?= $ride['STATUS_TYPE'] ?>
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <div>
                    <p class="text-xl font-bold text-green-600">
                      RM <?= number_format($ride['FARE'], 2) ?>
                    </p>
                    <p class="text-sm text-gray-500">
                      <?= $ride['PAYMENT_METHOD'] ?> •
                      <?= $ride['VEHICLE_NAME'] ?>
                    </p>
                  </div>
                  <div class="flex gap-2">
                    <?php if ($ride['STATUS_TYPE'] === 'Completed'): ?>
                      <?php if (empty($ride['FEEDBACK_ID'])): ?>
                        <button onclick="openModal(<?= $ride['BOOKING_ID'] ?>, <?= $ride['DRIVER_ID'] ?>)"
                          class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-star mr-1"></i> Rate
                        </button>
                      <?php else: ?>
                        <div class="flex items-center text-yellow-500">
                          <?= str_repeat('★', $ride['RATINGVALUE']) ?>
                          <?= str_repeat('☆', 5 - $ride['RATINGVALUE']) ?>
                        </div>
                      <?php endif; ?>
                    <?php else: ?>
                      <button class="btn btn-sm btn-outline-danger">
                        Cancel
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Feedback Modal -->
  <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
      <h3 class="text-xl font-bold mb-4">Rate Your Ride</h3>
      <form id="feedbackForm" action="submit_feedback.php" method="POST">
        <input type="hidden" name="booking_id" id="modalBookingId">
        <input type="hidden" name="driver_id" id="modalDriverId">

        <div class="mb-4">
          <label class="block text-gray-700 font-bold mb-2">Rating</label>
          <div class="rating-stars flex gap-2">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" class="hidden" required>
              <label for="star<?= $i ?>" class="text-2xl cursor-pointer text-gray-300 hover:text-yellow-400">★</label>
            <?php endfor; ?>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-bold mb-2">Comments</label>
          <textarea name="comments" class="w-full p-2 border rounded" rows="3"></textarea>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" onclick="closeModal()" class="btn btn-outline-danger">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(bookingId, driverId) {
      document.getElementById('modalBookingId').value = bookingId;
      document.getElementById('modalDriverId').value = driverId;
      document.getElementById('feedbackModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('feedbackModal').classList.add('hidden');
    }

    // Star rating interaction
    document.querySelectorAll('.rating-stars input').forEach(star => {
      star.addEventListener('change', function () {
        const stars = this.parentElement.querySelectorAll('label');
        const value = parseInt(this.value);
        stars.forEach((label, index) => {
          label.style.color = (5 - index) <= value ? '#f59e0b' : '#e5e7eb';
        });
      });
    });
  </script>
</body>

</html>