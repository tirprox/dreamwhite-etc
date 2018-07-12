<?php
/*
Plugin Name: Исправление изображений в метке
Plugin URI:
Description: Показывает фото вариации, соответствующей метке по цвету.
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-product-image-fix
Domain Path: /languages
*/


function replacing_template_loop_product_thumbnail() {
    // Remove product images from the shop loop
    //remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
    // Adding something instead
    function wc_template_loop_product_replaced_thumb() {
        //echo "TEST TEST";
    }
    add_action( 'woocommerce_before_shop_loop_item_title', 'wc_template_loop_product_replaced_thumb', 10 );
}
add_action( 'woocommerce_init', 'replacing_template_loop_product_thumbnail');