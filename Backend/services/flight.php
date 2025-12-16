<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/FlightController.php';

$controller = new FlightController();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['from'])) {
    echo json_encode($controller->search());
    exit;
}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    echo json_encode($controller->getFlightInfo());
    exit;
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['userId']) && isset($_GET['flightId'])) {
    echo json_encode($controller->bookFlight());
    exit;
}

echo json_encode([
    'status' => false,
    'message' => 'Invalid request method'
]);
