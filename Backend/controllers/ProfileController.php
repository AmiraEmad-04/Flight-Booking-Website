<?php

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/FileUpload.php';

class ProfileController {
    
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Update company profile
    public function updateProfile() {
        session_start();
        
        // Check if user is logged in and is a company
        if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'company') {
            header('Location: login.php');
            exit();
        }

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
                $fileUpload = new FileUpload('uploads/');
                $uploadResult = $fileUpload->upload($_FILES['logo']);
                
                if ($uploadResult['success']) {
                    $logoFilename = $uploadResult['filename'];
                    
                    // Delete old logo if exists
                    $stmt = $this->db->prepare("SELECT logo_img FROM companies WHERE user_id = :user_id");
                    $stmt->execute(['user_id' => $_SESSION['user_id']]);
                    $oldLogo = $stmt->fetchColumn();
                    
                    if ($oldLogo) {
                        $fileUpload->delete($oldLogo);
                    }
                } else {
                    $errors[] = $uploadResult['message'];
                }
            }

            if (empty($errors)) {
                try {
                    $this->db->beginTransaction();

                    // Update users table
                    $userSql = "UPDATE users SET name = :name, email = :email, tel = :tel WHERE id = :user_id";
                    $userStmt = $this->db->prepare($userSql);
                    $userStmt->execute([
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'tel' => $_POST['tel'],
                        'user_id' => $_SESSION['user_id']
                    ]);

                    // Update companies table
                    $companySql = "UPDATE companies SET bio = :bio, address = :address, location = :location";
                    
                    if ($logoFilename) {
                        $companySql .= ", logo_img = :logo_img";
                    }
                    
                    $companySql .= " WHERE user_id = :user_id";
                    
                    $companyStmt = $this->db->prepare($companySql);
                    
                    $params = [
                        'bio' => $_POST['bio'],
                        'address' => $_POST['address'],
                        'location' => $_POST['location'],
                        'user_id' => $_SESSION['user_id']
                    ];
                    
                    if ($logoFilename) {
                        $params['logo_img'] = $logoFilename;
                    }
                    
                    $companyStmt->execute($params);

                    $this->db->commit();
                    
                    $_SESSION['success'] = "Profile updated successfully!";
                    header('Location: ../profile.php');
                    exit();

                } catch (PDOException $e) {
                    $this->db->rollBack();
                    
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errors[] = "Email already exists";
                    } else {
                        $errors[] = "Error updating profile: " . $e->getMessage();
                    }
                }
            }

            $_SESSION['errors'] = $errors;
            header('Location: ../profile.php?edit=true');
            exit();
        }
    }
}

// Handle the request
if (isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller = new ProfileController();
    $controller->updateProfile();
}