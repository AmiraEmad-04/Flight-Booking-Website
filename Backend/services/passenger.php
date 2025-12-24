<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/PassengerController.php';
require_once __DIR__ . '/../models/Passenger.php';

try {
    session_start();
    
    // Check authentication
    if (!isset($_SESSION['user_id']) || ($_SESSION['type'] ?? '') !== 'passenger') {
        echo json_encode(['status' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $userId = (int) $_SESSION['user_id'];  // ← Use user_id directly
    
    $controller = new PassengerController();
    
    // Get passenger to verify they exist
    $passengerModel = new Passenger();
    $passenger = $passengerModel->getByUserId($userId);
    
    if (!$passenger) {
        echo json_encode(['status' => false, 'message' => 'Passenger not found']);
        exit;
    }
    
    $passengerId = (int) $passenger['passenger_id'];  // ← Keep this for update-profile
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'home':
            // Pass USER_ID, not passenger_id
            $result = $controller->home($userId);  // ← FIXED: Use $userId
            echo json_encode($result, JSON_PRETTY_PRINT);
            break;
        
        case 'profile':
            // Profile uses passenger_id
            $result = $controller->profile($passengerId);
            echo json_encode($result, JSON_PRETTY_PRINT);
            break;
        
        case 'update-profile':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Handle JSON input
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                
                if (strpos($contentType, 'application/json') !== false) {
                    $jsonData = file_get_contents('php://input');
                    $data = json_decode($jsonData, true);
                } else {
                    $data = $_POST;
                }
                
                // Debug logging
                error_log("=== UPDATE PROFILE DEBUG ===");
                error_log("User ID: " . $userId);
                error_log("Passenger ID: " . $passengerId);
                error_log("Data: " . print_r($data, true));
                
                $result = $controller->updateProfile($passengerId, $data);
                
                error_log("Result: " . print_r($result, true));
                error_log("=== END DEBUG ===");
                
                echo json_encode($result, JSON_PRETTY_PRINT);
            } else {
                echo json_encode([
                    'status' => false, 
                    'message' => 'POST method required'
                ], JSON_PRETTY_PRINT);
            }
            break;
        
        default:
            echo json_encode([
                'status' => false, 
                'message' => 'Invalid action',
                'available_actions' => ['home', 'profile', 'update-profile']
            ], JSON_PRETTY_PRINT);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Server error',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>