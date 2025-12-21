<?php

// Include Database utility
require_once __DIR__ . '/../utils/Database.php';

class Flight {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all flights for a specific company
    public function getFlightsByCompany($companyId) {
        $sql = "SELECT * FROM flights WHERE company_id = :company_id ORDER BY start_datetime DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['company_id' => $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single flight by ID
    public function getFlightById($flightId) {
        $sql = "SELECT * FROM flights WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $flightId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new flight
    public function createFlight($data) {
        $sql = "INSERT INTO flights 
                (company_id, flight_name, flight_code, itinerary, fees, max_passengers, start_datetime, end_datetime) 
                VALUES 
                (:company_id, :flight_name, :flight_code, :itinerary, :fees, :max_passengers, :start_datetime, :end_datetime)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'company_id' => $data['company_id'],
            'flight_name' => $data['flight_name'],
            'flight_code' => $data['flight_code'],
            'itinerary' => $data['itinerary'],
            'fees' => $data['fees'],
            'max_passengers' => $data['max_passengers'],
            'start_datetime' => $data['start_datetime'],
            'end_datetime' => $data['end_datetime']
        ]);
    }

    // Get pending passengers for a flight
    public function getPendingPassengers($flightId) {
        $sql = "SELECT u.name, u.email, u.tel, b.id as booking_id, b.payment_type
                FROM bookings b
                JOIN passengers p ON b.passenger_id = p.id
                JOIN users u ON p.user_id = u.id
                WHERE b.flight_id = :flight_id AND b.status = 'pending'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get registered passengers for a flight
    public function getRegisteredPassengers($flightId) {
        $sql = "SELECT u.name, u.email, u.tel, b.id as booking_id, b.payment_type
                FROM bookings b
                JOIN passengers p ON b.passenger_id = p.id
                JOIN users u ON p.user_id = u.id
                WHERE b.flight_id = :flight_id AND b.status = 'registered'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['flight_id' => $flightId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cancel flight and refund passengers
    public function cancelFlight($flightId) {
        try {
            $this->db->beginTransaction();

            // Get flight details
            $flight = $this->getFlightById($flightId);
            if (!$flight) {
                throw new Exception("Flight not found");
            }

            // Get all passengers who paid with account
            $sql = "SELECT p.user_id, b.payment_type
                    FROM bookings b
                    JOIN passengers p ON b.passenger_id = p.id
                    WHERE b.flight_id = :flight_id AND b.status IN ('pending', 'registered')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['flight_id' => $flightId]);
            $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Refund each passenger
            foreach ($passengers as $passenger) {
                if ($passenger['payment_type'] === 'account') {
                    $refundSql = "UPDATE users SET account_balance = account_balance + :fees WHERE id = :user_id";
                    $refundStmt = $this->db->prepare($refundSql);
                    $refundStmt->execute([
                        'fees' => $flight['fees'],
                        'user_id' => $passenger['user_id']
                    ]);
                }
            }

            // Update all bookings to cancelled
            $cancelBookingsSql = "UPDATE bookings SET status = 'cancelled' WHERE flight_id = :flight_id";
            $cancelStmt = $this->db->prepare($cancelBookingsSql);
            $cancelStmt->execute(['flight_id' => $flightId]);

            // Mark flight as cancelled
            $cancelFlightSql = "UPDATE flights SET is_cancelled = 1 WHERE id = :flight_id";
            $flightStmt = $this->db->prepare($cancelFlightSql);
            $flightStmt->execute(['flight_id' => $flightId]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Count flights for a company
    public function countFlightsByCompany($companyId) {
        $sql = "SELECT COUNT(*) as total FROM flights WHERE company_id = :company_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['company_id' => $companyId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}