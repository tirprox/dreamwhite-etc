<?php
/*
Plugin Name: Наличие на складах
Plugin URI:
Description: Отображение наличия для Москвы и СПб
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: woocommerce-stock
Domain Path: /languages
*/

add_filter( 'woocommerce_get_availability' , 'revised_woocommerce_get_availability' , 10, 2 );

function revised_woocommerce_get_availability( $available_array , $product) {

    if ( $product->managing_stock() ) {

        $stock = [];
        $stock['spb'] = get_post_meta( $product->id, 'stock_spb', false );
        $stock['msk'] = get_post_meta( $product->id, 'stock_msk', false );

        var_dump($product->id);
        var_dump($stock['msk'][0] );

    }
    return $available_array;
}

function resolveStockForCities($stockSpb, $stockMsk) {

}