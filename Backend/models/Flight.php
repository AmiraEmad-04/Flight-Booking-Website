<?php
require_once __DIR__ . '/../utils/Database.php';

class Flight
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

public function searchFlights($from, $to)
{
    $sql = "
        SELECT *
        FROM flights
        WHERE 
            is_cancelled = 0
            AND is_completed = 0
            AND registered_passengers < max_passengers
            AND itinerary LIKE :fromCity
            AND itinerary LIKE :toCity
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':fromCity' => '%' . $from . '%',
        ':toCity'   => '%' . $to . '%'
    ]);

    $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ FILTER BY ORDER (FROM → TO)
    $validFlights = [];

    foreach ($flights as $flight) {
        $itinerary = strtolower($flight['itinerary']);

        $fromPos = strpos($itinerary, strtolower($from));
        $toPos   = strpos($itinerary, strtolower($to));

        if ($fromPos !== false && $toPos !== false && $fromPos < $toPos) {
            $validFlights[] = $flight;
        }
    }

    return $validFlights;
}

public function getFlightById($id)
{
    $sql = "
        SELECT *
        FROM flights
        WHERE id = :Id
        LIMIT 1
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':Id' => $id
    ]);

    $flight = $stmt->fetch(PDO::FETCH_ASSOC);

    return $flight;
}

}
