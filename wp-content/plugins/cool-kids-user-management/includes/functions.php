<?php
if (!defined('ABSPATH')) {
    exit;
}

// Fetch random user details
function get_random_user_data() {
    $response = wp_remote_get('https://randomuser.me/api/?nat=us,ca,gb,au');
    if (is_wp_error($response)) {
        return false;
    }
    $data = json_decode(wp_remote_retrieve_body($response), true);
    return array(
        'first_name' => $data['results'][0]['name']['first'],
        'last_name' => $data['results'][0]['name']['last'],
        'country' => $data['results'][0]['location']['country']
    );
}

// Register custom roles: 'Cooler Kid' and 'Coolest Kid'
function register_custom_roles() {
    // Register custom roles
    add_role(
        'cool_kid',
        'Cool Kid',
        array(
            'read' => true, // can read content
            'level_0' => true,
        )
    );

    add_role(
        'cooler_kid',
        'Cooler Kid',
        array(
            'read' => true, // can read content
            'level_1' => true,
        )
    );

    add_role(
        'coolest_kid',
        'Coolest Kid',
        array(
            'read' => true, // can read content
            'level_2' => true,
        )
    );
}

add_action('init', 'register_custom_roles');


// Create user with a random identity and assigned role
function cool_kids_create_user($email, $role = 'cool_kid') {
    // Ensure role is valid
    if (!in_array($role, ['cool_kid', 'cooler_kid', 'coolest_kid'])) {
        return new WP_Error('invalid_role', 'Invalid role.');
    }

    // Generate random user data
    $user_data = get_random_user_data();
    
    if (!$user_data) {
        return new WP_Error('random_user_error', 'Could not generate random user data.');
    }

    // Check if email is already registered
    if (email_exists($email)) {
        return new WP_Error('email_exists', 'Email already registered.');
    }

    // Prepare user data array
    $user_args = array(
        'user_login' => $email,
        'user_email' => $email,
        'user_pass' => wp_generate_password(), // Password is generated for now
        'role' => $role, // Dynamically set role
        'first_name' => $user_data['first_name'],
        'last_name' => $user_data['last_name']
    );

    // Create user
    $user_id = wp_insert_user($user_args);
    
    // Handle errors in user creation
    if (is_wp_error($user_id)) {
        return $user_id;
    }

    // Store country as user meta
    update_user_meta($user_id, 'user_country', $user_data['country']);
    
    return $user_id;
}

// Handle user sign-up request
function cool_kids_user_signup() {
    if (isset($_POST['email']) && isset($_POST['username'])) {
        $email = sanitize_email($_POST['email']);
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        
        // Check if user already exists
        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error(array('message' => 'User already exists'));
        }

        // Create new user
        $user_id = wp_create_user($username, $password, $email);

        if (!is_wp_error($user_id)) {
            // Set the default role to 'Cool Kid'
            $user = new WP_User($user_id);
            $user->set_role('cool_kid'); // Make sure you have registered this role

            // Generate fake identity data (using randomuser.me or similar)
            // Example: Assign random name, country, etc.
            // You might want to store this data in user meta
            update_user_meta($user_id, 'first_name', 'GeneratedFirstName');
            update_user_meta($user_id, 'last_name', 'GeneratedLastName');
            update_user_meta($user_id, 'country', 'GeneratedCountry');
            update_user_meta($user_id, 'role', 'Cool Kid');  // Ensure this is stored too

            wp_send_json_success(array('message' => 'User registered successfully'));
        } else {
            wp_send_json_error(array('message' => 'Error creating user'));
        }
    }
    wp_die();
}
add_action('wp_ajax_nopriv_cool_kids_user_signup', 'cool_kids_user_signup');
add_action('wp_ajax_cool_kids_user_signup', 'cool_kids_user_signup');

// Handle user login request
function cool_kids_handle_login() {
    if (!isset($_POST['email'])) {
        wp_send_json_error(array('message' => 'Email is required.'));
    }

    // Sanitize email
    $email = sanitize_email($_POST['email']);

    // Check if user exists
    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
    }

    // Log the user in
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);

    wp_send_json_success(array('message' => 'Login successful.'));
}
add_action('wp_ajax_cool_kids_login', 'cool_kids_handle_login'); // For logged-in users
add_action('wp_ajax_nopriv_cool_kids_login', 'cool_kids_handle_login'); // For anonymous users

// API to update user role
function cool_kids_update_role() {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'cool_kids_nonce_action')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
    }

    if (isset($_POST['email']) && isset($_POST['role'])) {
        $email = sanitize_email($_POST['email']);
        $role = sanitize_text_field($_POST['role']);
        
        // Validate role
        if (!in_array($role, ['cool_kid', 'cooler_kid', 'coolest_kid'])) {
            wp_send_json_error(array('message' => 'Invalid role'));
        }

        // Get user by email
        $user = get_user_by('email', $email);

        if ($user) {
            // Set user role
            $user->set_role($role);
            wp_send_json_success(array('message' => 'Role updated successfully'));
        } else {
            wp_send_json_error(array('message' => 'User not found'));
        }
    }
    wp_die();
}
add_action('wp_ajax_cool_kids_update_role', 'cool_kids_update_role');

// Handle fetching user data for Cooler Kid and Coolest Kid roles
function cool_kids_get_user_data() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User is not logged in.'));
    }

    $current_user = wp_get_current_user();
    if (!in_array('cooler_kid', $current_user->roles) && !in_array('coolest_kid', $current_user->roles)) {
        wp_send_json_error(array('message' => 'Insufficient permissions.'));
    }

    // Fetch all users' names and countries for Cooler Kid or Coolest Kid roles
    $users = get_users();
    $user_data = array();

    foreach ($users as $user) {
        $user_info = get_userdata($user->ID);
        $user_data[] = array(
            'first_name' => $user_info->first_name,
            'last_name' => $user_info->last_name,
            'country' => get_user_meta($user->ID, 'user_country', true)
        );
    }

    wp_send_json_success(array('message' => 'Users fetched successfully', 'data' => $user_data));
}
add_action('wp_ajax_cool_kids_get_user_data', 'cool_kids_get_user_data'); // For Cooler Kid and Coolest Kid roles

// Fetch email and role for Coolest Kid role
function cool_kids_get_email_role_data() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'User is not logged in.'));
    }

    $current_user = wp_get_current_user();
    if (!in_array('coolest_kid', $current_user->roles)) {
        wp_send_json_error(array('message' => 'Insufficient permissions.'));
    }

    // Fetch all users' email and role for Coolest Kid role
    $users = get_users();
    $user_data = array();

    foreach ($users as $user) {
        $user_info = get_userdata($user->ID);
        $user_data[] = array(
            'email' => $user_info->user_email,
            'role' => implode(', ', $user_info->roles)
        );
    }

    wp_send_json_success(array('message' => 'User roles and emails fetched successfully', 'data' => $user_data));
}
add_action('wp_ajax_cool_kids_get_email_role_data', 'cool_kids_get_email_role_data'); // For Coolest Kid role
?>
