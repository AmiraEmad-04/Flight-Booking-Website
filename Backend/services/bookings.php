<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/BookingsController.php';

try {
    $controller = new BookingsController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Handle both JSON and form data
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
        } else {
            // Check if data is in POST body or query string
            $data = !empty($_POST) ? $_POST : $_GET;
        }
        
        // Debug logging
        error_log("=== BOOKING REQUEST DEBUG ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("GET data: " . print_r($_GET, true));
        error_log("Parsed data: " . print_r($data, true));
        
        // Validate required fields
        if (!isset($data['passengerId']) || !isset($data['flightId']) || !isset($data['paymentType'])) {
            echo json_encode([
                'status' => false,
                'message' => 'Missing required fields: passengerId, flightId, paymentType',
                'received' => $data
            ]);
            exit;
        }
        
        echo json_encode($controller->bookFlight($data));
        exit;
    }

    echo json_encode([
        'status' => false,
        'message' => 'Invalid request method. POST required.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
?>