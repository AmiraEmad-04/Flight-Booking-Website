<?php 
$flightId = $_GET['flightId'] ?? null; 
$passengerId = 1; 

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['name'] ?? 'Profile') : '';
$userType = $isLoggedIn ? ($_SESSION['type'] ?? '') : '';

// Get unread message count if logged in
$unreadCount = 0;
if ($isLoggedIn) {
    require_once __DIR__ . '/../../Backend/controllers/MessageController.php';
    $msgController = new MessageController();
    $result = $msgController->getUnreadCount();
    if ($result['status'] === 'success') {
        $unreadCount = $result['count'];
    }
}

// Decide home link based on user type
if ($userType === 'company') {
    $homeLink = "index.php";   // <-- path for company
} elseif ($userType === 'passenger') {
    $homeLink = "#";      // <-- path for passenger
} else {
    $homeLink = "../Home/home.php";               // <-- default path
}
?>

<header>
    <div class="container">
        <div class="header-content">
            <div class="logo">SKY<span>BOOK</span></div>
            <nav>
                <ul>
                    <!-- Dynamic Home link -->
                    <li><a href="<?php echo $homeLink; ?>">Home</a></li>
                    
                    <?php if ($isLoggedIn): ?>
                        <!-- Logged in menu -->
                        <?php if ($userType === 'passenger'): ?>
                            <li><a href="search_flights.php">Search Flights</a></li>
                            <li><a href="my_bookings.php">My Bookings</a></li>
                        <?php endif; ?>
                        <li><a href="profile.php"><?php echo htmlspecialchars($userName); ?></a></li>
                        <!-- Messages icon with badge -->
                        <li class="messages-nav-item">
                            <a href="../Messages/messages.php" class="messages-link">
                                <svg class="message-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="unread-badge"><?php echo $unreadCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="#" onclick="handleLogout(event)">Logout</a></li>
                    <?php else: ?>
                        <!-- Not logged in menu -->
                        <li><a href="../auth/login.php">Login</a></li>
                        <li><a href="../auth/register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>