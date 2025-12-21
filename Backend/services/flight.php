<?php

require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../utils/Validator.php';

session_start();

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
    header('Location: ../../Frontend/views/auth/login.php');
    exit();
}

$flightModel = new Flight();

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

// Function to handle adding a flight
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

                $flightModel->createFlight($flightData);
                $_SESSION['success'] = "Flight added successfully!";
                header('Location: ../../Frontend/views/company/index.php');
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
        header('Location: ../../Frontend/views/company/index.php');
        exit();
    }
}

// Function to handle cancelling a flight
function handleCancelFlight($flightModel) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'])) {
        try {
            $flightModel->cancelFlight($_POST['flight_id']);
            $_SESSION['success'] = "Flight cancelled and passengers refunded successfully!";
        } catch (Exception $e) {
            $_SESSION['errors'] = ["Error cancelling flight: " . $e->getMessage()];
        }
        
        header('Location: ../../Frontend/views/company/index.php');
        exit();
    }
}