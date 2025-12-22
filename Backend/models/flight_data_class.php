<?php
require_once(__DIR__ . "/../utils/connection.php");

class flight_data_class extends connection {

    private $lastError = null;

    function insertFlight($company_id, $flight_name, $flight_code, $itinerary, $fees, $max_passengers, $start_datetime, $end_datetime)
    {
        parent::connect();

        // Escape strings to prevent SQL injection
        $flight_name = mysqli_real_escape_string(parent::return_connect(), $flight_name);
        $flight_code = mysqli_real_escape_string(parent::return_connect(), $flight_code);
        $itinerary = mysqli_real_escape_string(parent::return_connect(), $itinerary);

        $sql = "INSERT INTO flights
        (company_id, flight_name, flight_code, itinerary, fees, max_passengers, start_datetime, end_datetime, registered_passengers, is_cancelled, is_completed)
        VALUES
        ($company_id, '$flight_name', '$flight_code', '$itinerary', $fees, $max_passengers, '$start_datetime', '$end_datetime', 0, 0, 0)";

        $query = mysqli_query(parent::return_connect(), $sql);
        
        if (!$query) {
            $this->lastError = mysqli_error(parent::return_connect());
        }

        parent::disconnect();
        return $query ? true : false;
    }
    
    function getLastError() {
        return $this->lastError;
    }

    function selectFlightsByCompanyId($company_id)
    {
        parent::connect();

        $sql = "SELECT * FROM flights WHERE company_id=$company_id ORDER BY start_datetime DESC";
        $query = mysqli_query(parent::return_connect(), $sql);

        $rows = mysqli_num_rows($query);
        $arr = array();

        for ($i=0; $i<$rows; $i++) {
            $arr[$i] = mysqli_fetch_assoc($query);
        }

        parent::disconnect();
        return $arr;
    }

    function selectFlightById($flight_id)
    {
        parent::connect();

        $sql = "SELECT * FROM flights WHERE id=$flight_id";
        $query = mysqli_query(parent::return_connect(), $sql);

        $row = mysqli_fetch_assoc($query);

        parent::disconnect();
        return $row;
    }

    function searchFlights($from, $to)
    {
        parent::connect();

        $from = mysqli_real_escape_string(parent::return_connect(), $from);
        $to = mysqli_real_escape_string(parent::return_connect(), $to);

        $sql = "SELECT * FROM flights
        WHERE itinerary LIKE '%$from%'
        AND itinerary LIKE '%$to%'
        AND is_cancelled=0
        AND is_completed=0";

        $query = mysqli_query(parent::return_connect(), $sql);

        $rows = mysqli_num_rows($query);
        $arr = array();

        for ($i=0; $i<$rows; $i++) {
            $arr[$i] = mysqli_fetch_assoc($query);
        }

        parent::disconnect();
        return $arr;
    }

    function updateFlightById($flight_id, $flight_name, $itinerary, $fees, $max_passengers, $start_datetime, $end_datetime)
    {
        parent::connect();

        $flight_name = mysqli_real_escape_string(parent::return_connect(), $flight_name);
        $itinerary = mysqli_real_escape_string(parent::return_connect(), $itinerary);

        $sql = "UPDATE flights SET
        flight_name='$flight_name',
        itinerary='$itinerary',
        fees=$fees,
        max_passengers=$max_passengers,
        start_datetime='$start_datetime',
        end_datetime='$end_datetime'
        WHERE id=$flight_id";

        $query = mysqli_query(parent::return_connect(), $sql);

        parent::disconnect();
        return $query ? true : false;
    }

    function cancelFlightById($flight_id)
    {
        parent::connect();

        $sql = "UPDATE flights SET is_cancelled=1 WHERE id=$flight_id";
        $query = mysqli_query(parent::return_connect(), $sql);

        parent::disconnect();
        return $query ? true : false;
    }

