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

// Get pending and registered passengers
$pendingPassengers = $flightModel->getPendingPassengers($flightId);
$registeredPassengers = $flightModel->getRegisteredPassengers($flightId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details - <?php echo htmlspecialchars($flight['flight_code']); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background: #7f8c8d;
        }
        
        .flight-info {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .flight-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        
        .flight-title {
            font-size: 28px;
            color: #2c3e50;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
        }
        
        .passengers-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #ecf0f1;
            font-weight: bold;
            color: #2c3e50;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
        
        .btn-danger {
            padding: 12px 25px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .cancel-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ffc107;
            margin-top: 20px;
        }
        
        .cancel-warning {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <a href="index.php" class="back-button">← Back to Home</a>
    </div>
</div>

<div class="container">
    
    <div class="flight-info">
        <div class="flight-header">
            <h1 class="flight-title"><?php echo htmlspecialchars($flight['flight_name']); ?></h1>
            <?php if ($flight['is_cancelled']): ?>
                <span class="status-badge status-cancelled">Cancelled</span>
            <?php elseif ($flight['is_completed']): ?>
                <span class="status-badge status-completed">Completed</span>
            <?php else: ?>
                <span class="status-badge status-active">Active</span>
            <?php endif; ?>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Flight Code</div>
                <div class="info-value"><?php echo htmlspecialchars($flight['flight_code']); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Fees</div>
                <div class="info-value">$<?php echo number_format($flight['fees'], 2); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Start Date & Time</div>
                <div class="info-value"><?php echo date('M d, Y - H:i', strtotime($flight['start_datetime'])); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">End Date & Time</div>
                <div class="info-value"><?php echo date('M d, Y - H:i', strtotime($flight['end_datetime'])); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Max Passengers</div>
                <div class="info-value"><?php echo $flight['max_passengers']; ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Registered Passengers</div>
                <div class="info-value"><?php echo $flight['registered_passengers']; ?></div>
            </div>
        </div>
        
        <div class="info-item" style="grid-column: span 2;">
            <div class="info-label">Itinerary (Cities)</div>
            <div class="info-value"><?php echo nl2br(htmlspecialchars($flight['itinerary'])); ?></div>
        </div>
    </div>
    
    <div class="passengers-section">
        <h2 class="section-title">Pending Passengers (<?php echo count($pendingPassengers); ?>)</h2>
        
        <?php if (empty($pendingPassengers)): ?>
            <div class="no-data">No pending passengers</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Payment Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingPassengers as $passenger): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($passenger['name']); ?></td>
                            <td><?php echo htmlspecialchars($passenger['email']); ?></td>
                            <td><?php echo htmlspecialchars($passenger['tel']); ?></td>
                            <td><?php echo ucfirst($passenger['payment_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="passengers-section">
        <h2 class="section-title">Registered Passengers (<?php echo count($registeredPassengers); ?>)</h2>
        
        <?php if (empty($registeredPassengers)): ?>
            <div class="no-data">No registered passengers yet</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Payment Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registeredPassengers as $passenger): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($passenger['name']); ?></td>
                            <td><?php echo htmlspecialchars($passenger['email']); ?></td>
                            <td><?php echo htmlspecialchars($passenger['tel']); ?></td>
                            <td><?php echo ucfirst($passenger['payment_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <?php if (!$flight['is_cancelled'] && !$flight['is_completed']): ?>
        <div class="cancel-section">
            <p class="cancel-warning">
                <strong>⚠️ Warning:</strong> Cancelling this flight will refund all passengers who paid with their account balance.
            </p>
            <form action="../../../Backend/services/flight.php?action=cancel" method="POST" 
                  onsubmit="return confirm('Are you sure you want to cancel this flight? This action cannot be undone.');">
                <input type="hidden" name="flight_id" value="<?php echo $flight['id']; ?>">
                <button type="submit" class="btn-danger">Cancel Flight</button>
            </form>
        </div>
    <?php endif; ?>
    
</div>

</body>
</html>