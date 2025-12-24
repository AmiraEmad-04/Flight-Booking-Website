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
    <?php include '../navbar/navbar_css.php'; ?>
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('../images/photo-1436491865332-7a61a109cc05.avif');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>
<?php include '../navbar/navbar.php'; ?>

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
