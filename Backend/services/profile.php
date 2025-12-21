<?php

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../models/Company.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/FileUpload.php';

session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header('Location: ../../Frontend/views/auth/login.php');
    exit();
}

$companyModel = new Company();

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'update') {
    handleUpdateProfile($companyModel);
} else {
    header('Location: ../../Frontend/views/company/profile.php');
    exit();
}

// Function to handle profile update
function handleUpdateProfile($companyModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        // Validate inputs
        if (!Validator::required($_POST['name'])) {
            $errors[] = "Company name is required";
        }

        if (!Validator::email($_POST['email'])) {
            $errors[] = "Invalid email format";
        }

        if (!Validator::phone($_POST['tel'])) {
            $errors[] = "Invalid phone number (10-15 digits)";
        }

        // Handle logo upload if provided
        $logoFilename = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Upload path is relative to Backend/services/, so we need to go up to project root
            $fileUpload = new FileUpload(__DIR__ . '/../../Backend/uploads/logos/');
            $uploadResult = $fileUpload->upload($_FILES['logo']);
            
            if ($uploadResult['success']) {
                $logoFilename = $uploadResult['filename'];
                
                // Delete old logo if exists
                $oldLogo = $companyModel->getLogoFilename($_SESSION['user_id']);
                if ($oldLogo) {
                    $fileUpload->delete($oldLogo);
                }
            } else {
                $errors[] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            try {
                $userData = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'tel' => $_POST['tel']
                ];

                $companyData = [
                    'bio' => $_POST['bio'],
                    'address' => $_POST['address'],
                    'location' => $_POST['location']
                ];

                $companyModel->updateProfile($_SESSION['user_id'], $userData, $companyData, $logoFilename);
                
                $_SESSION['success'] = "Profile updated successfully!";
                header('Location: ../../Frontend/views/company/profile.php');
                exit();

            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errors[] = "Email already exists";
                } else {
                    $errors[] = "Error updating profile: " . $e->getMessage();
                }
            }
        }

        $_SESSION['errors'] = $errors;
        header('Location: ../../Frontend/views/company/profile.php?edit=true');
        exit();
    }
}