<?php

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../utils/Validator.php';

class FlightController {
    
    private $flightModel;

    public function __construct() {
        $this->flightModel = new Flight();
    }

    // Handle add flight form submission
    public function addFlight() {
        session_start();
        
        // Check if user is logged in and is a company
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            header('Location: ../login.php');
            exit();
        }

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
                $errors[] = "Fees must be a positive number";
            }

            if (!Validator::positiveNumber($_POST['max_passengers'])) {
                $errors[] = "Max passengers must be a positive number";
            }

            if (!Validator::dateTime($_POST['start_datetime'])) {
                $errors[] = "Invalid start date/time";
            }

            if (!Validator::dateTime($_POST['end_datetime'])) {
                $errors[] = "Invalid end date/time";
            }

            // Check if end date is after start date
            if (strtotime($_POST['end_datetime']) <= strtotime($_POST['start_datetime'])) {
                $errors[] = "End date must be after start date";
            }

            if (empty($errors)) {
                try {
                    $flightData = [
                        'company_id' => $_SESSION['company_id'],
                        'flight_name' => $_POST['flight_name'],
                        'flight_code' => $_POST['flight_code'],
                        'itinerary' => $_POST['itinerary'],
                        'fees' => $_POST['fees'],
                        'max_passengers' => $_POST['max_passengers'],
                        'start_datetime' => $_POST['start_datetime'],
                        'end_datetime' => $_POST['end_datetime']
                    ];

                    $this->flightModel->createFlight($flightData);
                    $_SESSION['success'] = "Flight added successfully!";
                    header('Location: ../index.php');
                    exit();

                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errors[] = "Flight code already exists";
                    } else {
                        $errors[] = "Error adding flight: " . $e->getMessage();
                    }
                }
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ../index.php');
            exit();
        }
    }

    // Handle flight cancellation
    public function cancelFlight() {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            header('Location: ../login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'])) {
            try {
                $this->flightModel->cancelFlight($_POST['flight_id']);
                $_SESSION['success'] = "Flight cancelled and passengers refunded successfully!";
            } catch (Exception $e) {
                $_SESSION['errors'] = ["Error cancelling flight: " . $e->getMessage()];
            }
            
            header('Location: ../index.php');
            exit();
        }
    }

    // Get flight details
    public function getFlightDetails($flightId) {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            return null;
        }

        return $this->flightModel->getFlightById($flightId);
    }

    // Get pending passengers
    public function getPendingPassengers($flightId) {
        return $this->flightModel->getPendingPassengers($flightId);
    }

    // Get registered passengers
    public function getRegisteredPassengers($flightId) {
        return $this->flightModel->getRegisteredPassengers($flightId);
    }
}

// Handle the request
if (isset($_GET['action'])) {
    $controller = new FlightController();
    
    if ($_GET['action'] === 'add') {
        $controller->addFlight();
    } elseif ($_GET['action'] === 'cancel') {
        $controller->cancelFlight();
    }
}