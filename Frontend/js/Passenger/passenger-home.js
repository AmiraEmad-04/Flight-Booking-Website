$(document).ready(function() {
    loadPassengerHome();
    
    // Logout functionality
    $('#logoutBtn').click(function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '../auth/logout.php';
        }
    });
});

// Load passenger dashboard data (profile + flights)
function loadPassengerHome() {
    $.ajax({
        url: '/Flight-Booking-Website/Backend/services/passenger.php?action=home',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.debug('passenger home response:', response);
            if (response.status) {
                const passenger = response.passenger || {};
                const currentFlights = response.currentFlights || [];
                const completedFlights = response.completedFlights || [];

                // User info
                $('#userName').text(passenger.name || passenger.user_name || 'Passenger');
                if (passenger.account_balance !== undefined) {
                    $('#userAccount').text(parseFloat(passenger.account_balance).toFixed(2));
                }

                // Counts
                $('#currentFlightsCount').text(currentFlights.length);
                $('#completedFlightsCount').text(completedFlights.length);

                // Render lists
                if (currentFlights.length === 0) {
                    $('#currentFlightsList').html('<div class="loading">No current flights</div>');
                } else {
                    displayFlights(currentFlights, '#currentFlightsList');
                }

                if (completedFlights.length === 0) {
                    $('#completedFlightsList').html('<div class="loading">No completed flights</div>');
                } else {
                    displayFlights(completedFlights, '#completedFlightsList', true);
                }
            } else {
                console.error('Error loading dashboard:', response.message || 'Unknown');
                // show response in UI for debugging
                $('#currentFlightsList').html('<div class="loading">Error loading flights: '+(response.message||'Unknown')+'</div>');
                $('#currentFlightsList').html('<div class="loading">Error loading flights</div>');
                $('#completedFlightsList').html('<div class="loading">Error loading flights</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error, xhr.responseText);
            $('#currentFlightsList').html('<div class="loading">Error loading flights</div>');
            $('#completedFlightsList').html('<div class="loading">Error loading flights</div>');
        }
    });
}

// Load current flights
function loadCurrentFlights() {
    $.ajax({
        url: '../../Backend/passenger/get-flights.php',
        method: 'GET',
        data: { status: 'current' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const flights = response.data;
                $('#currentFlightsCount').text(flights.length);
                
                if (flights.length === 0) {
                    $('#currentFlightsList').html(
                        '<div class="loading">No current flights</div>'
                    );
                } else {
                    displayFlights(flights, '#currentFlightsList');
                }
            } else {
                $('#currentFlightsList').html(
                    '<div class="loading">Error loading flights</div>'
                );
            }
        },
        error: function() {
            $('#currentFlightsList').html(
                '<div class="loading">Error loading flights</div>'
            );
        }
    });
}

// Load completed flights
function loadCompletedFlights() {
    $.ajax({
        url: '../../Backend/passenger/get-flights.php',
        method: 'GET',
        data: { status: 'completed' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const flights = response.data;
                $('#completedFlightsCount').text(flights.length);
                
                if (flights.length === 0) {
                    $('#completedFlightsList').html(
                        '<div class="loading">No completed flights</div>'
                    );
                } else {
                    displayFlights(flights, '#completedFlightsList', true);
                }
            } else {
                $('#completedFlightsList').html(
                    '<div class="loading">Error loading flights</div>'
                );
            }
        },
        error: function() {
            $('#completedFlightsList').html(
                '<div class="loading">Error loading flights</div>'
            );
        }
    });
}

// Display flights in cards
function displayFlights(flights, container, isCompleted = false) {
    let html = '';
    
    flights.forEach(function(flight) {
        // Support backend field names from PassengerController
        const statusText = flight.status || flight.registrationStatus || 'pending';
        const statusClass = getStatusClass(statusText);

        const name = flight.flight_name || flight.flightName || 'Flight';

        // Try to extract route from itinerary or fall back to code
        let route = '';
        if (flight.itinerary) {
            route = flight.itinerary;
        } else if (flight.flight_code) {
            route = flight.flight_code;
        }

        const airline = flight.company_name || flight.airlineName || 'Unknown Airline';
        const fid = flight.id || flight.flightID || flight.flight_code || '';

        // start_datetime may contain both date and time
        let flightDate = 'N/A';
        if (flight.start_datetime) {
            const dt = flight.start_datetime.split(' ');
            flightDate = dt[0] + (dt[1] ? ' ' + dt[1].slice(0,5) : '');
        }

        html += `
            <div class="flight-card">
                <div class="flight-info-left">
                    <div class="flight-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                        </svg>
                    </div>
                    <div class="flight-details">
                        <h3>${name}</h3>
                        <p class="flight-route">${route}</p>
                        <p class="flight-airline">${airline}</p>
                    </div>
                </div>
                <div class="flight-info-right">
                    <span class="flight-id">${fid}</span>
                    <span class="flight-date">${flightDate}</span>
                    ${!isCompleted ? `<span class="status-badge ${statusClass}">${statusText}</span>` : ''}
                </div>
            </div>
        `;
    });
    
    $(container).html(html);
}

// Get status badge class
function getStatusClass(status) {
    const statusMap = {
        'registered': 'registered',
        'confirmed': 'registered',
        'pending': 'pending',
        'completed': 'completed',
        'cancelled': 'cancelled'
    };
    return statusMap[status?.toLowerCase()] || 'pending';
}

// Format date and time
function formatDateTime(date, time) {
    if (!date) return 'N/A';
    
    const dateObj = new Date(date);
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    
    let timeStr = '';
    if (time) {
        const timeParts = time.split(':');
        timeStr = ' ' + timeParts[0] + ':' + timeParts[1];
    }
    
    return `${year}-${month}-${day}${timeStr}`;
}