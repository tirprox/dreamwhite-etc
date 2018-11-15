<?php
/*
Plugin Name: DreamWhite Instagram
Plugin URI:
Description: Лента инстаграма по хэштегу в товары
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-instagram
Domain Path: /languages
*/

/**
 * Add Instagram tab
 */
function instagram_product_tab( $tabs ) {
    $tabs['instagram_tab'] = array(
        'title'    => __( 'Instagram', 'dw-instagram' ),
        'callback' => 'instagram_tab_content',
        'priority' => 50,
    );
    return $tabs;
}

function instagram_tab_content( $slug, $tab ) {
    ?>
    <div id="instafeed"></div>

    <?php
}

add_filter( 'woocommerce_product_tabs', 'instagram_product_tab' );

add_action('wp_enqueue_scripts', 'dw_instagram_enqueue_scripts');
add_action('wp_enqueue_scripts', 'dw_instagram_enqueue_styles');

function dw_instagram_enqueue_scripts()
{
    wp_enqueue_script('dw-instagram', plugins_url('/assets/instafeed.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('dw-instagram', plugins_url('/assets/dw-instagram.js', __FILE__), array('jquery'), '1.0', true);
}


function dw_instagram_enqueue_styles()
{
    wp_enqueue_style('dw-instagram-style', plugins_url('/assets/dw-instagram.css', __FILE__));
}