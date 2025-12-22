<?php
require_once __DIR__ . '/../utils/Database.php';

class Passenger {
    private $conn;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($user_id, $data){
        $stmt = $this->conn->prepare("
            INSERT INTO passengers (user_id, photo, passport_img)
            VALUES (:user_id, :photo, :passport_img)
        ");

        return $stmt->execute([
            'user_id' => $user_id,
            'photo' => $data['photo'] ,
            'passport_img' => $data['passport_img'] 
        ]);
    }
}
?>
