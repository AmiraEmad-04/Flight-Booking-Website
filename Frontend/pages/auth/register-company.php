<?php
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header('Location: register.php');
    exit;
}
$user_id = $_GET['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details - SKYBOOK</title>
    <link rel="stylesheet" href="../../css/auth/global.css">
    <link rel="stylesheet" href="../../css/auth/forms.css">
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/auth/file-upload.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <span class="sky">SKY</span><span class="book">BOOK</span>
            </div>
            <nav>
                <ul>
                    <li><a href="../../Home/home.html">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="register-btn">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Complete Your Company Profile</h1>
            <p>Tell us more about your airline company</p>
        </div>
    </section>

    <section class="auth-section">
        <div class="auth-container">
            <h2>Company Details</h2>
            <p>This information will be displayed on your company profile.</p>

            <form id="companyForm" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="type" value="company">

                <div class="form-group">
                    <label>Company Logo</label>
                    <div class="file-upload-container">
                        <div class="file-upload-text">
                            <div class="file-upload-main-text">Choose File</div>
                            <div class="file-upload-subtext">PNG, JPG up to 2MB</div>
                        </div>
                        <input type="file" name="logo_img" accept="image/*" required 
                               class="file-upload-input" id="logoInput">
                    </div>
                    <div class="file-name-display" id="logoFileName">No file chosen</div>
                </div>

                <div class="form-group">
                    <label>Company Bio</label>
                    <textarea name="bio" placeholder="Tell us about your airline company, services, and what makes you unique..." required></textarea>
                </div>

                <hr class="hr-divider">

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="123 Aviation Street, Suite 100" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="City, Country">
                </div>

                <div class="form-actions">
                    <button type="button" class="secondary-btn" onclick="window.history.back()">Back</button>
                    <button type="submit" class="primary-btn">Create Account</button>
                </div>
            </form>
        </div>
    </section>
</body>
</html>