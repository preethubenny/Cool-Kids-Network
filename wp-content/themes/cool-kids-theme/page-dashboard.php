<?php
/* Template Name: Dashboard */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
$user_first_name = get_user_meta($current_user->ID, 'first_name', true);
$user_last_name = get_user_meta($current_user->ID, 'last_name', true);
$user_country = get_user_meta($current_user->ID, 'country', true);
$user_role = reset($current_user->roles); // Get first role

?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo esc_html($user_first_name); ?>!</h2>
    <p><strong>First Name:</strong> <?php echo esc_html($user_first_name); ?></p>
    <p><strong>Last Name:</strong> <?php echo esc_html($user_last_name); ?></p>
    <p><strong>Country:</strong> <?php echo esc_html($user_country); ?></p>
    <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
    <p><strong>Role:</strong> <?php echo esc_html(ucwords(str_replace('_', ' ', $user_role))); ?></p>

    <?php if (in_array($user_role, ['cooler_kid', 'coolest_kid'])) : ?>
        <h3>All Users</h3>
        <ul>
            <?php
            $users = get_users();
            foreach ($users as $user) {
                $user_info = get_userdata($user->ID);
                $role = reset($user_info->roles);
                echo '<li>' . esc_html($user_info->first_name) . ' ' . esc_html($user_info->last_name) . ' - ' . esc_html(get_user_meta($user->ID, 'country', true)) . '</li>';

                if ($user_role === 'coolest_kid') {
                    echo '<p>Email: ' . esc_html($user_info->user_email) . ' | Role: ' . esc_html($role) . '</p>';
                }
            }
            ?>
        </ul>
    <?php endif; ?>
</div>

<style>
.dashboard-container {
    width: 60%;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.dashboard-container h2 {
    text-align: center;
    margin-bottom: 20px;
}

.dashboard-container p {
    font-size: 18px;
    margin-bottom: 15px;
}

.dashboard-container ul {
    list-style: none;
    padding: 0;
}

.dashboard-container li {
    font-size: 16px;
    margin-bottom: 10px;
}
</style>

<?php get_footer(); ?>
