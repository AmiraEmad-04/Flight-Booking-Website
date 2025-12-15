<?php
require_once __DIR__ . '/../models/Flight.php';

class FlightController
{
    public function search()
    {
        if (!isset($_GET['from'], $_GET['to'])) {
            return [
                'status' => false,
                'message' => 'From and To are required'
            ];
        }

        $from = trim($_GET['from']);
        $to   = trim($_GET['to']);

        if ($from === '' || $to === '') {
            return [
                'status' => false,
                'message' => 'Invalid search values'
            ];
        }

        $flightModel = new Flight();
        $flights = $flightModel->searchFlights($from, $to);

        return [
            'status' => true,
            'count' => count($flights),
            'data' => $flights
        ];
    }

    public function getFlightInfo()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            return [
                'status' => false,
                'message' => 'Valid flight ID is required'
            ];
        }

        $id = (int) $_GET['id'];

        $flightModel = new Flight();
        $flight = $flightModel->getFlightById($id);

        if (!$flight) {
            return [
                'status' => false,
                'message' => 'Flight not found'
            ];
        }

        return [
            'status' => true,
            'data' => $flight
        ];
    }
}
