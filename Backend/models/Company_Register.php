<?php
require_once __DIR__ . '/../utils/Database.php';

class Company {
    private $conn;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($user_id, $data){
        $stmt = $this->conn->prepare("
            INSERT INTO companies (user_id, bio, address, location, logo_img)
            VALUES (:user_id, :bio, :address, :location, :logo_img)
        ");

        return $stmt->execute([
            'user_id' => $user_id,
            'bio' => $data['bio'] ,
            'address' => $data['address'] ,
            'location' => $data['location'] ?? null,
            'logo_img' => $data['logo_img'] 
        ]);
    }
}
?>
