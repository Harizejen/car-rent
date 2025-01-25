<?php
include 'db/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    try {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            throw new Exception("Email and password are required");
        }

        $query = "SELECT CLIENT_ID, CLIENT_NAME, PASSWORD 
                FROM CARRENTAL.CLIENTS 
                WHERE EMAIL = :email";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":email", $_POST['email']);
        oci_execute($stmt);

        if ($user = oci_fetch_assoc($stmt)) {
            if (password_verify($_POST['password'], $user['PASSWORD'])) {
                $_SESSION['client_id'] = $user['CLIENT_ID'];
                $_SESSION['client_name'] = $user['CLIENT_NAME'];
                header("Location: index.php");
                exit;
            } else {
                throw new Exception("Invalid credentials");
            }
        } else {
            throw new Exception("User  not found");
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    try {
        // Validate required fields
        $required = ['name', 'pnum', 'type', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required");
            }
        }

        // Hash password
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Prepare insert statement
        $query = "INSERT INTO CARRENTAL.CLIENTS 
                (CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE, EMAIL, PASSWORD) 
                VALUES (:name, :pnum, :type, :email, :password)
                RETURNING CLIENT_ID INTO :client_id";

        $stmt = oci_parse($conn, $query);
        
        // Bind all parameters
        oci_bind_by_name($stmt, ":name", $_POST['name']);
        oci_bind_by_name($stmt, ":pnum", $_POST['pnum']);
        oci_bind_by_name($stmt, ":type", $_POST['type']);
        oci_bind_by_name($stmt, ":email", $_POST['email']);
        oci_bind_by_name($stmt, ":password", $hashed_password);
        oci_bind_by_name($stmt, ":client_id", $client_id, 32);

        if (!oci_execute($stmt)) {
            throw new Exception("Registration failed");
        }

        // Set session variables
        $_SESSION['client_id'] = $client_id;
        $_SESSION['client_name'] = $_POST['name'];
        
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
}

// Enhanced error handling
set_exception_handler(function ($e) {
    error_log("Critical Error: " . $e->getMessage());
    die("A system error occurred. Please try again later.");
});

// Vehicle query
$vehicleQuery = "SELECT veh.VEHICLE_ID, veh.VEHICLE_NAME, veh.RATE_PER_DAY 
                FROM CARRENTAL.VEHICLE veh
                JOIN CARRENTAL.STATUS sts ON veh.STATUS_ID = sts.STATUS_ID
                WHERE sts.STATUS_TYPE = 'Available'";

