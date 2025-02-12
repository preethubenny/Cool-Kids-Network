<?php
/**
 * Plugin Name: Cool Kids User Management
 * Description: Manage users, roles, and API for Cool Kids Network.
 * Version: 1.0
 * Author: Preethu Benny
 * Author URI: https://linkedin.com/in/preethu-benny-3965b51b7
 * Text Domain: cool-kids-user-management
 * License: GPL2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('CKUM_PLUGIN_DIR', plugin_dir_path(__FILE__));  // Plugin directory
define('CKUM_PLUGIN_URL', plugin_dir_url(__FILE__));    // Plugin URL

// Enqueue Styles and Scripts
function cool_kids_enqueue_assets() {
    wp_enqueue_style('cool-kids-style', CKUM_PLUGIN_URL . 'assets/css/style.css');
    wp_enqueue_script('cool-kids-custom-js', CKUM_PLUGIN_URL . 'assets/js/custom.js', array('jquery'), null, true);

    // Localize AJAX URL and Nonce for security
    wp_localize_script('cool-kids-custom-js', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cool_kids_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'cool_kids_enqueue_assets');

// Shortcodes for user pages
function cool_kids_signup_page_shortcode() {
    ob_start();
    include get_template_directory() . '/signup.php';  // Include from theme
    return ob_get_clean();
}
add_shortcode('cool_kids_signup', 'cool_kids_signup_page_shortcode');

function cool_kids_login_page_shortcode() {
    ob_start();
    include get_template_directory() . '/login.php';  // Include from theme
    return ob_get_clean();
}
add_shortcode('cool_kids_login', 'cool_kids_login_page_shortcode');

function cool_kids_dashboard_page_shortcode() {
    ob_start();
    include get_template_directory() . '/dashboard.php';  // Include from theme
    return ob_get_clean();
}
add_shortcode('cool_kids_dashboard', 'cool_kids_dashboard_page_shortcode');

// AJAX Actions for Login and Signup
function cool_kids_user_signup() {
    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_send_json_error(array('message' => 'Invalid email.'));
    }

    $email = sanitize_email($_POST['email']);
    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'Email already exists.'));
    }

    // Create the user
    $user_data = array(
        'user_login' => $email,
        'user_email' => $email,
        'role' => 'cool_kid',  // Default role
    );
    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => 'Error creating account.'));
    }

    // Generate and save fake identity for user
    $random_data = cool_kids_generate_random_identity();
    update_user_meta($user_id, 'first_name', $random_data['first_name']);
    update_user_meta($user_id, 'last_name', $random_data['last_name']);
    update_user_meta($user_id, 'country', $random_data['country']);
    update_user_meta($user_id, 'role', 'cool_kid');  // Default role

    wp_send_json_success(array('message' => 'Account created successfully.'));
}
add_action('wp_ajax_nopriv_cool_kids_user_signup', 'cool_kids_user_signup');

// Handle User Login
function cool_kids_user_login() {
    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_send_json_error(array('message' => 'Invalid email.'));
    }

    $email = sanitize_email($_POST['email']);
    $user = get_user_by('email', $email);

    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
    }

    // Successful login (for demo purposes, no password check)
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);

    wp_send_json_success(array('message' => 'Login successful.'));
}
add_action('wp_ajax_nopriv_cool_kids_user_login', 'cool_kids_user_login');

// Role Update API (for Admins)
function cool_kids_update_role() {
    if (!isset($_POST['email']) || !isset($_POST['role'])) {
        wp_send_json_error(array('message' => 'Missing parameters.'));
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized.'));
    }

    $email = sanitize_email($_POST['email']);
    $role = sanitize_text_field($_POST['role']);

    if (!in_array($role, ['cool_kid', 'cooler_kid', 'coolest_kid'])) {
        wp_send_json_error(array('message' => 'Invalid role.'));
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
    }

    wp_update_user(array('ID' => $user->ID, 'role' => $role));
    wp_send_json_success(array('message' => 'Role updated successfully.'));
}
add_action('wp_ajax_cool_kids_update_role', 'cool_kids_update_role');

// Helper function to generate random user data (from randomuser.me)
function cool_kids_generate_random_identity() {
    $response = wp_remote_get('https://randomuser.me/api/');
    if (is_wp_error($response)) {
        return array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'country' => 'Canada'
        );
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $user_data = $data['results'][0];

    return array(
        'first_name' => $user_data['name']['first'],
        'last_name' => $user_data['name']['last'],
        'country' => $user_data['location']['country']
    );
}

// Display All Users
function cool_kids_display_all_users() {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url());
        exit;
    }

    $current_user = wp_get_current_user();
    if (in_array('cooler_kid', $current_user->roles) || in_array('coolest_kid', $current_user->roles)) {
        // Start the output buffer
        ob_start();

        echo '<h3>All Users</h3>';
        echo '<ul>';

        $users = get_users(); // Get all users
        foreach ($users as $user) {
            $user_info = get_userdata($user->ID);
            $roles = $user_info->roles; // Get all roles assigned to the user

            // Display all roles
            $role_display = !empty($roles) ? implode(', ', array_map('ucfirst', $roles)) : 'No role assigned'; // Join multiple roles with commas

            // Output user information
            echo '<li>' . esc_html($user_info->first_name) . ' ' . esc_html($user_info->last_name) . ' (' . esc_html($role_display) . ') - ' . esc_html(get_user_meta($user->ID, 'country', true)) . '</li>';
        }

        echo '</ul>';

        // Get the buffered content
        return ob_get_clean();
    } else {
        return '<p>You do not have permission to view all users.</p>';
    }
}

// Register the function to show users on the page
add_shortcode('cool_kids_all_users', 'cool_kids_display_all_users');

// Shortcode to display user dashboard after login
function cool_kids_user_dashboard() {
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url());
        exit;
    }

    $current_user = wp_get_current_user();
    
    // Display user info
    ob_start();  // Start output buffering

    echo '<h3>Welcome, ' . esc_html($current_user->first_name) . '!</h3>'; // Display first name
    echo '<p><strong>First Name:</strong> ' . esc_html($current_user->first_name) . '</p>';
    echo '<p><strong>Last Name:</strong> ' . esc_html($current_user->last_name) . '</p>';
    echo '<p><strong>Country:</strong> ' . esc_html(get_user_meta($current_user->ID, 'country', true)) . '</p>';
    echo '<p><strong>Email:</strong> ' . esc_html($current_user->user_email) . '</p>';

    // Get user roles and display them
    $roles = $current_user->roles;
    $role_display = !empty($roles) ? implode(', ', array_map('ucfirst', $roles)) : 'No role assigned';
    echo '<p><strong>Role:</strong> ' . esc_html($role_display) . '</p>';

    return ob_get_clean(); // Return buffered content
}

// Register the shortcode for the dashboard
add_shortcode('cool_kids_user_dashboard', 'cool_kids_user_dashboard');
