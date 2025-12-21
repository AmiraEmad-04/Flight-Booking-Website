<?php
session_start();
require_once 'models/Passenger.php';
require_once 'models/Flight.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Passenger System Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-top: 30px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px; margin: 5px 0; background: #f8f9fa; border-radius: 5px; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; 
               text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #0056b3; }
        hr { margin: 30px 0; border: none; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🧪 Testing Passenger System</h1>";
echo "<div class='info'>Testing Date: " . date('Y-m-d H:i:s') . "</div>";
echo "<hr>";

// Test 1: Get Passenger by Email
echo "<h2>Test 1: Get Passenger by Email</h2>";
$passenger = new Passenger();
$result = $passenger->getByEmail('ahmed@test.com');
if($result) {
    echo "<p class='success'>✅ Success! Found passenger:</p>";
    echo "<ul>";
    echo "<li><strong>Name:</strong> " . htmlspecialchars($result['name']) . "</li>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($result['email']) . "</li>";
    echo "<li><strong>Phone:</strong> " . htmlspecialchars($result['tel']) . "</li>";
    echo "<li><strong>Balance:</strong> $" . number_format($result['account_balance'], 2) . "</li>";
    echo "<li><strong>Passenger ID:</strong> " . $result['passenger_id'] . "</li>";
    echo "</ul>";
    $passengerId = $result['passenger_id'];
} else {
    echo "<p class='error'>❌ Failed to find passenger</p>";
}
echo "<hr>";

// Test 2: Get Passenger Bookings
echo "<h2>Test 2: Get Passenger Bookings</h2>";
if(isset($passengerId)) {
    $bookings = $passenger->getFlights($passengerId);
    if(count($bookings) > 0) {
        echo "<p class='success'>✅ Success! Found " . count($bookings) . " booking(s):</p>";
        echo "<ul>";
        foreach($bookings as $booking) {
            echo "<li>";
            echo "<strong>" . htmlspecialchars($booking['flight_name']) . "</strong> (" . 
                 htmlspecialchars($booking['flight_code']) . ")<br>";
            echo "Company: " . htmlspecialchars($booking['company_name']) . "<br>";
            echo "Status: <strong>" . htmlspecialchars($booking['status']) . "</strong> | ";
            echo "Payment: " . htmlspecialchars($booking['payment_type']) . "<br>";
            echo "Booking Date: " . htmlspecialchars($booking['booking_date']);
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='warning'>⚠️ No bookings found for this passenger</p>";
    }
} else {
    echo "<p class='error'>❌ Cannot test bookings without passenger ID</p>";
}
echo "<hr>";

// Test 3: Search Flights
echo "<h2>Test 3: Search Flights (Cairo to Dubai)</h2>";
$flightModel = new Flight();
$flights = $flightModel->searchFlights('Cairo', 'Dubai');
if(count($flights) > 0) {
    echo "<p class='success'>✅ Success! Found " . count($flights) . " flight(s):</p>";
    echo "<ul>";
    foreach($flights as $flight) {
        $available = $flight['max_passengers'] - $flight['registered_passengers'];
        echo "<li>";
        echo "<strong>" . htmlspecialchars($flight['flight_name']) . "</strong> (" . 
             htmlspecialchars($flight['flight_code']) . ")<br>";
        echo "Route: " . htmlspecialchars($flight['itinerary']) . "<br>";
        echo "Fee: $" . number_format($flight['fees'], 2) . "<br>";
        echo "Available Seats: " . $available . " / " . $flight['max_passengers'] . "<br>";
        echo "Departure: " . htmlspecialchars($flight['start_datetime']);
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>❌ No flights found</p>";
}
echo "<hr>";

// Test 4: Password Verification
echo "<h2>Test 4: Password Verification</h2>";
if(isset($result) && password_verify('password', $result['password'])) {
    echo "<p class='success'>✅ Password verification works correctly!</p>";
    echo "<div class='info'>Test credentials:<br>Email: ahmed@test.com<br>Password: password</div>";
} else {
    echo "<p class='error'>❌ Password verification failed</p>";
}
echo "<hr>";

echo "<h2>✅ All Tests Completed!</h2>";
echo "<a href='login.php' class='btn'>Go to Login Page →</a>";

echo "</div></body></html>";
?>
