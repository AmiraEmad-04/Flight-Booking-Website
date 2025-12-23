let paymentType = 'account';

// Select payment method
$('.payment').click(function () {
    $('.payment').removeClass('selected');
    $(this).addClass('selected');
    paymentType = $(this).data('type');
});

// Load flight info
$.ajax({
    url: '../../Backend/services/flight.php',
    method: 'GET',
    data: { id: flightId },
    dataType: 'json',
    success: function (res) {
        if (res.status) {
            $('#flightInfo').html(
                `Booking <strong>${res.data.flight_name}</strong>
                 (${res.data.flight_code}) for
                 <strong>$${res.data.fees}</strong>`
            );
        } else {
            $('#flightInfo').css('color','red').text(res.message);
        }
    }
});

// Confirm booking
$('#confirm').click(function () {
    $(this).prop('disabled', true).text('Processing...');

    $.ajax({
        url: '../../Backend/services/bookings.php',
        method: 'POST',
        data: {
            passengerId: passengerId,
            flightId: flightId,
            paymentType: paymentType
        },
        dataType: 'json',
        success: function (res) {
            if (res.status) {
                $('#result').css('color','green').text(res.message);
                setTimeout(() => parent.closeBookingPopup(), 2000);
            } else {
                $('#result').css('color','red').text(res.message);
                $('#confirm').prop('disabled', false).text('Confirm Booking');
            }
        }
    });
});
