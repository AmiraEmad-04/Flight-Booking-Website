<?php
header('Content-Type: application/json');

session_start();

require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../controllers/FlightController.php';

$controller = new FlightController();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['from']) && isset($_GET['to'])) {
    echo json_encode($controller->search());
    exit;
}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    echo json_encode($controller->getFlightInfo());
    exit;
}

$flightModel = new Flight();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'company') {
    header('Location: ../../Frontend/views/auth/login.php');
    exit();
}

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'add':
        handleAddFlight($flightModel);
        break;
    
    case 'cancel':
        handleCancelFlight($flightModel);
        break;
    
    default:
        header('Location: ../../Frontend/views/company/index.php');
        exit();
}

// Function to add flight
function handleAddFlight($flightModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        // Validate inputs
        if (!Validator::required($_POST['flight_name'])) {
            $errors[] = "Flight name is required";
        }

        if (!Validator::required($_POST['flight_code'])) {
            $errors[] = "Flight code is required";
        }

        if (!Validator::required($_POST['itinerary'])) {
            $errors[] = "Itinerary is required";
        }

        if (!Validator::positiveNumber($_POST['fees'])) {
            $errors[] = "Valid fees amount is required";
        }

        if (!Validator::positiveNumber($_POST['max_passengers'])) {
            $errors[] = "Valid max passengers number is required";
        }

        if (!Validator::dateTime($_POST['start_datetime'])) {
            $errors[] = "Valid start date and time is required";
        }

        if (!Validator::dateTime($_POST['end_datetime'])) {
            $errors[] = "Valid end date and time is required";
        }

        // Check if end time is after start time
        if (strtotime($_POST['end_datetime']) <= strtotime($_POST['start_datetime'])) {
            $errors[] = "End date must be after start date";
        }

        if (empty($errors)) {
            $result = $flightModel->addFlight($_SESSION['company_id'], $_POST);
            
            if ($result) {
                $_SESSION['success'] = "Flight added successfully!";
                header('Location: ../../Frontend/views/company/index.php');
                exit();
            } else {
                $errors[] = "Failed to add flight. Flight code may already exist.";
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: ../../Frontend/views/company/index.php');
        exit();
    }
}

// Function to cancel flight
function handleCancelFlight($flightModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'])) {
        $flightId = $_POST['flight_id'];
        
        // Verify this flight belongs to the logged-in company
        $flight = $flightModel->getFlightById($flightId);
        
        if (!$flight || $flight['company_id'] != $_SESSION['company_id']) {
            $_SESSION['errors'] = ["You don't have permission to cancel this flight"];
            header('Location: ../../Frontend/views/company/index.php');
            exit();
        }

        $result = $flightModel->cancelFlightWithRefund($flightId);
        
        if ($result) {
            $_SESSION['success'] = "Flight cancelled successfully and passengers refunded!";
        } else {
            $_SESSION['errors'] = ["Failed to cancel flight"];
        }
        
        header('Location: ../../Frontend/views/company/flight.php?id=' . $flightId);
        exit();
    }
}
?>