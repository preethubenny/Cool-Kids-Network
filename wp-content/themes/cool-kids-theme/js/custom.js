jQuery(document).ready(function($) {
    // Signup Form Submission
    $('#cool-kids-signup').submit(function(e) {
        e.preventDefault();

        let formData = {
            action: 'cool_kids_user_signup', // WordPress AJAX action
            username: $('input[name="username"]').val(),
            email: $('input[name="email"]').val(),
            password: $('input[name="password"]').val()
        };

        $.ajax({
            type: 'POST',
            url: ajaxurl, // WordPress AJAX URL
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#signup-message').html('<p class="success">' + response.data.message + '</p>');
                    setTimeout(function() {
                        window.location.href = '/login'; // Redirect to login page
                    }, 2000);
                } else {
                    $('#signup-message').html('<p class="error">' + response.data.message + '</p>');
                }
            }
        });
    });

    // Login Form Submission
    $('#cool-kids-login').submit(function(e) {
        e.preventDefault();

        let formData = {
            action: 'cool_kids_user_login',
            username: $('input[name="username"]').val(),
            password: $('input[name="password"]').val()
        };

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#login-message').html('<p class="success">' + response.data.message + '</p>');
                    setTimeout(function() {
                        window.location.href = '/dashboard'; // Redirect to dashboard
                    }, 2000);
                } else {
                    $('#login-message').html('<p class="error">' + response.data.message + '</p>');
                }
            }
        });
    });

    // Update User Role
    $('#update-role-btn').click(function(e) {
        e.preventDefault();

        let email = $('#user-email').val();
        let role = $('#user-role').val();
        let nonce = $('#_wpnonce').val(); // Fetch nonce from hidden field

        $.ajax({
            type: 'POST',
            url: ajaxurl, // WordPress AJAX URL
            data: {
                action: 'cool_kids_update_role',
                email: email,
                role: role,
                _wpnonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload(); // Refresh page after role update
                } else {
                    alert('Error: ' + response.data.message);
                }
            }
        });
    });
});
