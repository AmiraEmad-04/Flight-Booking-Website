<?php
require_once __DIR__ . '/../utils/Database.php';

class Passenger
{
    private $conn;
    
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Get passenger basic info by passenger ID
    public function getById($passengerId)
    {
        $stmt = $this->conn->prepare("
            SELECT u.id AS user_id, u.name, u.email, u.tel, u.account_balance,
                   p.id AS passenger_id, p.photo, p.passport_img
            FROM passengers p
            JOIN users u ON u.id = p.user_id
            WHERE p.id = ?
            LIMIT 1
        ");
        $stmt->execute([$passengerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result : false;
    }
    
    // Get flights for a passenger by status
    public function getFlights($passengerId, $statuses)
    {
        if (empty($statuses)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        
        $stmt = $this->conn->prepare("
            SELECT f.id, f.flight_name, f.flight_code, f.itinerary, f.fees,
                   f.max_passengers, f.registered_passengers,
                   f.start_datetime, f.end_datetime, f.is_completed, f.is_cancelled,
                   b.id as booking_id, b.status, b.payment_type, b.booking_date,
                   c.id as company_id, cu.name as company_name, c.logo_img
            FROM bookings b
            JOIN flights f ON b.flight_id = f.id
            JOIN companies c ON f.company_id = c.id
            JOIN users cu ON c.user_id = cu.id
            WHERE b.passenger_id = ?
            AND b.status IN ($placeholders)
            ORDER BY f.start_datetime DESC
        ");
        
        $params = array_merge([$passengerId], $statuses);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
// Check if email exists (excluding current passenger)
public function emailExists($email, $excludePassengerId = null)
{
    if ($excludePassengerId) {
        $stmt = $this->conn->prepare("
            SELECT u.id 
            FROM users u
            LEFT JOIN passengers p ON p.user_id = u.id
            WHERE u.email = ? AND (p.id IS NULL OR p.id != ?)
            LIMIT 1
        ");
        $stmt->execute([$email, $excludePassengerId]);
    } else {
        $stmt = $this->conn->prepare("
            SELECT id FROM users WHERE email = ? LIMIT 1
        ");
        $stmt->execute([$email]);
    }
    
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// Updated updateProfile method with email support
public function updateProfile($passengerId, $data)
{
    try {
        $this->conn->beginTransaction();
        
        // Get user_id from passenger_id first
        $stmt = $this->conn->prepare("SELECT user_id FROM passengers WHERE id = ?");
        $stmt->execute([$passengerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            $this->conn->rollBack();
            return false;
        }
        
        $userId = $result['user_id'];
        
        // Update users table
        $userFields = [];
        $userValues = [];
        
        if (isset($data['name']) && trim($data['name']) !== '') {
            $userFields[] = 'name = ?';
            $userValues[] = trim($data['name']);
        }
        
        if (isset($data['email']) && trim($data['email']) !== '') {
            $userFields[] = 'email = ?';
            $userValues[] = trim($data['email']);
        }
        
        if (isset($data['tel']) && trim($data['tel']) !== '') {
            $userFields[] = 'tel = ?';
            $userValues[] = trim($data['tel']);
        }
        
        if (isset($data['password']) && trim($data['password']) !== '') {
            $userFields[] = 'password = ?';
            $userValues[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Update users table if there are fields
        if (!empty($userFields)) {
            $userValues[] = $userId;
            
            $sql = "UPDATE users SET " . implode(', ', $userFields) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($userValues);
        }
        
        // Update passengers table
        $passengerFields = [];
        $passengerValues = [];
        
        if (isset($data['photo']) && trim($data['photo']) !== '') {
            $passengerFields[] = 'photo = ?';
            $passengerValues[] = trim($data['photo']);
        }
        
        if (isset($data['passport_img']) && trim($data['passport_img']) !== '') {
            $passengerFields[] = 'passport_img = ?';
            $passengerValues[] = trim($data['passport_img']);
        }
        
        // Update passengers table if there are fields
        if (!empty($passengerFields)) {
            $passengerValues[] = $passengerId;
            
            $sql = "UPDATE passengers SET " . implode(', ', $passengerFields) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($passengerValues);
        }
        
        $this->conn->commit();
        return true;
        
    } catch (PDOException $e) {
        $this->conn->rollBack();
        error_log("Update profile error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Check for duplicate email error
        if ($e->getCode() == 23000) { // Integrity constraint violation
            error_log("Duplicate email detected");
        }
        
        return false;
    }
}
public function getByUserId($userId)
{
    $stmt = $this->conn->prepare("
        SELECT 
            p.id AS passenger_id,
            p.*,
            u.name,
            u.email,
            u.tel,
            u.account_balance
        FROM passengers p
        JOIN users u ON p.user_id = u.id
        WHERE p.user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
?>