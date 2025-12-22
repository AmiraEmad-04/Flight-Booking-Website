<?php
require_once __DIR__ . '/../models/Bookings.php';
require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../models/User.php';

class BookingsController
{
    public function bookFlight()
    {
        $passengerId = (int) $_GET['passengerId'];
        $flightId    = (int) $_GET['flightId'];
        $paymentType = $_GET['paymentType'];

        if (!in_array($paymentType, ['cash', 'account'])) {
            return ['status' => false, 'message' => 'Invalid payment type'];
        }

        $bookingModel = new Bookings();
        $flightModel  = new Flight();
        $userModel    = new User();

        // 1️⃣ Passenger exists
        $passenger = $userModel->getPassengerById($passengerId);
        if (!$passenger) {
            return ['status' => false, 'message' => 'Passenger not found'];
        }

        // 2️⃣ Flight exists
        $flight = $flightModel->getFlightDetails($flightId);
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

        $conn = $bookingModel->getConnection();

        try {
            $conn->beginTransaction();

            // 4️⃣ ACCOUNT PAYMENT LOGIC
            if ($paymentType === 'account') {

                $passengerUser = $userModel->getUserById($passenger['user_id']);
                if ($passengerUser['account_balance'] < $flight['fees']) {
                    $conn->rollBack();
                    return [
                        'status' => false,
                        'message' => 'Insufficient account balance'
                    ];
                }

                // deduct passenger
                $userModel->updateBalance(
                    $passenger['user_id'],
                    -$flight['fees']
                );

                // add company
                $userModel->updateBalance(
                    $flight['company_id'],
                    +$flight['fees']
                );
            }

            // 5️⃣ Insert booking
            $bookingModel->insertBooking(
                $flightId,
                $passengerId,
                $paymentType
            );

            // 6️⃣ Increase registered passengers
            $flightModel->increaseRegisteredPassengers($flightId);

            $conn->commit();

            return [
                'status' => true,
                'message' => 'Flight booked successfully',
                'paymentType' => $paymentType
            ];

        } catch (Exception $e) {
            $conn->rollBack();
            return [
                'status' => false,
                'message' => 'Booking failed',
                'error' => $e->getMessage()
            ];
        }
    }
}
