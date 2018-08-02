<?php
/*
Plugin Name: Права пользователей на мультисайт
Plugin URI:
Description: Замена префикса для базы данных при проверке прав пользователей в Москве
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-user-caps
Domain Path: /languages
*/

add_action( 'set_current_user', 'dw_custom_caps_prefix', '-1000', '0' );

function dw_custom_caps_prefix() {
    $current_user = wp_get_current_user();
    $current_user->cap_key = "spb_capabilities";
    $current_user->caps = get_user_meta($current_user->ID, $current_user->cap_key, true);


    $current_user->get_role_caps();

}
