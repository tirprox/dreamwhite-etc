<?php

function merchandiser_child_theme_setup() {
    load_child_theme_textdomain( 'merchandiser', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'merchandiser_child_theme_setup' );

register_nav_menu( 'mobile', __( 'Mobile Menu', 'theme-slug' ) );

if ( ! is_admin() ) {
    if ( defined('WC_VERSION') ) {
        add_action( 'wp_enqueue_scripts', 'child_theme_scripts', 20 );
        function child_theme_scripts(){
            wp_enqueue_script( 'yandex-target', '/metrika/yandex-target.js', array( 'jquery' ), false, true );
            wp_enqueue_script( 'custom-js', '/js/custom-js.js', array( 'jquery' ), false, true );
        }
    }
}
