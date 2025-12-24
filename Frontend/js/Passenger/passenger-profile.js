$(document).ready(function() {
    // Check if we're in edit mode
    const urlParams = new URLSearchParams(window.location.search);
    const isEditMode = urlParams.get('edit') === 'true';
    
    if (isEditMode) {
        loadProfileForEdit();
        setupFormHandlers();
    } else {
        loadProfileForView();
    }
});

// Load profile data for viewing
function loadProfileForView() {
    $.ajax({
        url: '/Flight-Booking-Website/Backend/services/passenger.php?action=profile',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                const data = response.passenger;
                $('#displayName').text(data.name || '');
                $('#displayEmail').text(data.email || '');
                $('#displayPhone').text(data.tel || 'Not provided');
            } else {
                showMessage('Error loading profile: ' + (response.message || 'Unknown'), 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            $('#displayName').text('Error loading data');
            $('#displayEmail').text('Error loading data');
            $('#displayPhone').text('Error loading data');
        }
    });
}

// Load profile data for editing
function loadProfileForEdit() {
    $.ajax({
        url: '/Flight-Booking-Website/Backend/services/passenger.php?action=profile',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                const data = response.passenger;

                // Populate form fields
                $('#fullName').val(data.name || '');
                $('#email').val(data.email || '');
                $('#phone').val(data.tel || '');
                
                // Show current photo if exists
                if (data.photo) {
                    $('#avatarPreview').attr('src', '../../uploads/' + data.photo);
                } else {
                    // Show default user icon as SVG
                    $('#avatarPreview').replaceWith(`
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="#1976d2">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    `);
                }
            } else {
                showMessage('Error loading profile: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            showMessage('Failed to load profile data', 'error');
        }
    });
}

// Setup form handlers
function setupFormHandlers() {
    // Preview photo on selection
    $('#photoInput').change(function() {
        const file = this.files[0];
        if (file) {
            // Check file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showMessage('Image size should not exceed 5MB', 'error');
                this.value = '';
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showMessage('Please upload a valid image (JPG, PNG, or GIF)', 'error');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                // Replace SVG with actual image if it exists
                if ($('#avatarPreview').length === 0) {
                    $('.avatar-circle').html('<img id="avatarPreview" src="" alt="Profile Photo">');
                }
                $('#avatarPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Handle form submission
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        
        // Validate form
        const fullName = $('#fullName').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        
        if (!fullName || !email) {
            showMessage('Please fill in all required fields', 'error');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showMessage('Please enter a valid email address', 'error');
            return;
        }
        
        const formData = new FormData(this);
        // Send 'name' field expected by backend model
        formData.delete('fullName');
        formData.append('name', fullName);
        // Ensure passport file is sent as 'passport_img' (input name updated in PHP)
        
        // Show loading state
        const submitBtn = $('.btn-save');
        const originalText = submitBtn.text();
        submitBtn.text('Saving...').prop('disabled', true);
        
        $.ajax({
            url: '/Flight-Booking-Website/Backend/services/passenger.php?action=update-profile',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    showMessage(response.message || 'Profile updated successfully!', 'success');
                    setTimeout(function() {
                        window.location.href = 'passenger-profile.php';
                    }, 1500);
                } else {
                    showMessage('Error: ' + (response.message || 'Unknown'), 'error');
                    submitBtn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.error('Response:', xhr.responseText);
                showMessage('Failed to update profile. Please try again.', 'error');
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
}

// Show message to user
function showMessage(message, type) {
    // Remove existing messages
    $('.message').remove();
    
    // Create new message
    const messageDiv = $('<div>')
        .addClass('message ' + type)
        .text(message);
    
    // Insert at top of profile content
    $('.profile-content').prepend(messageDiv);
    
    // Scroll to message
    $('html, body').animate({
        scrollTop: $('.message').offset().top - 100
    }, 300);
    
    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(function() {
            messageDiv.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
}