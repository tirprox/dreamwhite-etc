<?php
/*
Plugin Name: DreamWhite Facebook Conversions
Plugin URI:
Description: Цели фейсбука
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-facebook-conversions
Domain Path: /languages
*/

add_action('wp_enqueue_scripts', 'dw_facebook_conversions_enqueue_scripts');

function dw_facebook_conversions_enqueue_scripts() {
    wp_enqueue_script('dw_facebook_conversions', plugins_url('/js/dw_facebook_conversions.js', __FILE__), array('jquery'), '1.0', true);
}