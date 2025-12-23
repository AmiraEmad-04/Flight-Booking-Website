<?php
// Get the base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/Flight-Booking-Website/Frontend";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Flights - SKYBOOK</title>
    
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/search.css">
    <script src="<?= $baseUrl ?>/js/jquery.min.js"></script>
</head>
<body>
    <!-- Your HTML content -->
    <header class="navbar">
        <div class="nav-brand">
            <div class="logo-icon">✈</div>
            <h1 class="brand-name">SKYBOOK</h1>
        </div>
        <nav class="nav-links">
            <a href="search.php" class="nav-link">Home</a>
            <a href="login.php" class="nav-link">Login</a>
            <a href="register.php" class="nav-btn">Register</a>
        </nav>
    </header>

    <div class="container">
        <div class="search-card">
            <h2 class="search-title">Search Flights</h2>
            
            <div class="search-form">
                <div class="input-group">
                    <label for="from">From</label>
                    <input type="text" id="from" placeholder="Departure city">
                </div>
                
                <div class="input-group">
                    <label for="to">To</label>
                    <input type="text" id="to" placeholder="Destination city">
                </div>
                
                <button id="searchBtn" class="search-button">🔍 Search</button>
            </div>
        </div>

        <h2 class="results-title">Available Flights</h2>
        <div id="flights" class="flights-container">
            <div class="empty-state">No Flights Found</div>
        </div>
    </div>

    <script src="<?= $baseUrl ?>/js/search.js"></script>
</body>
</html>