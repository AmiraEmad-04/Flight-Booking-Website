<?php
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'company') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../../Backend/utils/Database.php';
require_once __DIR__ . '/../../../Backend/models/Flight.php';

$flightModel = new Flight();

// Get company data
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT u.*, c.id as company_id, c.bio, c.address, c.location, c.logo_img 
                      FROM users u 
                      JOIN companies c ON u.id = c.user_id 
                      WHERE u.id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Error: Company not found");
}

// Store company_id in session
if (!isset($_SESSION['company_id'])) {
    $_SESSION['company_id'] = $company['company_id'];
}

// Get all flights
$flights = $flightModel->getFlightsByCompany($_SESSION['company_id']);

// Calculate statistics
$activeFlights = 0;
$totalPassengers = 0;
foreach ($flights as $flight) {
    if (!$flight['is_cancelled'] && !$flight['is_completed']) {
        $activeFlights++;
    }
    $totalPassengers += $flight['registered_passengers'];
}

// Get messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
unset($_SESSION['success'], $_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company['name']); ?> - Dashboard</title>
    <link rel="stylesheet" href="../../css/company/company-styles.css">
    <?php include '../../navbar/navbar_css.php'; ?>
    
   
</head>
<body>

<!-- Navigation Bar -->
<?php include '../../navbar/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <div class="hero-subtitle">COMPANY DASHBOARD</div>
        <h1 class="hero-title">Manage Your Fleet</h1>
        <p class="hero-description">Monitor your flights, track passengers, and manage your airline operations all in one place.</p>
        <div class="hero-actions">
            <a href="add-flight.php" class="btn btn-light">+ Add Flight</a>
            <a href="profile.php" class="btn btn-outline">Company Profile</a>
        </div>
    </div>
</section>

<!-- Statistics Cards -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">✈️</div>
            <div class="stat-value"><?php echo $activeFlights; ?></div>
            <div class="stat-label">Active Flights</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?php echo $totalPassengers; ?></div>
            <div class="stat-label">Total Passengers</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">$<?php echo number_format($company['account_balance'], 0); ?></div>
            <div class="stat-label">Account Balance</div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container">
    
    <!-- Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Flights Section -->
    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Your Flights</h2>
            <a href="../../pages/Messages/messages.php" class="btn btn-secondary">Messages</a>
        </div>
        
        <?php if (empty($flights)): ?>
            <div class="no-data">
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">No flights added yet</p>
                <p>Add your first flight to get started!</p>
            </div>
        <?php else: ?>
            <?php foreach ($flights as $flight): ?>
                <div class="flight-card">
                    <div class="flight-card-header">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="flight-icon">✈️</div>
                            <div>
                                <div class="flight-title"><?php echo htmlspecialchars($flight['flight_code']); ?> - <?php echo htmlspecialchars($flight['flight_name']); ?></div>
                                <div class="flight-route">📍 <?php echo htmlspecialchars(substr($flight['itinerary'], 0, 50)); ?><?php echo strlen($flight['itinerary']) > 50 ? '...' : ''; ?></div>
                            </div>
                        </div>
                        <div>
                            <?php if ($flight['is_cancelled']): ?>
                                <span class="status-badge status-cancelled">Cancelled</span>
                            <?php elseif ($flight['is_completed']): ?>
                                <span class="status-badge status-completed">Completed</span>
                            <?php else: ?>
                                <span class="status-badge status-active">Active</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flight-details">
                        <div class="detail-item">
                            <span class="detail-label">📅 Departure:</span>
                            <span class="detail-value"><?php echo date('M d, Y H:i', strtotime($flight['start_datetime'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">🕐 Arrival:</span>
                            <span class="detail-value"><?php echo date('M d, Y H:i', strtotime($flight['end_datetime'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">👥 Passengers:</span>
                            <span class="detail-value"><?php echo $flight['registered_passengers']; ?>/<?php echo $flight['max_passengers']; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">💵 Price:</span>
                            <span class="detail-value">$<?php echo number_format($flight['fees'], 2); ?></span>
                        </div>
                        <div style="margin-left: auto;">
                            <a href="flight.php?id=<?php echo $flight['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
</div>

</body>
</html>