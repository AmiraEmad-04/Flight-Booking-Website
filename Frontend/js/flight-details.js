// flight-details.js

// Check if flightId is valid
if (!flightId) {
    $('#flightDetails').html('<p class="error-msg">Invalid flight ID</p>');
} else {
    // Load flight details
    loadFlightDetails();
}

/**
 * Load flight details from API
 */
function loadFlightDetails() {
    $.ajax({
        url: '../../Backend/services/flight.php',
        method: 'GET',
        data: {
            id: flightId
        },
        dataType: 'json',
        success: function (res) {
            console.log('✅ details result:', res);
            if (!res.status) {
                $('#flightDetails').html('<p class="error-msg">' + res.message + '</p>');
                return;
            }

            displayFlightDetails(res.data);
        },
        error: function(xhr, status, error) {
            $('#flightDetails').html('<p class="error-msg">Error loading flight details: ' + error + '</p>');
        }
    });
}

/**
 * Format datetime to readable format
 */
function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
}

/**
 * Display flight details in the UI
 * @param {Object} f - Flight data object
 */
function displayFlightDetails(f) {
    const seatsLeft = f.max_passengers - f.registered_passengers;
    const departureFormatted = formatDateTime(f.start_datetime);
    const arrivalFormatted = formatDateTime(f.end_datetime);

    const html = `
        <div class="flight-header">
            <div class="flight-info-left">
                <span class="flight-code">✈ ${f.flight_code}</span>
                <h1 class="flight-name">${f.flight_name}</h1>
                <p class="flight-route">
                    ${f.itinerary}
                </p>
            </div>
            <div class="flight-price">
                <div class="price-amount">$${parseFloat(f.fees).toFixed(2)}</div>
                <div class="price-label">per passenger</div>
            </div>
        </div>

        <div class="flight-details-grid">
            <div class="detail-item">
                <div class="detail-label">Departure</div>
                <div class="detail-value">🕒 ${departureFormatted}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Arrival</div>
                <div class="detail-value">🛬 ${arrivalFormatted}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Available Seats</div>
                <div class="detail-value">💺 ${seatsLeft} of ${f.max_passengers}</div>
            </div>

        </div>

        <div class="action-buttons">
            <button class="btn-book" onclick="openBookingPopup(${flightId})">
                ✈ Book This Flight
            </button>

            <button class="btn-message" onclick="messageCompany()">
                💬 Message Company
            </button>
        </div>
    `;

    $('#flightDetails').html(html);
}


function openBookingPopup(flightId) {
    $('#bookingFrame').attr(
        'src',
        `book-flight.php?flightId=${flightId}`
    );
    $('#bookingModal').fadeIn();
}

function closeBookingPopup() {
    $('#bookingModal').fadeOut();
    $('#bookingFrame').attr('src', '');
}


/**
 * Open messaging interface
 */
function messageCompany() {
    // TODO: Implement messaging
    alert('Messaging feature coming soon!');
}

