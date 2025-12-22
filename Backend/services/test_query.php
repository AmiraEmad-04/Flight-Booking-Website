<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../models/Passenger.php';

try {
    echo "=== TESTING PASSENGER MODEL ===\n\n";
    
    // Test 1: Create Passenger model instance
    echo "Test 1: Creating Passenger model...\n";
    $passengerModel = new Passenger();
    echo "✓ Passenger model created\n\n";
    
    // Test 2: Try getById with passenger_id = 1
    echo "Test 2: Calling getById(1)...\n";
    $result1 = $passengerModel->getById(1);
    echo "Result type: " . gettype($result1) . "\n";
    echo "Result: " . json_encode($result1, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 3: Try getById with passenger_id = 2
    echo "Test 3: Calling getById(2)...\n";
    $result2 = $passengerModel->getById(2);
    echo "Result type: " . gettype($result2) . "\n";
    echo "Result: " . json_encode($result2, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 4: Check what happens with if (!$result)
    echo "Test 4: Testing condition checks...\n";
    $testResult = $passengerModel->getById(1);
    echo "Result is false: " . ($testResult === false ? 'YES' : 'NO') . "\n";
    echo "Result is null: " . ($testResult === null ? 'YES' : 'NO') . "\n";
    echo "Result is empty: " . (empty($testResult) ? 'YES' : 'NO') . "\n";
    echo "Result is NOT (!): " . (!$testResult ? 'YES' : 'NO') . "\n\n";
    
    // Test 5: Test getFlights
    echo "Test 5: Calling getFlights(1, ['completed'])...\n";
    $flights1 = $passengerModel->getFlights(1, ['completed']);
    echo "Result: " . json_encode($flights1, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "Test 6: Calling getFlights(1, ['pending', 'registered'])...\n";
    $flights2 = $passengerModel->getFlights(1, ['pending', 'registered']);
    echo "Result: " . json_encode($flights2, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 7: Now test the controller
    echo "Test 7: Testing PassengerController...\n";
    require_once __DIR__ . '/../controllers/PassengerController.php';
    $controller = new PassengerController();
    $homeResult = $controller->home(1);
    echo "Controller home() result:\n";
    echo json_encode($homeResult, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "=== ALL TESTS COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
?>