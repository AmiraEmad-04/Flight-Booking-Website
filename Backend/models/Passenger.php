<?php
require_once __DIR__ . '/../utils/Database.php';

class Passenger {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    // Register new passenger
    public function register($data) {
        try {
            $this->conn->beginTransaction();
            
            // إضافة المستخدم في جدول users
            $query = "INSERT INTO users (name, email, password, tel, type, account_balance) 
                      VALUES (?, ?, ?, ?, 'passenger', ?)";
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $data['name'], 
                $data['email'], 
                $hashedPassword, 
                $data['tel'],
                $data['account_balance'] ?? 0
            ]);
            
            $userId = $this->conn->lastInsertId();
            
            // إضافة بيانات الراكب
            $query2 = "INSERT INTO passengers (user_id, photo, passport_img) 
                       VALUES (?, NULL, NULL)";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->execute([$userId]);
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Get passenger by email
    public function getByEmail($email) {
        $query = "SELECT u.*, p.id as passenger_id, p.photo, p.passport_img 
                  FROM users u 
                  JOIN passengers p ON u.id = p.user_id 
                  WHERE u.email = ? AND u.type = 'passenger'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get passenger by ID
    public function getById($id) {
        $query = "SELECT u.*, p.id as passenger_id, p.photo, p.passport_img 
                  FROM users u 
                  JOIN passengers p ON u.id = p.user_id 
                  WHERE p.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update passenger profile
    public function updateProfile($id, $data) {
        try {
            $this->conn->beginTransaction();
            
            // تحديث بيانات users
            $query = "UPDATE users u 
                      JOIN passengers p ON u.id = p.user_id 
                      SET u.name=?, u.tel=? 
                      WHERE p.id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['name'],
                $data['tel'],
                $id
            ]);
            
            // تحديث الصور
            if(isset($data['photo']) || isset($data['passport_img'])) {
                $query2 = "UPDATE passengers SET photo=?, passport_img=? WHERE id=?";
                $stmt2 = $this->conn->prepare($query2);
                $stmt2->execute([
                    $data['photo'] ?? null,
                    $data['passport_img'] ?? null,
                    $id
                ]);
            }
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Get passenger bookings (flights)
    public function getFlights($passengerId, $status = null) {
        if ($status) {
            $query = "SELECT f.*, b.status, b.payment_type, b.booking_date,
                      u.name as company_name
                      FROM flights f 
                      JOIN bookings b ON f.id = b.flight_id 
                      JOIN companies c ON f.company_id = c.id
                      JOIN users u ON c.user_id = u.id
                      WHERE b.passenger_id = ? AND b.status = ?
                      ORDER BY f.start_datetime DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$passengerId, $status]);
        } else {
            $query = "SELECT f.*, b.status, b.payment_type, b.booking_date,
                      u.name as company_name
                      FROM flights f 
                      JOIN bookings b ON f.id = b.flight_id 
                      JOIN companies c ON f.company_id = c.id
                      JOIN users u ON c.user_id = u.id
                      WHERE b.passenger_id = ?
                      ORDER BY f.start_datetime DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$passengerId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Book a flight
    public function bookFlight($passengerId, $flightId, $paymentType) {
        try {
            $this->conn->beginTransaction();
            
            // إضافة الحجز
            $query = "INSERT INTO bookings (flight_id, passenger_id, payment_type, status) 
                      VALUES (?, ?, ?, 'pending')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$flightId, $passengerId, $paymentType]);
            
            // تحديث عدد المسجلين
            $updateQuery = "UPDATE flights 
                           SET registered_passengers = registered_passengers + 1 
                           WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute([$flightId]);
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>