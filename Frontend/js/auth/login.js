$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/Flight-Booking-Website/Backend/services/auth.php?action=login',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    const type = res.user.type;
                    window.location.href = type === 'company' ? 'company-home.php' : 'passenger-home.php';
                } else {
                    alert(res.message || 'Login failed');
                }
            },
            error: function() {
                alert('Login error');
            }
        });
    });
});