<?php
require_once __DIR__ . '/../utils/Database.php';

class Bookings
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getPassengerById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM passengers WHERE id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isPassengerBooked($passengerId, $flightId)
    {
        $stmt = $this->conn->prepare("
            SELECT id FROM bookings
            WHERE passenger_id = :pid AND flight_id = :fid
            LIMIT 1
        ");
        $stmt->execute([
            ':pid' => $passengerId,
            ':fid' => $flightId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertBooking($flightId, $passengerId, $paymentType)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO bookings (flight_id, passenger_id, payment_type)
            VALUES (:fid, :pid, :ptype)
        ");
        return $stmt->execute([
            ':fid'   => $flightId,
            ':pid'   => $passengerId,
            ':ptype' => $paymentType
        ]);
    }
}
