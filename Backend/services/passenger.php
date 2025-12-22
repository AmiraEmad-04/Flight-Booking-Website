<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/PassengerController.php';

try {
    session_start();
    
    // // For testing: Set passenger_id in session
    // if (!isset($_SESSION['passenger_id'])) {
    //     $_SESSION['passenger_id'] = 1; // Malak (passenger_id = 1)
    // }
    if (!isset($_SESSION['passenger_id'])) {
        echo json_encode(['status' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $controller = new PassengerController();
    $passengerId = (int)$_SESSION['passenger_id'];
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'home':
            $result = $controller->home($passengerId);
            echo json_encode($result, JSON_PRETTY_PRINT);
            break;
        
        case 'profile':
            $result = $controller->profile($passengerId);
            echo json_encode($result, JSON_PRETTY_PRINT);
            break;
        
        case 'update-profile':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Log incoming data for debugging
                    error_log("=== UPDATE PROFILE DEBUG ===");
                    error_log("POST data: " . print_r($_POST, true));
                    error_log("Passenger ID: " . $passengerId);
                    
                    $result = $controller->updateProfile($passengerId, $_POST);
                    
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $controller->updateProfile($passengerId, $_POST);
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
                'available_actions' => ['home', 'profile', 'update-profile', 'search']
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