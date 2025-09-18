$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        name = $('#name').val();
        email = $('#email').val();
        password = $('#password').val();
        confirm_password = $('#confirm_password').val();
        phone_number = $('#phone_number').val();
        country = $('#country').val();
        city = $('#city').val();
        role = $('input[name="role"]:checked').val();

        if (name == '' || email == '' || password == '' || confirm_password == '' || phone_number == '' || country == '' || city == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });

            return;
        } else if (password !== confirm_password) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Passwords do not match!',
            });

            return;
        } else if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });

            return;
        }

        $.ajax({
            url: '../actions/register_customer_action.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                password: password,
                confirm_password: confirm_password,
                phone_number: phone_number,
                country: country,
                city: city,
                role: role
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Registration error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                
                let errorMessage = 'An error occurred! Please try again later.';
                
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
                    title: 'Oops...',
                    text: errorMessage,
                });
            }
        });
    });
});