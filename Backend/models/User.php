<?php 
require_once __DIR__ . '/../utils/Database.php'; // Include the Database class so we can connect to DB

class User{
    private $conn;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        // When a User object is created, get the singleton DB connection
    }
    public function exists($email){
        $stmt = $this->conn->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->rowCount() > 0;
    }

    // Register only basic info and account balance
    public function register($data){
        $stmt = $this->conn->prepare("
            INSERT INTO users (name, email, password, tel, type, account_balance)
            VALUES (:name, :email, :password, :tel, :type, :account_balance)
        ");

        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'tel' => $data['tel'],
            'type' => $data['type'],
            'account_balance' => $data['account_balance']
        ]);

        return $this->conn->lastInsertId(); // return inserted user ID for step 2
    }

    public function login($email, $password){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password,$user['password'])){
            return $user;
        }
        return false;
    }
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM users WHERE id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBalance($userId, $amount)
    {
        $stmt = $this->conn->prepare("
            UPDATE users
            SET account_balance = account_balance + :amount
            WHERE id = :id
        ");
        return $stmt->execute([
            ':amount' => $amount,
            ':id'     => $userId
        ]);
    }
    public function getPassengerById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM passengers WHERE id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getPassengerByUserId($userId)
    {
        $sql = "
            SELECT *
            FROM passengers
            WHERE user_id = :userId
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':userId' => $userId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>