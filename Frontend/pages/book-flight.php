<?php
// Get parameters from URL at the TOP
$flightId = $_GET['flightId'] ?? null;
$passengerId = 1; // TEMP until login system (Malak for testing)

// Redirect back if no flightId
if (!$flightId) {
    header('Location: search.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Flight</title>

    <link rel="stylesheet" href="../css/book-flight.css">
    <script src="../js/jquery.min.js"></script>
</head>
<body>

<div class="booking-card">
    <h2>Book Flight</h2>

    <p id="flightInfo">Loading flight...</p>

    <h4>Payment Method</h4>

    <div class="payment-options">
        <div class="payment selected" data-type="account">
            💰
            <strong>From Account</strong>
            <span>Pay with balance</span>
        </div>

        <div class="payment" data-type="cash">
            💵
            <strong>Cash</strong>
            <span>Pay at company</span>
        </div>
    </div>

    <div class="buttons">
        <button id="confirm">Confirm Booking</button>
        <button id="cancel" onclick="history.back()">Cancel</button>
    </div>

    <p id="result"></p>
</div>

<script>
const flightId = <?= (int)$flightId ?>;
const passengerId = <?= (int)$passengerId ?>;
</script>
<script src="../js/book-flight.js"></script>


</body>
</html>