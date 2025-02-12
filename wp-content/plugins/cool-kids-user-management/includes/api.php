<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// API to update user role
function cool_kids_update_role() {
    // Verify if the user is an administrator
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access. Admin privileges required.'), 403);
    }

    // Verify nonce for security
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'cool_kids_update_role_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed. Invalid request.'), 403);
    }

    // Validate and sanitize the email and role input
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';

    // Allowed roles
    $allowed_roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];

    if (!$email || !$role) {
        wp_send_json_error(array('message' => 'Missing required fields: email and role.'), 400);
    }

    if (!in_array($role, $allowed_roles)) {
        wp_send_json_error(array('message' => 'Invalid role. Accepted roles: Cool Kid, Cooler Kid, Coolest Kid.'), 400);
    }

    // Get the user by email address
    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found with this email: ' . $email), 404);
    }

    // Get the user ID
    $user_id = $user->ID;

    // If the user already has the requested role, return an error
    if (in_array($role, $user->roles)) {
        wp_send_json_error(array('message' => 'User already has the role: ' . $role), 400);
    }

    // Update the user role
    $user_update = wp_update_user(array('ID' => $user_id, 'role' => $role));

    if (is_wp_error($user_update)) {
        wp_send_json_error(array('message' => 'Failed to update the user role due to a system error.'), 500);
    }

    // Success response
    wp_send_json_success(array('message' => 'User role updated successfully to ' . ucfirst(str_replace('_', ' ', $role)) . '.'));
}

// Hook the function to AJAX action
add_action('wp_ajax_cool_kids_update_role', 'cool_kids_update_role');

// Generate nonce for AJAX requests
function cool_kids_create_nonce() {
    wp_nonce_field('cool_kids_update_role_nonce', '_wpnonce');
}
