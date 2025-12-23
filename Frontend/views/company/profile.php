<?php
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'company') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../../Backend/utils/Database.php';

$db = Database::getInstance()->getConnection();

// Get company data
$stmt = $db->prepare("SELECT u.*, c.id as company_id, c.bio, c.address, c.location, c.logo_img 
                      FROM users u 
                      JOIN companies c ON u.id = c.user_id 
                      WHERE u.id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Error: Company not found");
}

// Get messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
    <link rel="stylesheet" href="../../css/company/company-styles.css">
    <?php include '../../navbar/navbar_css.php'; ?>
   
</head>
<body>
 <?php include '../../navbar/navbar.php'; ?>


<!-- Main Content -->
<div class="container" style="max-width: 800px; margin-top: 3rem;">
    
    <!-- Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <!-- Profile Card -->
    <div class="content-card">
        <div class="card-header">
            <h1 class="card-title">Company Profile</h1>
            <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
        </div>
        
        <!-- Profile Header with Logo -->
        <div class="profile-header">
            <?php if ($company['logo_img']): ?>
                <img src="../../../Backend/uploads/logos/<?php echo htmlspecialchars($company['logo_img']); ?>" alt="Company Logo" class="profile-logo">
            <?php else: ?>
                <div class="profile-logo-placeholder">
                    🏢
                </div>
            <?php endif; ?>
            
            <div class="profile-info" style="flex: 1;">
                <h1 style="margin: 0;"><?php echo htmlspecialchars($company['name']); ?></h1>
            </div>
        </div>
        
        <!-- Company Information -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Company Name</div>
                <div class="info-value"><?php echo htmlspecialchars($company['name']); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($company['email']); ?></div>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value"><?php echo htmlspecialchars($company['tel']); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value"><?php echo htmlspecialchars($company['location'] ?: 'Not provided'); ?></div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="info-item">
                <div class="info-label">Address</div>
                <div class="info-value"><?php echo htmlspecialchars($company['address'] ?: 'Not provided'); ?></div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="info-item">
                <div class="info-label">Bio</div>
                <div class="info-value"><?php echo nl2br(htmlspecialchars($company['bio'] ?: 'Premium airline service connecting major cities across the nation. Founded in 2010, we pride ourselves on comfort and reliability.')); ?></div>
            </div>
        </div>
    </div>
    
</div>

</body>
</html>