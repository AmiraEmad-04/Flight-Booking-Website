$(document).ready(function() {
    $('#passengerForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: '/Flight-Booking-Website/Backend/services/auth.php?action=registerDetails',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    alert('Passenger registration successful!');
                    window.location.href = 'login.php';
                } else {
                    alert('Error: ' + (res.errors ? res.errors.join('\n') : res.message));
                }
            },
            error: function() {
                alert('Failed to save passenger details');
            }
        });
    });
});
