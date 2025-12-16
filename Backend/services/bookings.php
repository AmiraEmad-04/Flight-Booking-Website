<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/BookingsController.php';

$controller = new BookingsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['passengerId'])
    && isset($_GET['flightId'])) {

    echo json_encode($controller->bookFlight());
    exit;
}

echo json_encode([
    'status' => false,
    'message' => 'Invalid request'
]);
