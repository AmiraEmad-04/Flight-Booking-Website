// File upload functionality for company and passenger registration
$(document).ready(function() {
    // Initialize file upload functionality
    initializeFileUploads();
    
    // Company form submission
    $('#companyForm').submit(function(e) {
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
                    alert('Company registration successful!');
                    window.location.href = 'login.php';
                } else {
                    alert('Error: ' + (res.errors ? res.errors.join('\n') : res.message));
                }
            },
            error: function() {
                alert('Failed to save company details');
            }
        });
    });

    // Passenger form submission
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

// Initialize file upload functionality
function initializeFileUploads() {
    // Attach change event to all file inputs
    $('input[type="file"]').each(function() {
        $(this).on('change', function() {
            updateFileNameDisplay(this);
        });
    });
}

// Update file name display
function updateFileNameDisplay(fileInput) {
    const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file chosen';
    const displayId = fileInput.id.replace('Input', 'FileName');
    const displayElement = document.getElementById(displayId);
    
    if (displayElement) {
        displayElement.textContent = fileName;
    }
}
