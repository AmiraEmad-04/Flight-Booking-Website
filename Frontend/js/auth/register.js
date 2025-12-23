$(document).ready(function() {
    $('.type-btn').click(function() {
        $('.type-btn').removeClass('active');
        $(this).addClass('active');
        $('#accountType').val($(this).data('type'));
    });

    $('#registerForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/flight_booking_project/Flight-Booking-Website/Backend/services/auth.php?action=registerUser',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    const type = res.type;
                    const uid = res.user_id;
                    window.location.href = type === 'company' 
                        ? 'register-company.php?user_id=' + uid 
                        : 'register-passenger.php?user_id=' + uid;
                } else {
                    alert('Errors:\n' + (res.errors || []).join('\n'));
                }
            },
            error: function() {
                alert('Connection error');
            }
        });
    });
});