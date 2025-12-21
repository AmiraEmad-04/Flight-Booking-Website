<?php
require_once 'models/Passenger.php';
require_once 'models/Flight.php';

class PassengerController {
    private $passengerModel;
    private $flightModel;
    
    public function __construct($db) {
        $this->passengerModel = new Passenger($db);
        $this->flightModel = new Flight($db);
    }
    
    // Show passenger home page
    public function home() {
        session_start();
        if (!isset($_SESSION['passenger_id'])) {
            header('Location: login.php');
            exit();
        }
        
        $passenger = $this->passengerModel->getById($_SESSION['passenger_id']);
        $completedFlights = $this->passengerModel->getFlights($_SESSION['passenger_id'], 'completed');
        $currentFlights = $this->passengerModel->getFlights($_SESSION['passenger_id'], 'pending');
        
        include 'views/passenger/home.php';
    }
    
    // Show passenger profile
    public function profile() {
        session_start();
        if (!isset($_SESSION['passenger_id'])) {
            header('Location: login.php');
            exit();
        }
        
        $passenger = $this->passengerModel->getById($_SESSION['passenger_id']);
        include 'views/passenger/profile.php';
    }
    
    // Search flights
    public function searchFlights() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from = $_POST['from'];
            $to = $_POST['to'];
            $flights = $this->flightModel->searchFlights($from, $to);
            include 'views/passenger/search_results.php';
        } else {
            include 'views/passenger/search.php';
        }
    }
    
    // Show flight details
    public function flightInfo($flightId) {
        $flight = $this->flightModel->getById($flightId);
        include 'views/passenger/flight_info.php';
    }
    
    // Book a flight
    public function bookFlight() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $flightId = $_POST['flight_id'];
            $paymentType = $_POST['payment_type'];
            
            $result = $this->passengerModel->bookFlight(
                $_SESSION['passenger_id'], 
                $flightId, 
                $paymentType
            );
            
            if ($result) {
                header('Location: passenger_home.php?success=1');
            } else {
                header('Location: flight_info.php?id=' . $flightId . '&error=1');
            }
        }
    }
}