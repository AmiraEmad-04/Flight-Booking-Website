<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    // Redirect to test login for development
    header('Location: test_login.php');
    exit();
}

require_once __DIR__ . '/../../../Backend/utils/Database.php';
require_once __DIR__ . '/../../../Backend/models/Flight.php';
require_once __DIR__ . '/../../../Backend/models/Message.php';

$flightModel = new Flight();
$messageModel = new Message();

// Get company data
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT u.*, c.id as company_id, c.bio, c.address, c.location, c.logo_img 
                      FROM users u 
                      JOIN companies c ON u.id = c.user_id 
                      WHERE u.id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if company data was found
if (!$company) {
    die("Error: Company not found for user ID " . $_SESSION['user_id']);
}

// Store company_id in session for later use
if ($company && !isset($_SESSION['company_id'])) {
    $_SESSION['company_id'] = $company['company_id'];
}

// Get all flights for this company
$flights = $flightModel->getFlightsByCompany($_SESSION['company_id']);

// Get unread messages count
$unreadCount = $messageModel->getUnreadCount($_SESSION['user_id']);

// Get success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$oldInput = isset($_SESSION['old_input']) ? $_SESSION['old_input'] : [];

// Clear session messages
unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company['name']); ?> - Home</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .company-name { font-size: 24px; font-weight: bold; color: #333; }
        
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover { background: #f0f0f0; }
        
        .badge {
            background: #e74c3c;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .add-flight-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .flights-section h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .flights-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #34495e;
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background: #f8f9fa;
            cursor: pointer;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
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
        
        .no-flights {
            text-align: center;
            padding: 50px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-section">
        <?php if ($company['logo_img']): ?>
            <img src="../../../Backend/uploads/logos/<?php echo htmlspecialchars($company['logo_img']); ?>" alt="Logo" class="logo">
        <?php else: ?>
            <div class="logo" style="background: #3498db; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
            </div>
        <?php endif; ?>
        <span class="company-name"><?php echo htmlspecialchars($company['name']); ?></span>
    </div>
    
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="message.php">
            Messages
            <?php if ($unreadCount > 0): ?>
                <span class="badge"><?php echo $unreadCount; ?></span>
            <?php endif; ?>
        </a>
        <a href="test_login.php">Logout</a>
    </div>
</div>

<div class="container">
    
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
    
    <div class="add-flight-section">
        <h2>Add New Flight</h2>
        <form action="../../../Backend/services/flight.php?action=add" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Flight Name *</label>
                    <input type="text" name="flight_name" required 
                           value="<?php echo isset($oldInput['flight_name']) ? htmlspecialchars($oldInput['flight_name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Flight Code *</label>
                    <input type="text" name="flight_code" required 
                           value="<?php echo isset($oldInput['flight_code']) ? htmlspecialchars($oldInput['flight_code']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Itinerary (Cities) *</label>
                <textarea name="itinerary" rows="3" required><?php echo isset($oldInput['itinerary']) ? htmlspecialchars($oldInput['itinerary']) : ''; ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Fees ($) *</label>
                    <input type="number" step="0.01" name="fees" required 
                           value="<?php echo isset($oldInput['fees']) ? htmlspecialchars($oldInput['fees']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Max Passengers *</label>
                    <input type="number" name="max_passengers" required 
                           value="<?php echo isset($oldInput['max_passengers']) ? htmlspecialchars($oldInput['max_passengers']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Date & Time *</label>
                    <input type="datetime-local" name="start_datetime" required 
                           value="<?php echo isset($oldInput['start_datetime']) ? htmlspecialchars($oldInput['start_datetime']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>End Date & Time *</label>
                    <input type="datetime-local" name="end_datetime" required 
                           value="<?php echo isset($oldInput['end_datetime']) ? htmlspecialchars($oldInput['end_datetime']) : ''; ?>">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Flight</button>
        </form>
    </div>
    
    <div class="flights-section">
        <h2>My Flights (<?php echo count($flights); ?>)</h2>
        
        <?php if (empty($flights)): ?>
            <div class="no-flights">
                <p>No flights added yet. Add your first flight above!</p>
            </div>
        <?php else: ?>
            <div class="flights-table">
                <table>
                    <thead>
                        <tr>
                            <th>Flight Code</th>
                            <th>Flight Name</th>
                            <th>Start Date</th>
                            <th>Passengers</th>
                            <th>Fees</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flights as $flight): ?>
                            <tr onclick="window.location.href='flight.php?id=<?php echo $flight['id']; ?>'">
                                <td><?php echo htmlspecialchars($flight['flight_code']); ?></td>
                                <td><?php echo htmlspecialchars($flight['flight_name']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($flight['start_datetime'])); ?></td>
                                <td><?php echo $flight['registered_passengers']; ?> / <?php echo $flight['max_passengers']; ?></td>
                                <td>$<?php echo number_format($flight['fees'], 2); ?></td>
                                <td>
                                    <?php if ($flight['is_cancelled']): ?>
                                        <span class="status-badge status-cancelled">Cancelled</span>
                                    <?php elseif ($flight['is_completed']): ?>
                                        <span class="status-badge status-completed">Completed</span>
                                    <?php else: ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
</div>

</body>
</html>