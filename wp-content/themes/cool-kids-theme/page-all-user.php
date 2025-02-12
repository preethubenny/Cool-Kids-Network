<?php
/* Template Name: All Users */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
?>

<?php if (in_array('cooler_kid', $current_user->roles) || in_array('coolest_kid', $current_user->roles)) : ?>
    <h3>All Users</h3>
    <ul>
        <?php
        $users = get_users();
        foreach ($users as $user) {
            $user_info = get_userdata($user->ID);
            $role = reset($user_info->roles); // Get first role
            
            echo '<li>' . esc_html($user_info->first_name) . ' ' . esc_html($user_info->last_name) . ' (' . esc_html($role) . ') - ' . esc_html(get_user_meta($user->ID, 'country', true)) . '</li>';
        }
        ?>
    </ul>
<?php else: ?>
    <p>You do not have permission to view all users.</p>
<?php endif; ?>

<?php get_footer(); ?>
