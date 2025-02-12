<?php
// Include WordPress functions to work with users
if (!defined('ABSPATH')) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = sanitize_email($_POST['email']);

    // Check if email already exists
    if (email_exists($email)) {
        echo '<p style="color:red;">Email already registered!</p>';
        return;
    }

    // Generate fake user data via randomuser.me API
    $response = wp_remote_get('https://randomuser.me/api/');
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    // Extract random user data
    $first_name = $data['results'][0]['name']['first'];
    $last_name = $data['results'][0]['name']['last'];
    $country = $data['results'][0]['location']['country'];
    
    // Create the user account
    $user_id = wp_create_user($email, wp_generate_password(), $email);
    if (is_wp_error($user_id)) {
        echo '<p style="color:red;">Error creating user!</p>';
        return;
    }

    // Set the user role and meta data
    $user = new WP_User($user_id);
    $user->set_role('cool_kid'); // Default role is "Cool Kid"
    
    // Save additional user information
    update_user_meta($user_id, 'first_name', $first_name);
    update_user_meta($user_id, 'last_name', $last_name);
    update_user_meta($user_id, 'country', $country);

    // Redirect to login page
    echo '<p style="color:green;">Account created successfully! Redirecting to login...</p>';
    echo '<script>setTimeout(function() { window.location.href = "/login"; }, 2000);</script>';
    exit;
}
?>

<!-- Sign-up Form -->
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Confirm</button>
</form>