try {
    $stmt = oci_parse($conn, $vehicleQuery);
    if (!$stmt) {
        throw new Exception("Database query preparation failed");
    }
    
    if (!oci_execute($stmt)) {
        throw new Exception("Database query execution failed");
    }

    $vehicles = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $vehicles[] = $row;
    }
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage() . " - Query: " . $vehicleQuery);
    die("A database error occurred. Please try again later.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking'])) {
    try {
        oci_begin($conn); // Start transaction

        // Client registration with validation
        $requiredFields = [
            'client_name',
            'client_pnum',
            'client_type',
            'vehicle_id',
            'booking_date',
            'return_date'
        ];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field]))
                throw new Exception("Missing required field: $field");
        }

        // Insert client
        $clientQuery = "INSERT INTO CARRENTAL.CLIENTS (CLIENT_NAME, CLIENT_PNUM, CLIENT_TYPE) 
                       VALUES (:name, :pnum, :type)
                       RETURNING CLIENT_ID INTO :client_id";

        $stmt = oci_parse($conn, $clientQuery);
        oci_bind_by_name($stmt, ":name", $_POST['client_name']);
        oci_bind_by_name($stmt, ":pnum", $_POST['client_pnum']);
        oci_bind_by_name($stmt, ":type", $_POST['client_type']);
        oci_bind_by_name($stmt, ":client_id", $client_id, 32);
        oci_execute($stmt) or handleOracleError($stmt);

        // Get vehicle details
        $vehicle_id = $_POST['vehicle_id'];
        $vehicleQuery = "SELECT LOCATION_ID, RATE_PER_DAY 
                        FROM CARRENTAL.VEHICLE 
                        WHERE VEHICLE_ID = :vehicle_id
                        AND STATUS_ID = (SELECT STATUS_ID FROM CARRENTAL.STATUS WHERE STATUS_TYPE = 'Available')";

        $stmt = oci_parse($conn, $vehicleQuery);
        oci_bind_by_name($stmt, ":vehicle_id", $vehicle_id);
        oci_execute($stmt) or handleOracleError($stmt);
        $vehicle = oci_fetch_assoc($stmt);

        if (!$vehicle)
            throw new Exception("Selected vehicle is no longer available");

        // Validate dates
        $pickup = new DateTime($_POST['booking_date']);
        $dropoff = new DateTime($_POST['return_date']);
        if ($pickup >= $dropoff)
            throw new Exception("Return date must be after pickup date");
        $duration = $pickup->diff($dropoff)->days;

        // Insert booking
        $bookingQuery = "INSERT INTO CARRENTAL.BOOKINGS (
            BOOKING_DATE, RETURN_DATE,
            PICKUP_LOCATION_ID, DROPOFF_LOCATION_ID, 
            CLIENT_ID, VEHICLE_ID, STATUS_ID, DURATION
        ) VALUES (
            TO_DATE(:booking_date, 'YYYY-MM-DD'), 
            TO_DATE(:return_date, 'YYYY-MM-DD'),
            :pickup_loc, :dropoff_loc, 
            :client_id, :vehicle_id, 
            (SELECT STATUS_ID FROM CARRENTAL.STATUS WHERE STATUS_TYPE = 'Booked'), 
            :duration
        ) RETURNING BOOKING_ID INTO :booking_id";

        $stmt = oci_parse($conn, $bookingQuery);
        oci_bind_by_name($stmt, ":booking_date", $_POST['booking_date']);
        oci_bind_by_name($stmt, ":return_date", $_POST['return_date']);
        oci_bind_by_name($stmt, ":pickup_loc", $vehicle['LOCATION_ID']);
        oci_bind_by_name($stmt, ":dropoff_loc", $vehicle['LOCATION_ID']);
        oci_bind_by_name($stmt, ":client_id", $client_id);
        oci_bind_by_name($stmt, ":vehicle_id", $vehicle_id);
        oci_bind_by_name($stmt, ":duration", $duration);
        oci_bind_by_name($stmt, ":booking_id", $booking_id, 32);
        oci_execute($stmt) or handleOracleError($stmt);

        // Insert payment
        $amount = $duration * $vehicle['RATE_PER_DAY'];
        $paymentQuery = "INSERT INTO CARRENTAL.PAYMENTS (
            PAYMENT_METHOD, PAYMENT_DATE, AMOUNT, BOOKING_ID, STATUS_ID
        ) VALUES (
            'Online', SYSDATE, :amount, :booking_id, 
            (SELECT STATUS_ID FROM CARRENTAL.STATUS WHERE STATUS_TYPE = 'Completed')
        )";

        $stmt = oci_parse($conn, $paymentQuery);
        oci_bind_by_name($stmt, ":amount", $amount);
        oci_bind_by_name($stmt, ":booking_id", $booking_id);
        oci_execute($stmt) or handleOracleError($stmt);

        oci_commit($conn);
        $_SESSION['booking_id'] = $booking_id;
        header("Location: confirmation.php");
        exit;

    } catch (Exception $e) {
        oci_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
}

