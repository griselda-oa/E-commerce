$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        let email = $('#email').val().trim();
        let password = $('#password').val();

        // Clear previous error messages
        $('.error-message').remove();

        // Validation
        let errors = [];

        // Email validation
        if (email === '') {
            errors.push('Email is required.');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push('Please enter a valid email address.');
        }

        // Password validation
        if (password === '') {
            errors.push('Password is required.');
        } else if (password.length < 6) {
            errors.push('Password must be at least 6 characters long.');
        }

        // Display validation errors
        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errors.join(' ')
            });
            return;
        }

        // Show loading state
        const submitBtn = $('#login-btn');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');
        
        submitBtn.prop('disabled', true);
        btnText.hide();
        btnLoading.show();

        // AJAX request with timeout
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            timeout: 15000, // 15 second timeout
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect || '../index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Login error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                
                let errorMessage = 'An error occurred during login. Please try again.';
                
                // Handle timeout specifically
                if (status === 'timeout') {
                    errorMessage = 'Login timed out. The server is taking too long to respond. Please try again.';
                }
                
                // Try to parse error response
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // If we can't parse JSON, use default message
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false);
                btnText.show();
                btnLoading.hide();
            }
        });
    });
});
