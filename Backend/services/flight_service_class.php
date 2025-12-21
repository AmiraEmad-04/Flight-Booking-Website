<?php
require_once(__DIR__ . "/../models/flight_data_class.php");

class flight_service_class {

    private $flight_model;

    function __construct()
    {
        $this->flight_model = new flight_data_class();
    }

function addFlight($data)
{
    $result = $this->flight_model->insertFlight(
        $data['company_id'],
        $data['flight_name'],
        $data['flight_code'],
        $data['itinerary'],
        $data['fees'],
        $data['max_passengers'],
        $data['start_datetime'],
        $data['end_datetime']
    );

    $error = $this->flight_model->getLastError(); // We'll add this method
    
    return [
        'success' => $result, 
        'message' => $result ? 'Flight added successfully' : 'Failed to add flight',
        'error' => $error,
        'data' => $data
    ];
}

    function getCompanyFlights($company_id)
    {
        $flights = $this->flight_model->selectFlightsByCompanyId($company_id);
        return ['success' => true, 'data' => $flights];
    }

    function getFlightDetails($flight_id)
    {
        $flight = $this->flight_model->selectFlightById($flight_id);
        return $flight ? ['success' => true, 'data' => $flight] : ['success' => false, 'message' => 'Flight not found'];
    }

    function getFlightPassengers($flight_id)
    {
        $passengers = $this->flight_model->selectFlightPassengers($flight_id);
        
        $pending = [];
        $registered = [];
        
        foreach ($passengers as $p) {
            if ($p['status'] == 'pending') $pending[] = $p;
            elseif ($p['status'] == 'registered') $registered[] = $p;
        }

        return ['success' => true, 'data' => ['pending' => $pending, 'registered' => $registered]];
    }

    function searchFlights($from, $to)
    {
        $flights = $this->flight_model->searchFlights($from, $to);
        return ['success' => true, 'data' => $flights];
    }

    function updateFlight($flight_id, $data)
    {
        $result = $this->flight_model->updateFlightById(
            $flight_id,
            $data['flight_name'],
            $data['itinerary'],
            $data['fees'],
            $data['max_passengers'],
            $data['start_datetime'],
            $data['end_datetime']
        );

        return ['success' => $result, 'message' => $result ? 'Flight updated successfully' : 'Failed to update flight'];
    }

    function cancelFlightWithRefund($flight_id)
    {
        $result = $this->flight_model->refundPassengersAndCancelFlight($flight_id);
        return ['success' => $result, 'message' => $result ? 'Flight cancelled and refunded' : 'Failed to cancel flight'];
    }

    function checkFlightAvailability($flight_id)
    {
        $available = $this->flight_model->isFlightAvailable($flight_id);
        $flight = $this->flight_model->selectFlightById($flight_id);

        return [
            'success' => true,
            'available' => $available,
            'registered' => $flight['registered_passengers'],
            'max' => $flight['max_passengers']
        ];
    }

    function completeFlight($flight_id)
    {
        $result = $this->flight_model->completeFlightById($flight_id);
        return ['success' => $result, 'message' => $result ? 'Flight completed' : 'Failed to complete flight'];
    }

    function incrementPassengerCount($flight_id)
    {
        return $this->flight_model->incrementRegisteredPassengers($flight_id);
    }

    function decrementPassengerCount($flight_id)
    {
        return $this->flight_model->decrementRegisteredPassengers($flight_id);
    }
}
?>