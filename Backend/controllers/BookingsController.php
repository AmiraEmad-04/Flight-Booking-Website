<?php
require_once __DIR__ . '/../models/Bookings.php';
require_once __DIR__ . '/../models/Flight.php';

class BookingsController
{
    public function bookFlight()
    {
        $passengerId = (int) $_GET['passengerId'];
        $flightId    = (int) $_GET['flightId'];

        $bookingModel = new Bookings();
        $flightModel  = new Flight();

        // 1️⃣ Check passenger exists
        $passenger = $bookingModel->getPassengerById($passengerId);
        if (!$passenger) {
            return ['status' => false, 'message' => 'Passenger not found'];
        }

        // 2️⃣ Check flight exists
        $flight = $flightModel->getFlightById($flightId);
        if (!$flight) {
            return ['status' => false, 'message' => 'Flight not found'];
        }

        if ($flight['is_cancelled'] || $flight['is_completed']) {
            return ['status' => false, 'message' => 'Flight not available'];
        }

        if ($flight['registered_passengers'] >= $flight['max_passengers']) {
            return ['status' => false, 'message' => 'Flight is full'];
        }

        // 3️⃣ Prevent duplicate booking
        if ($bookingModel->isPassengerBooked($passengerId, $flightId)) {
            return ['status' => false, 'message' => 'Already booked'];
        }

        // 4️⃣ Insert booking
        $bookingModel->insertBooking($flightId, $passengerId, 'cash');
        $flightModel->increaseRegisteredPassengers($flightId);

        return [
            'status' => true,
            'message' => 'Flight booked successfully'
        ];
    }
}
