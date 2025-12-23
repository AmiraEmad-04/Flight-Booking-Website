$(document).ready(function() {
    loadAllFlights();

    $('#searchBtn').click(function() {
        searchFlights();
    });

    $('#from, #to').keypress(function(e) {
        if (e.which === 13) {
            searchFlights();
        }
    });
});

function loadAllFlights() {
    
    $.ajax({
        url: '../../Backend/services/flight.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(res) {
            console.log('✅ Flights loaded:', res);
            
            if (res.status && res.flights && res.flights.length > 0) {
                displayFlights(res.flights);
            } else if (res.status && res.data && res.data.length > 0) {
                displayFlights(res.data);
            } else {
                showEmptyState('No flights found');
            }
        }
    });
}

function searchFlights() {
    const from = $('#from').val().trim();
    const to = $('#to').val().trim();

    if (!from || !to) {
        alert('Please enter both cities');
        return;
    }

    $('#flights').html('<div class="loading">Searching...</div>');

    $.ajax({
        url: '../../Backend/services/flight.php',
        method: 'GET',
        data: { from: from, to: to },
        dataType: 'json',
        success: function(res) {
            console.log('✅ Search result:', res);
            
            if (res.status && res.data && res.data.length > 0) {
                displayFlights(res.data);
            } else if (res.status && res.flights && res.flights.length > 0) {
                displayFlights(res.flights);
            } else {
                showEmptyState('No flights found from ' + from + ' to ' + to);
            }
        },
        error: function(xhr) {
            console.error('❌ Search error:', xhr.responseText);
            showEmptyState('Search failed');
        }
    });
}

function displayFlights(flights) {
    console.log('Displaying', flights.length, 'flights');
    
    const container = $('#flights');
    container.empty();

    flights.forEach(function(f) {
        const seatsLeft = f.max_passengers - f.registered_passengers;
        const date = new Date(f.start_datetime).toLocaleString();

        const card = `
            <div class="flight-card" onclick="window.location.href='flight-details.php?id=${f.id}'">
                <div class="flight-icon">✈️</div>
                
                <div class="flight-info">
                    <div class="flight-header">
                        <span class="flight-code">${f.flight_code}</span>
                        <h3 class="flight-name">${f.flight_name}</h3>
                    </div>
                    <p class="flight-route">${f.itinerary}</p>
                </div>
                
                <div class="flight-details">
                    <div class="flight-meta">
                        <span class="flight-meta-item">📅 ${date}</span>
                        <span class="flight-meta-item">👥 ${seatsLeft} seats left</span>
                    </div>
                    <div class="flight-price">$ ${parseFloat(f.fees).toFixed(2)}</div>
                </div>
            </div>
        `;
        
        container.append(card);
    });
}

function showEmptyState(message) {
    $('#flights').html(`
        <div class="empty-state">
            <h3>No Flights Found</h3>
            <p>${message}</p>
        </div>
    `);
}