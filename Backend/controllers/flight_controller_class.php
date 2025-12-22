<?php
require_once(__DIR__ . "/../services/flight_service_class.php");

class flight_controller_class {

    private $flight_service;

    function __construct()
    {
        $this->flight_service = new flight_service_class();
    }

    function handleRequest()
    {  $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // If JSON data exists, use it; otherwise use $_POST
    if ($data) {
        $_POST = array_merge($_POST, $data);
    }
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($action == "add") {
            $result = $this->flight_service->addFlight($_POST);
            echo json_encode($result);

        } elseif ($action == "list") {
            $result = $this->flight_service->getCompanyFlights($_GET['company_id']);
            echo json_encode($result);

        } elseif ($action == "details") {
            $result = $this->flight_service->getFlightDetails($_GET['flight_id']);
            echo json_encode($result);

        } elseif ($action == "passengers") {
            // NEW: Get passengers list for a flight
            $result = $this->flight_service->getFlightPassengers($_GET['flight_id']);
            echo json_encode($result);

        } elseif ($action == "search") {
            $result = $this->flight_service->searchFlights($_GET['from'], $_GET['to']);
            echo json_encode($result);

        } elseif ($action == "update") {
            $result = $this->flight_service->updateFlight($_GET['flight_id'], $_POST);
            echo json_encode($result);

        } elseif ($action == "cancel") {
            // UPDATED: Now handles refunds
            $result = $this->flight_service->cancelFlightWithRefund($_GET['flight_id']);
            echo json_encode($result);

        } elseif ($action == "check_availability") {
            // NEW: Check if flight has available seats
            $result = $this->flight_service->checkFlightAvailability($_GET['flight_id']);
            echo json_encode($result);

        } elseif ($action == "complete") {
            // NEW: Mark flight as completed
            $result = $this->flight_service->completeFlight($_GET['flight_id']);
            echo json_encode($result);

        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }
    }
}

// Initialize and handle request
$controller = new flight_controller_class();
$controller->handleRequest();
?>