    function selectFlightPassengers($flight_id)
    {
        parent::connect();
        
        $sql = "SELECT 
            b.id as booking_id,
            b.status,
            b.payment_type,
            u.name,
            u.email,
            u.tel,
            p.photo
        FROM bookings b
        JOIN passengers p ON b.passenger_id = p.id
        JOIN users u ON p.user_id = u.id
        WHERE b.flight_id = $flight_id
        ORDER BY b.booking_date";
        
        $query = mysqli_query(parent::return_connect(), $sql);
        $rows = mysqli_num_rows($query);
        $arr = array();
        
        for ($i=0; $i<$rows; $i++) {
            $arr[$i] = mysqli_fetch_assoc($query);
        }
        
        parent::disconnect();
        return $arr;
    }

    function refundPassengersAndCancelFlight($flight_id)
    {
        parent::connect();
        
        mysqli_begin_transaction(parent::return_connect());
        
        try {
            // Get flight fees
            $sql = "SELECT fees FROM flights WHERE id=$flight_id";
            $result = mysqli_query(parent::return_connect(), $sql);
            $flight = mysqli_fetch_assoc($result);
            $fees = $flight['fees'];
            
            // Get all passengers who paid from account
            $sql = "SELECT DISTINCT p.user_id 
                    FROM bookings b 
                    JOIN passengers p ON b.passenger_id = p.id
                    WHERE b.flight_id=$flight_id 
                    AND b.payment_type='account'
                    AND b.status IN ('registered', 'pending')";
            
            $result = mysqli_query(parent::return_connect(), $sql);
            
            // Refund each passenger
            while ($row = mysqli_fetch_assoc($result)) {
                $user_id = $row['user_id'];
                $refund_sql = "UPDATE users 
                              SET account_balance = account_balance + $fees 
                              WHERE id=$user_id";
                mysqli_query(parent::return_connect(), $refund_sql);
            }
            
            // Update bookings to cancelled
            $sql = "UPDATE bookings SET status='cancelled' WHERE flight_id=$flight_id";
            mysqli_query(parent::return_connect(), $sql);
            
            // Cancel the flight
            $sql = "UPDATE flights SET is_cancelled=1 WHERE id=$flight_id";
            mysqli_query(parent::return_connect(), $sql);
            
            mysqli_commit(parent::return_connect());
            parent::disconnect();
            return true;
            
        } catch (Exception $e) {
            mysqli_rollback(parent::return_connect());
            parent::disconnect();
            return false;
        }
    }

    function incrementRegisteredPassengers($flight_id)
    {
        parent::connect();
        $sql = "UPDATE flights 
                SET registered_passengers = registered_passengers + 1 
                WHERE id=$flight_id";
        $query = mysqli_query(parent::return_connect(), $sql);
        parent::disconnect();
        return $query ? true : false;
    }

    function completeFlightById($flight_id)
    {
        parent::connect();
        $sql = "UPDATE flights SET is_completed=1 WHERE id=$flight_id";
        $query = mysqli_query(parent::return_connect(), $sql);
        parent::disconnect();
        return $query ? true : false;
    }

    function decrementRegisteredPassengers($flight_id)
    {
        parent::connect();
        $sql = "UPDATE flights 
                SET registered_passengers = registered_passengers - 1 
                WHERE id=$flight_id 
                AND registered_passengers > 0";
        $query = mysqli_query(parent::return_connect(), $sql);
        parent::disconnect();
        return $query ? true : false;
    }

    function isFlightAvailable($flight_id)
    {
        parent::connect();
        $sql = "SELECT registered_passengers, max_passengers 
                FROM flights 
                WHERE id=$flight_id 
                AND is_cancelled=0 
                AND is_completed=0";
        
        $result = mysqli_query(parent::return_connect(), $sql);
        $flight = mysqli_fetch_assoc($result);
        
        parent::disconnect();
        
        if ($flight) {
            return $flight['registered_passengers'] < $flight['max_passengers'];
        }
        return false;
    }
}