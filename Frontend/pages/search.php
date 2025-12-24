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

        <main class="main-wrapper">
            <section class="hero-section">
                <div class="overlay">
                    <h1 class="hero-title">Search Flights</h1>
                </div>
            </section>

            <section class="search-section">
                <div class="search-card">
                    <form class="search-form">
                        <div class="input-group">
                            <label for="from">From</label>
                            <input type="text" id="from" placeholder="Departure city">
                        </div>
                        <div class="input-group">
                            <label for="to">To</label>
                            <input type="text" id="to" placeholder="Destination city">
                        </div>
                        <button id="searchBtn" class="search-button">🔍 Search</button>
                    </form>
                </div>
            </section>

            <section class="results-section">
                <h2 class="results-title">Available Flights</h2>
                <div id="flights" class="flights-container">
                    <div class="empty-state">
                        <h3>No Flights Found</h3>
                        <p>Try different cities or check back later.</p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>