function handleOracleError($resource) {
    $error = oci_error($resource);
    error_log("Oracle Error: " . $error['message']);
    throw new Exception("Database operation failed");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cental - Car Rent Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Adjust brightness of the carousel image */
        #carouselImage {
            filter: brightness(1.2);
            /* Adjust brightness here */
        }
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid topbar bg-secondary d-none d-xl-block w-100">
        <div class="container">
            <div class="row gx-0 align-items-center" style="height: 45px;">
                <div class="col-lg-6 text-center text-lg-start mb-lg-0">
                    <div class="d-flex flex-wrap">
                        <a href="tel:+01234567890" class="text-muted me-4"><i
                                class="fas fa-phone-alt text-primary me-2"></i>+01234567890</a>
                        <a href="mailto:example@gmail.com" class="text-muted me-0"><i
                                class="fas fa-envelope text-primary me-2"></i>Cental@gmail.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid nav-bar sticky-top px-0 px-lg-4 py-2 py-lg-0 ">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a href="" class="navbar-brand p-0">
                    <h1 class="display-6 text-primary"><i class="fas fa-car-alt me-3"></i>Cental</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Service</a>
                        <a href="blog.php" class="nav-item nav-link">Blog</a>

                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu m-0">
                                <a href="feature.php" class="dropdown-item">Our Feature</a>
                                <a href="cars.php" class="dropdown-item">Our Cars</a>
                                <a href="team.php" class="dropdown-item">Our Team</a>
                                <a href="testimonial.php" class="dropdown-item">Testimonial</a>
                                <a href="404.php" class="dropdown-item">404 Page</a>
                            </div>
                        </div>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                    </div>
                    <a href="CRUD.php" class="btn btn-primary rounded-pill py-2 px-4">Manage Bookings</a>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar & Hero End -->

    <!-- Carousel Start -->
    <div class="header-carousel h-25">
        <div class="carousel-inner" role="listbox">
            <div>
                <img src="img/carousel-2.jpg" class="img-fluid w-100" alt="First slide" />
                <div class="carousel-caption">
                    <div class="container py-4">
                        <div class="row g-5">
                            <div class="col-lg-6 fadeInLeft animated">
                                <div class="bg-secondary rounded p-5 shadow-lg">
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                                        <?php unset($_SESSION['error']); ?>
                                    <?php endif; ?>

                                    <?php if (!isset($_SESSION['client_id'])): ?>
                                        <!-- Login/Signup Tabs -->
                                        <ul class="nav nav-tabs mb-4" id="authTabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#login">Login</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#signup">Sign Up</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <!-- Login Form -->
                                            <div class="tab-pane fade show active" id="login">
                                                <form action="index.php" method="POST">
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Email</label>
                                                        <input type="email" class="form-control" name="email" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Password</label>
                                                        <input type="password" class="form-control" name="password" required>
                                                    </div>
                                                    <button type="submit" name="login" class="btn btn-light w-100">Login</button>
                                                </form>
                                            </div>

                                            <!-- Signup Form -->
                                            <div class="tab-pane fade" id="signup">
                                                <form action="index.php" method="POST">
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Full Name</label>
                                                        <input type="text" class="form-control" name="name" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Phone Number</label>
                                                        <input type="tel" class="form-control" name="pnum" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Client Type</label>
                                                        <select class="form-select" name="type" required>
                                                            <option value="individual">Individual</option>
                                                            <option value="business">Business</option>
                                                            <option value="family">Family</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Email</label>
                                                        <input type="email" class="form-control" name="email" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white">Password</label>
                                                        <input type="password" class="form-control" name="password" required>
                                                    </div>
                                                    <button type="submit" name="signup" class="btn btn-light w-100">Sign Up</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Booking Form for Logged-in Users -->
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <?php if (isset($_SESSION['client_name'])): ?>
                                                <h5 class="text-white">Welcome, <?= htmlspecialchars($_SESSION['client_name']) ?></h5>
                                            <?php endif; ?>
                                            <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
                                        </div>

                                        <form id="bookingForm" action="index.php" method="POST" novalidate>
                                            <div class="row g-3">
                                                <!-- Client Registration Fields -->
                                                <div class="col-12">
                                                    <label class="form-label text-white">Full Name</label>
                                                    <input type="text" class="form-control" name="client_name"
                                                        placeholder="Full Name"
                                                        value="<?= htmlspecialchars($_POST['client_name'] ?? '') ?>"
                                                        required>
                                                </div>
                                                <div class="col-12">
                                                    <input type="tel" class="form-control" name="client_pnum"
                                                        placeholder="Phone Number" required>
                                                </div>
                                                <div class="col-12">
                                                    <select class="form-select" name="client_type" required>
                                                        <option value="individual">Individual</option>
                                                        <option value="business">Business</option>
                                                        <option value="family">Family</option>
                                                    </select>
                                                </div>

                                                <!-- Vehicle Selection -->
                                                <div class="col-12">
                                                    <label class="form-label text-white">Select Vehicle</label>
                                                    <select class="form-select" name="vehicle_id" required>
                                                        <?php if (empty($vehicles)): ?>
                                                            <option disabled selected>No vehicles available</option>
                                                        <?php else: ?>
                                                            <option value="">Choose a vehicle</option>
                                                            <?php foreach ($vehicles as $v): ?>
                                                                <option value="<?= htmlspecialchars($v['VEHICLE_ID']) ?>"
                                                                    <?= ($_POST['vehicle_id'] ?? '') == $v['VEHICLE_ID'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($v['VEHICLE_NAME']) ?> -
                                                                    RM<?= number_format($v['RATE_PER_DAY'], 2) ?>/day
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>

                                                <!-- Date/Time Selection -->
                                                <div class="col-12">
                                                    <label class="form-label text-white">Pickup Date</label>
                                                    <input type="date" class="form-control" name="booking_date" 
                                                        min="<?= date('Y-m-d') ?>" 
                                                        value="<?= htmlspecialchars($_POST['booking_date'] ?? '') ?>" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label text-white">Return Date</label>
                                                    <input type="date" class="form-control" name="return_date" required>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" name="booking"
                                                        class="btn btn-light w-100 py-2">Book Now</button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-6 d-none d-lg-flex fadeInRight animated" data-animation="fadeInRight"
                                data-delay="1s" style="animation-delay: 1s;">
                                <div class="text-start text-white">
                                    <h1 class="display-5 fw-bolder text-primary">Your Convenient, Our Desire</h1>
                                    <p class="text-black">Best Service, Best Deals</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Features Start -->
    <div class="container-fluid feature py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-5 text-capitalize mb-3">Cental <span class="text-primary">Features</span></h1>
                <p class="mb-0">Provide safe and convenient transport by car</p>
            </div>
            <div class="row g-4 align-items-center">
                <div class="col-xl-4">
                    <div class="row gy-4 gx-0">
                        <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <span class="fa fa-trophy fa-2x"></span>
                                </div>
                                <div class="ms-4">
                                    <h5 class="mb-3">First Class Services</h5>
                                    <p class="mb-0">Professional and trained drivers to provide safe and reliable
                                        services.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <span class="fa fa-road fa-2x"></span>
                                </div>
                                <div class="ms-4">
                                    <h5 class="mb-3">Secured Insurance Protection</h5>
                                    <p class="mb-0">Safe journey, worry free.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                    <img src="img/features-img.png" class="img-fluid w-100" style="object-fit: cover;" alt="Img">
                </div>
                <div class="col-xl-4">
                    <div class="row gy-4 gx-0">
                        <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="feature-item justify-content-end">
                                <div class="text-end me-4">
                                    <h5 class="mb-3">Quality at Minimum</h5>
                                    <p class="mb-0">We assure ride comfort with no compensation to timeliness.</p>
                                </div>
                                <div class="feature-icon">
                                    <span class="fa fa-tag fa-2x"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="feature-item justify-content-end">
                                <div class="text-end me-4">
                                    <h5 class="mb-3">One and Done Payment</h5>
                                    <p class="mb-0">Drivers paid by the day, no extra charges.</p>
                                </div>
                                <div class="feature-icon">
                                    <span class="fa fa-map-pin fa-2x"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->

    <div class="container-fluid counter bg-secondary py-5">
        <div class="container py-5">
            <div class="row g-5">
                <?php
                // Fetch dynamic counts from database
                try {
                    // Happy Clients Count
                    $clientQuery = "SELECT COUNT(*) AS client_count FROM CARRENTAL.CLIENTS";
                    $stmt = oci_parse($conn, $clientQuery);
                    oci_execute($stmt);
                    $clientCount = oci_fetch_assoc($stmt)['CLIENT_COUNT'];

                    // Available Cars Count
                    $carQuery = "SELECT COUNT(*) AS car_count 
                               FROM CARRENTAL.VEHICLE v
                               JOIN CARRENTAL.STATUS s ON v.STATUS_ID = s.STATUS_ID
                               WHERE s.STATUS_TYPE = 'Available'";
                    $stmt = oci_parse($conn, $carQuery);
                    oci_execute($stmt);
                    $carCount = oci_fetch_assoc($stmt)['CAR_COUNT'];

                    // Car Centers (Hubs) Count
                    $hubQuery = "SELECT COUNT(*) AS hub_count FROM CARRENTAL.HUB";
                    $stmt = oci_parse($conn, $hubQuery);
                    oci_execute($stmt);
                    $hubCount = oci_fetch_assoc($stmt)['HUB_COUNT'];

                } catch (Exception $e) {
                    // Fallback values if database connection fails
                    $clientCount = 829;
                    $carCount = 56;
                    $hubCount = 127;
                }
                ?>

                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="counter-item text-center">
                        <div class="counter-item-icon mx-auto">
                            <i class="fas fa-thumbs-up fa-2x"></i>
                        </div>
                        <div class="counter-counting my-3">
                            <span class="text-white fs-2 fw-bold" data-toggle="counter-up">
                                <?= htmlspecialchars($clientCount) ?>
                            </span>
                            <span class="h1 fw-bold text-white">+</span>
                        </div>
                        <h4 class="text-white mb-0">Happy Clients</h4>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="counter-item text-center">
                        <div class="counter-item-icon mx-auto">
                            <i class="fas fa-car-alt fa-2x"></i>
                        </div>
                        <div class="counter-counting my-3">
                            <span class="text-white fs-2 fw-bold" data-toggle="counter-up">
                                <?= htmlspecialchars($carCount) ?>
                            </span>
                            <span class="h1 fw-bold text-white">+</span>
                        </div>
                        <h4 class="text-white mb-0">Available Cars</h4>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="counter-item text-center">
                        <div class="counter-item-icon mx-auto">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <div class="counter-counting my-3">
                            <span class="text-white fs-2 fw-bold" data-toggle="counter-up">
                                <?= htmlspecialchars($hubCount) ?>
                            </span>
                            <span class="h1 fw-bold text-white">+</span>
                        </div>
                        <h4 class="text-white mb-0">Car Centers</h4>
                    </div>
                </div>

                <!-- Static Kilometers Counter -->
                <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="counter-item text-center">
                        <div class="counter-item-icon mx-auto">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="counter-counting my-3">
                            <span class="text-white fs-2 fw-bold" data-toggle="counter-up">589</span>
                            <span class="h1 fw-bold text-white">+</span>
                        </div>
                        <h4 class="text-white mb-0">Total kilometers</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact Counter End -->

    <!-- Car categories Start -->
    <div class="container-fluid categories pb-5 mt-4">
        <div class="container pb-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-5 text-capitalize mb-3">Vehicle <span class="text-primary">Categories</span></h1>
                <p class="mb-0">Various car types to choose from!</p>
            </div>
            <div class="categories-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="categories-item p-4">
                    <div class="categories-item-inner">
                        <div class="categories-img rounded-top">
                            <img src="img/car-1.png" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="categories-content rounded-bottom p-4">
                            <h4>Mercedes Benz R3</h4>
                            <div class="categories-review mb-4">
                                <div class="me-3">4.5 Review</div>
                                <div class="d-flex justify-content-center text-secondary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-body"></i>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="bg-white text-primary rounded-pill py-2 px-4 mb-0">$99:00/Day</h4>
                            </div>
                            <div class="row gy-2 gx-0 text-center mb-4">
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-users text-dark"></i> <span class="text-body ms-1">4 Seat</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">AT/MT</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-gas-pump text-dark"></i> <span class="text-body ms-1">Petrol</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">2015</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-cogs text-dark"></i> <span class="text-body ms-1">AUTO</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-road text-dark"></i> <span class="text-body ms-1">27K</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-primary rounded-pill d-flex justify-content-center py-3">Book
                                Now</a>
                        </div>
                    </div>
                </div>
                <div class="categories-item p-4">
                    <div class="categories-item-inner">
                        <div class="categories-img rounded-top">
                            <img src="img/car-2.png" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="categories-content rounded-bottom p-4">
                            <h4>Toyota Corolla Cross</h4>
                            <div class="categories-review mb-4">
                                <div class="me-3">3.5 Review</div>
                                <div class="d-flex justify-content-center text-secondary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-body"></i>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="bg-white text-primary rounded-pill py-2 px-4 mb-0">$128:00/Day</h4>
                            </div>
                            <div class="row gy-2 gx-0 text-center mb-4">
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-users text-dark"></i> <span class="text-body ms-1">4 Seat</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">AT/MT</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-gas-pump text-dark"></i> <span class="text-body ms-1">Petrol</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">2015</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-cogs text-dark"></i> <span class="text-body ms-1">AUTO</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-road text-dark"></i> <span class="text-body ms-1">27K</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-primary rounded-pill d-flex justify-content-center py-3">Book
                                Now</a>
                        </div>
                    </div>
                </div>
                <div class="categories-item p-4">
                    <div class="categories-item-inner">
                        <div class="categories-img rounded-top">
                            <img src="img/car-3.png" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="categories-content rounded-bottom p-4">
                            <h4>Tesla Model S Plaid</h4>
                            <div class="categories-review mb-4">
                                <div class="me-3">3.8 Review</div>
                                <div class="d-flex justify-content-center text-secondary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star text-body"></i>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="bg-white text-primary rounded-pill py-2 px-4 mb-0">$170:00/Day</h4>
                            </div>
                            <div class="row gy-2 gx-0 text-center mb-4">
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-users text-dark"></i> <span class="text-body ms-1">4 Seat</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">AT/MT</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-gas-pump text-dark"></i> <span class="text-body ms-1">Petrol</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">2015</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-cogs text-dark"></i> <span class="text-body ms -1">AUTO</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-road text-dark"></i> <span class="text-body ms-1">27K</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-primary rounded-pill d-flex justify-content-center py-3">Book
                                Now</a>
                        </div>
                    </div>
                </div>
                <div class="categories-item p-4">
                    <div class="categories-item-inner">
                        <div class="categories-img rounded-top">
                            <img src="img/car-4.png" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="categories-content rounded-bottom p-4">
                            <h4>Hyundai Kona Electric</h4>
                            <div class="categories-review mb-4">
                                <div class="me-3">4.8 Review</div>
                                <div class="d-flex justify-content-center text-secondary">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="bg-white text-primary rounded-pill py-2 px-4 mb-0">$187:00/Day</h4>
                            </div>
                            <div class="row gy-2 gx-0 text-center mb-4">
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-users text-dark"></i> <span class="text-body ms-1">4 Seat</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">AT/MT</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-gas-pump text-dark"></i> <span class="text-body ms-1">Petrol</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-car text-dark"></i> <span class="text-body ms-1">2015</span>
                                </div>
                                <div class="col-4 border-end border-white">
                                    <i class="fa fa-cogs text-dark"></i> <span class="text-body ms-1">AUTO</span>
                                </div>
                                <div class="col-4">
                                    <i class="fa fa-road text-dark"></i> <span class="text-body ms-1">27K</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-primary rounded-pill d-flex justify-content-center py-3">Book
                                Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Car categories End -->

    <!-- Car Steps Start -->
    <div class="container-fluid steps py-5">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h1 class="display-5 text-capitalize text-white mb-3">Cental<span class="text-primary"> Process</span>
                </h1>
                <p class="mb-0 text-white">Get your car in 3 easy steps.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="steps-item p-4 mb-4">
                        <h4>Choose A Car</h4>
                        <p class="mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad, dolorem!</p>
                        <div class="setps-number">01.</div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="steps-item p-4 mb-4">
                        <h4>Get A Driver</h4>
                        <p class="mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad, dolorem!</p>
                        <div class="setps-number">02.</div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="steps-item ```html
 p-4 mb-4">
                        <h4>Set Location</h4>
                        <p class="mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ad, dolorem!</p>
                        <div class="setps-number">03.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Car Steps End -->

    <!-- Footer Start -->
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <div class="footer-item">
                            <h4 class="text-white mb-4">About Us</h4>
                            <p class="mb-3">Dolor amet sit justo amet elitr clita ipsum elitr est. Lorem ipsum dolor sit
                                amet, consectetur adipiscing elit consectetur adipiscing elit.</p>
                        </div>
                        <div class="position-relative">
                            <input class="form-control rounded-pill w-100 py-3 ps-4 pe-5" type="text"
                                placeholder="Enter your email">
                            <button type="button"
                                class="btn btn-secondary rounded-pill position-absolute top-0 end-0 py-2 mt-2 me-2">Subscribe</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Quick Links</h4>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> About</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> Cars</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> Car Types</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> Team</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> Contact us</a>
                        <a href="#"><i class="fas fa-angle-right me-2"></i> Terms & Conditions</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Business Hours</h4>
                        <div class="mb-3">
                            <h6 class="text-muted mb-0">Mon - Friday:</h6>
                            <p class="text-white mb-0">09.00 am to 07.00 pm</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted mb-0">Saturday:</h6>
                            <p class="text-white mb-0">10.00 am to 05.00 pm</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted mb-0">Vacation:</h6>
                            <p class="text-white mb-0">All Sunday is our vacation</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-white mb-4">Contact Info</h4>
                        <a href="#"><i class="fa fa-map-marker-alt me-2"></i> 123 Street, New York, USA</a>
                        <a href="mailto:info@example.com"><i class="fas fa-envelope me-2"></i> info@example.com</a>
                        <a href="tel:+012 345 67890"><i class="fas fa-phone me-2"></i> +012 345 67890</a>
                        <a href="tel:+012 345 67890" class="mb-3"><i class="fas fa-print me-2"></i> +012 345 67890</a>
                        <div class="d-flex">
                            <a class="btn btn-secondary btn-md-square rounded-circle me-3" href=""><i
                                    class="fab fa-facebook-f text-white"></i></a>
                            <a class="btn btn-secondary btn-md-square rounded-circle me-3" href=""><i
                                    class="fab fa-twitter text-white"></i></a>
                            <a class="btn btn-secondary btn-md-square rounded-circle me-3" href=""><i
                                    class="fab fa-instagram text-white "></i></a>
                            <a class="btn btn-secondary btn-md-square rounded-circle me-0" href=""><i
                                    class="fab fa-linkedin-in text-white"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-body"><a href="#" class="border-bottom text-white"><i
                                class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right
                        reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-body">
                    Designed By <a class="border-bottom text-white" href="https://htmlcodex.com">HTML Codex</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-secondary btn-lg-square rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
<?php
// logout.php
session_start();
session_destroy();
header("Location: index.php");
exit;
?>