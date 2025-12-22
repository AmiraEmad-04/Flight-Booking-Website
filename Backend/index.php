<?php
require_once __DIR__ . '/models/Passenger.php';
require_once __DIR__ . '/models/Flight.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>System Test</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 40px auto; }
        h2 { border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .ok { color: green; }
        .fail { color: red; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 6px; }
    </style>
</head>
<body>";

/* =====================
   Passenger by Email
   ===================== */
echo "<h2>Passenger Lookup</h2>";

$passenger = new Passenger();
$data = $passenger->getByEmail('jonn@test.com');

if ($data) {
    echo "<p class='ok'>Passenger found</p>";
    echo "<ul>";
    echo "<li>Name: {$data['name']}</li>";
    echo "<li>Email: {$data['email']}</li>";
    echo "<li>Balance: {$data['account_balance']}</li>";
    echo "</ul>";

    $passengerId = $data['passenger_id'];
} else {
    echo "<p class='fail'>Passenger not found</p>";
}

/* =====================
   Passenger Flights
   ===================== */
echo "<h2>Passenger Flights</h2>";

if (isset($passengerId)) {
    $flights = $passenger->getFlights($passengerId);

    if (!empty($flights)) {
        echo "<ul>";
        foreach ($flights as $f) {
            echo "<li>{$f['flight_name']} ({$f['flight_code']}) - {$f['status']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No flights booked</p>";
    }
} else {
    echo "<p class='fail'>Passenger ID missing</p>";
}

/* =====================
   Search Flights
   ===================== */
echo "<h2>Search Flights (Cairo → Dubai)</h2>";

$flightModel = new Flight();
$list = $flightModel->searchFlights('Cairo', 'Dubai');

if (!empty($list)) {
    echo "<ul>";
    foreach ($list as $f) {
        echo "<li>{$f['flight_name']} - {$f['fees']} USD</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No flights found</p>";
}

/* =====================
   Password Check
   ===================== */
echo "<h2>Password Check</h2>";

if (isset($data['password']) && password_verify('password', $data['password'])) {
    echo "<p class='ok'>Password verification OK</p>";
} else {
    echo "<p class='fail'>Password verification failed</p>";
}

echo "</body></html>";
