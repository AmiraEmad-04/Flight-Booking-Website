<?php
// Get flightId from URL
$flightId = $_GET['id'] ?? null;

// Build base URL (same as search.php)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/Flight-Booking-Website/frontend";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details - SKYBOOK</title>

    <link rel="stylesheet" href="<?= $baseUrl ?>/css/flight-details.css">
    <script src="<?= $baseUrl ?>/js/jquery.min.js"></script>
</head>
<body>

<header class="navbar">
    <div class="logo">
        ✈ <span>SKYBOOK</span>
    </div>
    <nav>
        <a href="search.php">Home</a>
        <a href="login.php">Login</a>
        <a class="btn-register" href="register.php">Register</a>
    </nav>
</header>

<div class="container">
    <div id="flightDetails" class="card">
        <div class="loader">Loading flight details...</div>
    </div>

    <a href="search.php" class="back-link">← Back to Search</a>
</div>

<!-- Booking Modal (FIXED POSITION) -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeBookingPopup()">×</span>
        <iframe id="bookingFrame"></iframe>
    </div>
</div>

<!-- Pass flightId safely to JS -->
<script>
    const flightId = <?= json_encode($flightId) ?>;
</script>

<script src="<?= $baseUrl ?>/js/flight-details.js"></script>

</body>
</html>
