<?php
require_once __DIR__ . '/../models/Bookings.php';
require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../models/User.php';

class BookingsController
{
    public function bookFlight($data = null)
    {
        // If no data passed, try to get from POST/GET
        if ($data === null) {
            $data = !empty($_POST) ? $_POST : $_GET;
        }
        
        // Validate and sanitize inputs
        if (!isset($data['passengerId']) || !isset($data['flightId']) || !isset($data['paymentType'])) {
            return [
                'status' => false,
                'message' => 'Missing required parameters'
            ];
        }
        
        $passengerId = (int) $data['passengerId'];
        $flightId    = (int) $data['flightId'];
        $paymentType = $data['paymentType'];

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

                // add to company
                $companyUser = $userModel->getCompanyUserId($flight['company_id']);
                if ($companyUser) {
                    $userModel->updateBalance(
                        $companyUser['user_id'],
                        +$flight['fees']
                    );
                }
            }

            // 5️⃣ Insert booking
            $bookingModel->insertBooking(
                $flightId,
                $passengerId,
                $paymentType
            );

            // 6️⃣ Increase registered passengers (only for account payment)
            if ($paymentType === 'account') {
                $flightModel->increaseRegisteredPassengers($flightId);
            }

            $conn->commit();

            $statusMessage = $paymentType === 'account' 
                ? 'Flight booked successfully! Payment deducted from your account.'
                : 'Booking submitted! Please complete cash payment at the company.';

            return [
                'status' => true,
                'message' => $statusMessage,
                'paymentType' => $paymentType,
                'bookingStatus' => $paymentType === 'account' ? 'registered' : 'pending'
            ];

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Booking error: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Booking failed',
                'error' => $e->getMessage()
            ];
        }
    }
}
?>