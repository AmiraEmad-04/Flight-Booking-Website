<?php
session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
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

// Get error messages
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company Profile</title>
    <link rel="stylesheet" href="../../css/company/company-styles.css">
    <?php include '../../navbar/navbar_css.php'; ?>
    
</head>
<body>

<!-- Navigation Bar -->
<?php include '../../navbar/navbar.php'; ?>

<!-- Main Content -->
<div class="container" style="max-width: 800px; margin-top: 3rem;">
    
    <!-- Alert Messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Edit Profile Form -->
    <div class="content-card">
        <div class="card-header">
            <h1 class="card-title">Company Profile</h1>
        </div>
        
        <form action="../../../Backend/services/profile.php?action=update" method="POST" enctype="multipart/form-data">
            
            <!-- Logo Upload Section -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <?php if ($company['logo_img']): ?>
                    <img src="../../../Backend/uploads/logos/<?php echo htmlspecialchars($company['logo_img']); ?>" 
                         alt="Company Logo" 
                         class="profile-logo"
                         id="logoPreview">
                <?php else: ?>
                    <div class="profile-logo-placeholder" id="logoPlaceholder">
                        🏢
                    </div>
                    <img src="" alt="Logo Preview" class="profile-logo" id="logoPreview" style="display: none;">
                <?php endif; ?>
                
                <div style="margin-top: 1rem;">
                    <label class="form-label">Change Logo</label>
                    <input type="file" 
                           name="logo" 
                           class="form-control" 
                           accept="image/jpeg,image/png,image/jpg"
                           id="logoInput"
                           style="max-width: 400px; margin: 0 auto;">
                    <p style="font-size: 0.875rem; color: oklch(0.4 0 0); margin-top: 0.5rem;">No file chosen</p>
                </div>
            </div>
            
            <!-- Company Information -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($company['name']); ?>"
                        required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($company['email']); ?>"
                        required>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input 
                        type="tel" 
                        name="tel" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($company['tel']); ?>"
                        required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input 
                        type="text" 
                        name="location" 
                        class="form-control" 
                        placeholder="e.g. New York, USA"
                        value="<?php echo htmlspecialchars($company['location'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Address</label>
                <input 
                    type="text" 
                    name="address" 
                    class="form-control" 
                    placeholder="e.g. 123 Aviation Blvd, Terminal A"
                    value="<?php echo htmlspecialchars($company['address'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea 
                    name="bio" 
                    class="form-control" 
                    rows="4"
                    placeholder="Tell us about your airline company..."><?php echo htmlspecialchars($company['bio'] ?? ''); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="profile.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
</div>

<script>
// Logo preview functionality
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');
            
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>