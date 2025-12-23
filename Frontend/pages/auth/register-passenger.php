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
    <title>Complete Profile - SKYBOOK</title>
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
            <h1>Complete Your Profile</h1>
            <p>Add your photo and passport for verification</p>
        </div>
    </section>

    <section class="auth-section">
        <div class="auth-container">
            <h2>Profile Documents</h2>
            <p>Upload your identification documents</p>

            <form id="passengerForm" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="type" value="passenger">

                <div class="form-group">
                    <label>Profile Photo</label>
                    <div class="file-upload-container">
                        <div class="file-upload-text">
                            <div class="file-upload-main-text">Choose File</div>
                            <div class="file-upload-subtext">A clear photo of your face</div>
                        </div>
                        <input type="file" name="photo" accept="image/*" required 
                               class="file-upload-input" id="photoInput">
                    </div>
                    <div class="file-name-display" id="photoFileName">No file chosen</div>
                </div>

                <div class="form-group">
                    <label>Passport Image</label>
                    <div class="file-upload-container">
                        <div class="file-upload-text">
                            <div class="file-upload-main-text">Choose File</div>
                            <div class="file-upload-subtext">Clear image of your passport</div>
                        </div>
                        <input type="file" name="passport_img" accept="image/*" required 
                               class="file-upload-input" id="passportInput">
                    </div>
                    <div class="file-name-display" id="passportFileName">No file chosen</div>
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