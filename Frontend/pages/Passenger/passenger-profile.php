<?php
session_start();

// Check if user is logged in and is a passenger
$userType = $_SESSION['type'] ?? '';
if (!isset($_SESSION['user_id']) || $userType !== 'passenger') {
    header('Location: ../auth/login.php');
    exit();
}

// Get passenger data
$passengerId = $_SESSION['user_id'];
$editMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? 'Edit Profile' : 'My Profile'; ?> - SKYBOOK</title>
    <style>
        /* Profile Page Specific Styles */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('../../images/photo-1436491865332-7a61a109cc05.avif');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Profile Section */
        .profile-section {
            padding: 120px 0;
            min-height: 100vh;
        }

        .profile-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 32px 40px;
            border-bottom: 1px solid #e0e0e0;
        }

        .profile-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #212121;
            font-family: inherit;
        }

        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
            padding: 10px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            font-family: inherit;
        }

        .btn-edit:hover {
            background: #bbdefb;
        }

        .profile-content {
            padding: 40px;
        }

        /* Avatar Section */
        .profile-avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 40px;
        }

        .avatar-circle {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-upload {
            text-align: center;
        }

        .photo-upload label {
            display: block;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .photo-upload input[type="file"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f9f9f9;
            cursor: pointer;
            font-size: 14px;
            font-family: inherit;
        }

        /* Profile Info - View Mode */
        .profile-info {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .info-group {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
        }

        .info-group:last-child {
            border-bottom: none;
        }

        .info-group label {
            display: block;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .info-group p {
            font-size: 16px;
            color: #212121;
            font-family: inherit;
        }

        /* Form - Edit Mode */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
            background: white;
            font-family: inherit;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group input[type="tel"]:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            cursor: pointer;
            font-size: 14px;
            font-family: inherit;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-save {
            flex: 1;
            background: #1976d2;
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            font-family: inherit;
        }

        .btn-save:hover {
            background: #1565c0;
        }

        .btn-cancel {
            flex: 1;
            background: #f5f5f5;
            color: #666;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: inherit;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        /* Success/Error Messages */
        .message {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #4caf50;
        }

        .message.error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #f44336;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                padding: 24px;
            }
            
            .profile-content {
                padding: 24px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-save,
            .btn-cancel {
                width: 100%;
            }
            
            .avatar-circle {
                width: 120px;
                height: 120px;
            }
        }
    </style>
    <?php include '../../navbar/navbar_css.php'; ?>
    <script src="../../js/jquery.min.js"></script>
</head>
<body class="profile-page-body">
    <!-- Header Section -->
     <?php include '../../navbar/navbar.php'; ?>

    <!-- Profile Section -->
    <section class="profile-section">
        <div class="container">
            <div class="profile-card">
                <div class="profile-header">
                    <h1>My Profile</h1>
                    <?php if (!$editMode): ?>
                        <a href="?edit=true" class="btn-edit">Edit Profile</a>
                    <?php endif; ?>
                </div>

                <div class="profile-content">
                    <?php if (!$editMode): ?>
                        <!-- View Mode -->
                        <div class="profile-avatar-section">
                            <div class="avatar-circle">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="#1976d2">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="profile-info">
                            <div class="info-group">
                                <label>Full Name</label>
                                <p id="displayName">Loading...</p>
                            </div>

                            <div class="info-group">
                                <label>Email</label>
                                <p id="displayEmail">Loading...</p>
                            </div>

                            <div class="info-group">
                                <label>Phone Number</label>
                                <p id="displayPhone">Loading...</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Edit Mode -->
                        <form id="profileForm" enctype="multipart/form-data">
                            <div class="profile-avatar-section">
                                <div class="avatar-circle">
                                    <img id="avatarPreview" src="../../uploads/default-user.png" alt="Profile Photo">
                                </div>
                                <div class="photo-upload">
                                    <label>Change Photo</label>
                                    <input type="file" id="photoInput" name="photo" accept="image/*">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" name="fullName" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>

                            <div class="form-group">
                                <label for="passport">Passport Image</label>
                                <input type="file" id="passport" name="passport_img" accept="image/*">
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save">Save Changes</button>
                                <a href="passenger-profile.php" class="btn-cancel">Cancel</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="../../js/passenger/passenger-profile.js"></script>
</body>
</html>