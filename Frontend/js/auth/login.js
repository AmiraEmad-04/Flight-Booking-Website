$(document).ready(function () {
    $('#loginForm').submit(function (e) {
        e.preventDefault();
        
        $.ajax({
            url: '/Flight-Booking-Website/Backend/services/auth.php?action=login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    // Base URL for all redirects
                    const baseUrl = '/Flight-Booking-Website/Frontend/';
                    
                    if (res.user.type === 'company') {
                        // Use absolute path with baseUrl
                        window.location.href = baseUrl + 'views/company/index.php';
                    } else if (res.user.type === 'passenger') {
                        // Use absolute path with baseUrl
                        window.location.href = baseUrl + 'pages/Passenger/passenger-home.php';
                    } else {
                        alert('Unknown user type');
                    }
                } else {
                    alert(res.message || 'Login failed');
                }
            },
            error: function (xhr, status, error) {
                console.error('Login error:', error);
                alert('Login error: ' + error);
            }
        });
    });
});