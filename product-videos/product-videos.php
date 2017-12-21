<?php
/*
Plugin Name: Видео товаров
Plugin URI:
Description: Замена картинки товара на видео
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: product-videos
Domain Path: /languages
*/

add_action( 'woocommerce_before_single_product', 'show_video_not_image' );

function show_video_not_image() {

// Do this for product ID = 282 only
	if ( is_single( '3037' ) ) {
		
		//remove_action( 'woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_images', 20 );
		//remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
		
		add_action( 'woocommerce_product_thumbnails', 'show_product_video', 30 );
	}
	
}

function show_product_video() {
	echo '<div class="woocommerce-product-gallery">';

// get video embed HTML from YouTube
	echo '<iframe width="120" height="120" src="https://www.youtube.com/embed/ox_Dyze_dkA?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>';
	
	echo '</div>';
}