<?php
/* Template Name: Admin Page */

get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['role'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'cool_kids_update_role_nonce')) {
        wp_die('Security check failed!');
    }

    $email = sanitize_email($_POST['email']);
    $new_role = sanitize_text_field($_POST['role']);

    // Validate role
    $valid_roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];
    if (!in_array($new_role, $valid_roles)) {
        wp_die('Invalid role selected.');
    }

    $user = get_user_by('email', $email);
    if ($user) {
        $user->set_role($new_role); // Set new role, removing old ones
        echo "<p style='color: green;'>Role updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>User not found.</p>";
    }
}
?>

<div class="admin-page-container">
    <h2>Manage User Roles</h2>
    
    <!-- Role Update Form -->
    <form method="post">
        <?php wp_nonce_field('cool_kids_update_role_nonce', '_wpnonce'); ?>
        
        <input type="email" name="email" placeholder="User Email" required>
        
        <select name="role">
            <option value="cool_kid">Cool Kid</option>
            <option value="cooler_kid">Cooler Kid</option>
            <option value="coolest_kid">Coolest Kid</option>
        </select>
        
        <button type="submit">Update Role</button>
    </form>
</div>

<?php get_footer(); ?>
