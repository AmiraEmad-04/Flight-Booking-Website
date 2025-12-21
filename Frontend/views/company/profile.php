<?php
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header('Location: test_login.php');
    exit();
}

require_once __DIR__ . '/../../../Backend/utils/Database.php';
require_once __DIR__ . '/../../../Backend/models/Flight.php';

$db = Database::getInstance()->getConnection();
$flightModel = new Flight();

// Get company data
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

// Get flights count
$flightCount = $flightModel->countFlightsByCompany($_SESSION['company_id']);

// Get all flights for display
$flights = $flightModel->getFlightsByCompany($_SESSION['company_id']);

// Get success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];

// Clear session messages
unset($_SESSION['success'], $_SESSION['errors']);

// Check if in edit mode
$editMode = isset($_GET['edit']) && $_GET['edit'] == 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
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
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            padding: 8px 15px;
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            background: #f0f0f0;
        }
        
        .container {
            max-width: 1000px;
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
        
        .profile-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        
        .logo-display {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-info h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .profile-stats {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .flights-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .flights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .flight-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .flight-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .flight-detail {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Company Profile</h2>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="message.php">Messages</a>
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
    
    <div class="profile-card">
        
        <?php if (!$editMode): ?>
            <!-- VIEW MODE -->
            <div class="profile-header">
                <?php if ($company['logo_img']): ?>
                    <img src="../../../Backend/uploads/logos/<?php echo htmlspecialchars($company['logo_img']); ?>" alt="Company Logo" class="logo-display">
                <?php else: ?>
                    <div class="logo-placeholder">
                        <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($company['name']); ?></h1>
                    <div class="profile-stats">
                        <?php echo $flightCount; ?> Flights | Balance: $<?php echo number_format($company['account_balance'], 2); ?>
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($company['email']); ?></div>
            </div>
            
            <div class="info-section">
                <div class="info-label">Phone</div>
                <div class="info-value"><?php echo htmlspecialchars($company['tel']); ?></div>
            </div>
            
            <div class="info-section">
                <div class="info-label">Bio</div>
                <div class="info-value"><?php echo $company['bio'] ? nl2br(htmlspecialchars($company['bio'])) : 'No bio provided'; ?></div>
            </div>
            
            <div class="info-section">
                <div class="info-label">Address</div>
                <div class="info-value"><?php echo $company['address'] ? htmlspecialchars($company['address']) : 'No address provided'; ?></div>
            </div>
            
            <div class="info-section">
                <div class="info-label">Location</div>
                <div class="info-value"><?php echo $company['location'] ? htmlspecialchars($company['location']) : 'No location provided'; ?></div>
            </div>
            
            <div class="button-group">
                <a href="profile.php?edit=true" class="btn btn-primary">Edit Profile</a>
            </div>
            
        <?php else: ?>
            <!-- EDIT MODE -->
            <h2 style="margin-bottom: 20px;">Edit Profile</h2>
            
            <form action="../../../Backend/services/profile.php?action=update" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Company Name *</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($company['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($company['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="tel" name="tel" required value="<?php echo htmlspecialchars($company['tel']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio"><?php echo htmlspecialchars($company['bio'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($company['address'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($company['location'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Company Logo (JPG, PNG - Max 5MB)</label>
                    <?php if ($company['logo_img']): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../../../Backend/uploads/logos/<?php echo htmlspecialchars($company['logo_img']); ?>" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="profile.php" class="btn btn-secondary">Cancel</a>
                </div>
                
            </form>
        <?php endif; ?>
        
        <?php if (!$editMode && !empty($flights)): ?>
            <!-- FLIGHTS LIST -->
            <div class="flights-section">
                <h2 class="section-title">My Flights</h2>
                <div class="flights-grid">
                    <?php foreach ($flights as $flight): ?>
                        <div class="flight-card">
                            <h3><?php echo htmlspecialchars($flight['flight_name']); ?></h3>
                            <div class="flight-detail">
                                <strong>Code:</strong> <?php echo htmlspecialchars($flight['flight_code']); ?>
                            </div>
                            <div class="flight-detail">
                                <strong>Date:</strong> <?php echo date('M d, Y', strtotime($flight['start_datetime'])); ?>
                            </div>
                            <div class="flight-detail">
                                <strong>Passengers:</strong> <?php echo $flight['registered_passengers']; ?>/<?php echo $flight['max_passengers']; ?>
                            </div>
                            <div class="flight-detail">
                                <strong>Fees:</strong> $<?php echo number_format($flight['fees'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
    
</div>

</body>
</html>