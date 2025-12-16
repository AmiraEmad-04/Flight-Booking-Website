<?php 
require_once __DIR__ . '/../utils/Database.php'; // Include the Database class so we can connect to DB

class User{
    private $con;

    public function __construct(){
        $this->con = Database::getInstance()->getConnection();
        // When a User object is created, get the singleton DB connection
    }

    public function exists($email){
        $stmt = $this->con->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($data){
        $stmt = $this->con->prepare("
        INSERT INTO users (name , email , password , tel , type , account_balance) 
        VALUES (:name, :email, :password, :tel, :type, :account_balance)
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'tel' => $data['tel'],
            'type' => $data['type'],
            'account_balance' => 0.00
        ]);
    }

    public function login($email, $password){
        $stmt = $this->con->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password,$user['password'])){
            return $user;
        }
        return false;
    }
}
?>