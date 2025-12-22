<?php
// Debug file to test database connection and queries
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/Database.php';

try {
    // Test 1: Database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo json_encode(['test1' => 'Database connection OK']) . "\n\n";
    
    // Test 2: Check passengers table
    $stmt = $conn->prepare("SELECT * FROM passengers");
    $stmt->execute();
    $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test2' => 'Passengers in database',
        'count' => count($passengers),
        'data' => $passengers
    ], JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 3: Check users table (passengers only)
    $stmt = $conn->prepare("SELECT * FROM users WHERE type = 'passenger'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test3' => 'Passenger users in database',
        'count' => count($users),
        'data' => $users
    ], JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 4: Test the exact query from Passenger model
    $stmt = $conn->prepare("
        SELECT u.id AS user_id, u.name, u.email, u.tel, u.account_balance,
               p.id AS passenger_id, p.photo, p.passport_img
        FROM passengers p
        JOIN users u ON u.id = p.user_id
        WHERE p.id = :passenger_id
        LIMIT 1
    ");
    
    $passengerId = 1;
    $stmt->bindParam(':passenger_id', $passengerId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test4' => 'Query for passenger_id = 1',
        'result' => $result
    ], JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 5: Try passenger_id = 2
    $stmt = $conn->prepare("
        SELECT u.id AS user_id, u.name, u.email, u.tel, u.account_balance,
               p.id AS passenger_id, p.photo, p.passport_img
        FROM passengers p
        JOIN users u ON u.id = p.user_id
        WHERE p.id = :passenger_id
        LIMIT 1
    ");
    
    $passengerId = 2;
    $stmt->bindParam(':passenger_id', $passengerId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'test5' => 'Query for passenger_id = 2',
        'result' => $result
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>