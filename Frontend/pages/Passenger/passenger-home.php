<?php
session_start();

// production: rely on auth to set session values (no test shims)

// Check if user is logged in and is a passenger
$userType = $_SESSION['type'] ?? '';
if (!isset($_SESSION['user_id']) || $userType !== 'passenger') {
    header('Location: ../auth/login.php');
    exit();
}

// Get passenger data
$passengerId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard - SKYBOOK</title>
    <link rel="stylesheet" href="../../css/passenger/passenger-home.css">
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/jquery-ui.min.js"></script>
    <?php include '../../navbar/navbar_css.php'; ?>
</head>
<body>
    <!-- Header Section -->
     <?php include '../../navbar/navbar.php'; ?>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <div class="banner-content">
                <p class="banner-label">PASSENGER DASHBOARD</p>
                <h1>Welcome, <span id="userName">Loading...</span></h1>
                <p class="banner-subtitle">Search for flights, manage your bookings, and track your travel history all in one place.</p>
                <div class="banner-actions">
                    <a href="../search.php" class="btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        Search Flights
                    </a>
                    <a href="passenger-profile.php" class="btn-secondary">Passenger Profile</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Stats -->
    <section class="dashboard-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Current Flights</p>
                        <h3 class="stat-value" id="currentFlightsCount">2</h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Completed Flights</p>
                        <h3 class="stat-value" id="completedFlightsCount">1</h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Account Balance</p>
                        <h3 class="stat-value">$<span id="userAccount">1250.00</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <section class="content-section">
        <div class="container">
            <div class="tabs">
                <button class="tab-btn active" data-tab="my-flights">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                    </svg>
                    My Flights
                </button>
            </div>

            <!-- Current Flights Section -->
            <div class="flights-section" id="current-flights">
                <h2>Current Flights</h2>
                <div class="flights-list" id="currentFlightsList">
                    <!-- Flights will be loaded via JavaScript -->
                    <div class="loading">Loading your flights...</div>
                </div>
            </div>

            <!-- Completed Flights Section -->
            <div class="flights-section" id="completed-flights">
                <h2>Completed Flights</h2>
                <div class="flights-list" id="completedFlightsList">
                    <!-- Flights will be loaded via JavaScript -->
                    <div class="loading">Loading flight history...</div>
                </div>
            </div>
        </div>
    </section>

    <script src="../../js/passenger/passenger-home.js"></script>
</body>
</html>