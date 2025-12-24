<?php 
$flightId = $_GET['flightId'] ?? null; 
$passengerId = 1; 

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? ($_SESSION['name'] ?? 'Profile') : '';
$userType   = $isLoggedIn ? ($_SESSION['type'] ?? '') : '';

// Get unread message count if logged in
$unreadCount = 0;
if ($isLoggedIn) {
    // Use absolute path from document root
    $controllerPath = $_SERVER['DOCUMENT_ROOT'] . '/Flight-Booking-Website/Backend/controllers/MessageController.php';
    
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $msgController = new MessageController();
        $result = $msgController->getUnreadCount();
        if ($result['status'] === 'success') {
            $unreadCount = $result['count'];
        }
    }
}

// Detect current file
$currentFile = basename($_SERVER['PHP_SELF']);

// Decide home link based on user type and current file
if ($userType === 'company') {
    $homeLink = ($currentFile === 'messages.php')
        ? "../../views/company/index.php"   // when inside messages.php
        : "../company/index.php";     // default
} elseif ($userType === 'passenger') {
    if ($currentFile === 'messages.php') {
        $homeLink = "../Passenger/passenger-home.php";   // from pages/Messages
    } elseif ($currentFile === 'search.php') {
        $homeLink = "Passenger/passenger-home.php";      // from pages/search.php
    } else {
        $homeLink = "passenger-home.php";      // default inside /pages
    }
} else {
    $homeLink = ($currentFile === 'messages.php')
        ? "../../Home/home.php"
        : "../Home/home.php";
}

// Decide messages link based on user type
if ($userType === 'company') {
    $messagesLink = "../../pages/Messages/messages.php";
} elseif ($userType === 'passenger') {
    $messagesLink = "../Messages/messages.php";
} else {
    $messagesLink = "../Messages/messages.php"; 
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
                            <li><a href="../search.php">Search Flights</a></li>
                        <?php endif; ?>
                        <li><a href="profile.php"><?php echo htmlspecialchars($userName); ?></a></li>
                        <!-- Messages icon with badge -->
                        <li class="messages-nav-item">
                            <a href="<?php echo $messagesLink; ?>" class="messages-link">
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