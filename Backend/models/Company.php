<?php

// Include Database utility
require_once __DIR__ . '/../utils/Database.php';

class Company {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get company by user ID
    public function getCompanyByUserId($userId) {
        $sql = "SELECT u.*, c.id as company_id, c.bio, c.address, c.location, c.logo_img 
                FROM users u 
                JOIN companies c ON u.id = c.user_id 
                WHERE u.id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get company ID by user ID
    public function getCompanyId($userId) {
        $sql = "SELECT id FROM companies WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    // Update company profile
    public function updateProfile($userId, $userData, $companyData, $logoFilename = null) {
        try {
            $this->db->beginTransaction();

            // Update users table
            $userSql = "UPDATE users SET name = :name, email = :email, tel = :tel WHERE id = :user_id";
            $userStmt = $this->db->prepare($userSql);
            $userStmt->execute([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'tel' => $userData['tel'],
                'user_id' => $userId
            ]);

            // Update companies table
            $companySql = "UPDATE companies SET bio = :bio, address = :address, location = :location";
            
            if ($logoFilename) {
                $companySql .= ", logo_img = :logo_img";
            }
            
            $companySql .= " WHERE user_id = :user_id";
            
            $companyStmt = $this->db->prepare($companySql);
            
            $params = [
                'bio' => $companyData['bio'],
                'address' => $companyData['address'],
                'location' => $companyData['location'],
                'user_id' => $userId
            ];
            
            if ($logoFilename) {
                $params['logo_img'] = $logoFilename;
            }
            
            $companyStmt->execute($params);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Get old logo filename
    public function getLogoFilename($userId) {
        $sql = "SELECT logo_img FROM companies WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchColumn();
    }
}