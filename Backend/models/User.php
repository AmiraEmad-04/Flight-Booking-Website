<?php
require_once __DIR__ . '/../utils/Database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
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
