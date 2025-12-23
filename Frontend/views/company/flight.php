<?php
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header('Location: ../auth/login.php');
    exit();
}

// Check if flight ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../../../Backend/utils/Database.php';
require_once __DIR__ . '/../../../Backend/models/Flight.php';

$flightModel = new Flight();
$flightId = $_GET['id'];

// Get flight details
$flight = $flightModel->getFlightById($flightId);

if (!$flight || $flight['company_id'] != $_SESSION['company_id']) {
    header('Location: index.php');
    exit();
}

// Get passengers
$pendingPassengers = $flightModel->getPendingPassengers($flightId);
$registeredPassengers = $flightModel->getRegisteredPassengers($flightId);

// Get messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details - <?php echo htmlspecialchars($flight['flight_code']); ?></title>
    <link rel="stylesheet" href="../../css/company/company-styles.css">
    <?php include '../../navbar/navbar_css.php'; ?>
    
</head>
<body>

<!-- Navigation Bar -->
<?php include '../../navbar/navbar.php'; ?>

<!-- Main Content -->
<div class="container" style="margin-top: 2rem;">
    
    <!-- Back Button -->
    <a href="index.php" class="back-button">← Back</a>
    
    <!-- Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <!-- Flight Details Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                    <span class="status-badge" style="font-size: 1rem; padding: 0.5rem 1rem;"><?php echo htmlspecialchars($flight['flight_code']); ?></span>
                    <?php if ($flight['is_cancelled']): ?>
                        <span class="status-badge status-cancelled">Cancelled</span>
                    <?php elseif ($flight['is_completed']): ?>
                        <span class="status-badge status-completed">Completed</span>
                    <?php else: ?>
                        <span class="status-badge status-active">Active</span>
                    <?php endif; ?>
                </div>
                <h1 class="detail-title"><?php echo htmlspecialchars($flight['flight_name']); ?></h1>
                <p style="color: oklch(0.4 0 0); margin-top: 0.5rem;">📍 <?php echo nl2br(htmlspecialchars($flight['itinerary'])); ?></p>
            </div>
            
            <?php if (!$flight['is_cancelled'] && !$flight['is_completed']): ?>
                <form action="../../../Backend/services/flight.php?action=cancel" method="POST" 
                      onsubmit="return confirm('⚠️ Are you sure you want to cancel this flight? All passengers will be refunded automatically.');"
                      style="margin: 0;">
                    <input type="hidden" name="flight_id" value="<?php echo $flight['id']; ?>">
                    <button type="submit" class="btn btn-danger">Cancel Flight</button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Flight Information Grid -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">📅 Departure</div>
                <div class="info-value"><?php echo date('M d, Y H:i', strtotime($flight['start_datetime'])); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">🕐 Arrival</div>
                <div class="info-value"><?php echo date('M d, Y H:i', strtotime($flight['end_datetime'])); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">👥 Passengers</div>
                <div class="info-value"><?php echo $flight['registered_passengers']; ?>/<?php echo $flight['max_passengers']; ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">💵 Price</div>
                <div class="info-value">$<?php echo number_format($flight['fees'], 2); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Passengers Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        
        <!-- Registered Passengers -->
        <div class="passengers-section">
            <h2 class="section-title">Registered Passengers (<?php echo count($registeredPassengers); ?>)</h2>
            
            <?php if (empty($registeredPassengers)): ?>
                <div class="no-data" style="padding: 2rem;">
                    <p>No registered passengers yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($registeredPassengers as $passenger): ?>
                    <div class="passenger-card">
                        <div class="passenger-info">
                            <div class="passenger-name"><?php echo htmlspecialchars($passenger['name']); ?></div>
                            <div class="passenger-email"><?php echo htmlspecialchars($passenger['email']); ?></div>
                        </div>
                        <span class="payment-badge <?php echo $passenger['payment_type'] === 'account' ? 'payment-account' : 'payment-cash'; ?>">
                            <?php echo ucfirst($passenger['payment_type']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pending Passengers -->
        <div class="passengers-section">
            <h2 class="section-title">Pending Passengers (<?php echo count($pendingPassengers); ?>)</h2>
            
            <?php if (empty($pendingPassengers)): ?>
                <div class="no-data" style="padding: 2rem;">
                    <p>No pending passengers</p>
                </div>
            <?php else: ?>
                <?php foreach ($pendingPassengers as $passenger): ?>
                    <div class="passenger-card">
                        <div class="passenger-info">
                            <div class="passenger-name"><?php echo htmlspecialchars($passenger['name']); ?></div>
                            <div class="passenger-email"><?php echo htmlspecialchars($passenger['email']); ?></div>
                        </div>
                        <span class="payment-badge <?php echo $passenger['payment_type'] === 'account' ? 'payment-account' : 'payment-cash'; ?>">
                            <?php echo ucfirst($passenger['payment_type']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Warning Section (only for active flights) -->
    <?php if (!$flight['is_cancelled'] && !$flight['is_completed']): ?>
        <div class="warning-section">
            <p class="warning-text">
                <strong>⚠️ Warning:</strong> Cancelling this flight will automatically refund all passengers who paid with their account balance.
            </p>
        </div>
    <?php endif; ?>
    
</div>

</body>
</html>