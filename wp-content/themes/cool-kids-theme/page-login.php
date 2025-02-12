<?php
// Handle user login using email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = sanitize_email($_POST['email']);

    $user = get_user_by('email', $email);

    if ($user) {
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        wp_redirect(home_url('/dashboard')); // Redirect to the dashboard page
        exit;
    } else {
        echo 'Invalid email address!';
    }
}
?>

<!-- Login Form -->
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Login</button>
</form>
