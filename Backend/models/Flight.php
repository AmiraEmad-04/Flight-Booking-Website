<?php

require_once __DIR__ . '/../utils/Database.php';

class Flight {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get flight by ID
    public function getFlightById($flightId) {
        $sql = "SELECT f.*, c.id as company_id, u.name as company_name 
                FROM flights f
                JOIN companies c ON f.company_id = c.id
                JOIN users u ON c.user_id = u.id
                WHERE f.id = :flight_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all flights by company
    public function getFlightsByCompany($companyId) {
        $sql = "SELECT * FROM flights 
                WHERE company_id = :company_id 
                ORDER BY start_datetime DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['company_id' => $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count flights by company
    public function countFlightsByCompany($companyId) {
        $sql = "SELECT COUNT(*) as count FROM flights WHERE company_id = :company_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['company_id' => $companyId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Get pending passengers for a flight
    public function getPendingPassengers($flightId) {
        $sql = "SELECT b.id as booking_id, b.payment_type, 
                       u.name, u.email, u.tel, p.photo
                FROM bookings b
                JOIN passengers p ON b.passenger_id = p.id
                JOIN users u ON p.user_id = u.id
                WHERE b.flight_id = :flight_id AND b.status = 'pending'
                ORDER BY b.booking_date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get registered passengers for a flight
    public function getRegisteredPassengers($flightId) {
        $sql = "SELECT b.id as booking_id, b.payment_type, 
                       u.name, u.email, u.tel, p.photo
                FROM bookings b
                JOIN passengers p ON b.passenger_id = p.id
                JOIN users u ON p.user_id = u.id
                WHERE b.flight_id = :flight_id AND b.status = 'registered'
                ORDER BY b.booking_date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new flight
    public function addFlight($companyId, $data) {
        $sql = "INSERT INTO flights 
                (company_id, flight_name, flight_code, itinerary, fees, 
                 max_passengers, start_datetime, end_datetime, registered_passengers)
                VALUES 
                (:company_id, :flight_name, :flight_code, :itinerary, :fees, 
                 :max_passengers, :start_datetime, :end_datetime, 0)";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            return $stmt->execute([
                'company_id' => $companyId,
                'flight_name' => $data['flight_name'],
                'flight_code' => $data['flight_code'],
                'itinerary' => $data['itinerary'],
                'fees' => $data['fees'],
                'max_passengers' => $data['max_passengers'],
                'start_datetime' => $data['start_datetime'],
                'end_datetime' => $data['end_datetime']
            ]);
        } catch (PDOException $e) {
            // Return false and store error
            return false;
        }
    }

    // Cancel flight and refund passengers
    public function cancelFlightWithRefund($flightId) {
        try {
            $this->db->beginTransaction();

            // Get flight fees
            $stmt = $this->db->prepare("SELECT fees FROM flights WHERE id = :flight_id");
            $stmt->execute(['flight_id' => $flightId]);
            $flight = $stmt->fetch(PDO::FETCH_ASSOC);
            $fees = $flight['fees'];

            // Get all passengers who paid from account
            $stmt = $this->db->prepare("
                SELECT DISTINCT p.user_id 
                FROM bookings b 
                JOIN passengers p ON b.passenger_id = p.id
                WHERE b.flight_id = :flight_id 
                AND b.payment_type = 'account'
                AND b.status IN ('registered', 'pending')
            ");
            $stmt->execute(['flight_id' => $flightId]);
            $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Refund each passenger
            foreach ($passengers as $passenger) {
                $refundStmt = $this->db->prepare("
                    UPDATE users 
                    SET account_balance = account_balance + :fees 
                    WHERE id = :user_id
                ");
                $refundStmt->execute([
                    'fees' => $fees,
                    'user_id' => $passenger['user_id']
                ]);
            }

            // Update bookings to cancelled
            $stmt = $this->db->prepare("UPDATE bookings SET status = 'cancelled' WHERE flight_id = :flight_id");
            $stmt->execute(['flight_id' => $flightId]);

            // Cancel the flight
            $stmt = $this->db->prepare("UPDATE flights SET is_cancelled = 1 WHERE id = :flight_id");
            $stmt->execute(['flight_id' => $flightId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
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

        $stmt = $this->db->prepare($sql);
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

    public function getFlightDetails($id)
    {
        $sql = "
            SELECT *
            FROM flights
            WHERE id = :Id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':Id' => $id
        ]);

        $flight = $stmt->fetch(PDO::FETCH_ASSOC);

        return $flight;
    }

    public function increaseRegisteredPassengers($flightId)
    {
        $sql = "
            UPDATE flights
            SET registered_passengers = registered_passengers + 1
            WHERE id = :flightId
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':flightId' => $flightId
        ]);
    }
}
