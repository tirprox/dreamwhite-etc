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

add_filter('woocommerce_get_availability', 'revised_woocommerce_get_availability', 10, 2);

function revised_woocommerce_get_availability($available_array, $product) {
   
   $stock = [];
   
   
   $stock[ 'spb' ] = get_post_meta($product->get_variation_id(), 'stock_spb', true);
   $stock[ 'msk' ] = get_post_meta($product->get_variation_id(), 'stock_msk', true);
   
   $spbTitle = "В Санкт-Петербурге: ";
   $mskTitle = "В Москве:  ";
   $avText = '';
   
   $otherCity = CITY === 'spb' ? 'msk' : 'spb';
   
   if ($stock[CITY] > 0) {
      $avText = $stock[CITY] . ' в наличии';
   }
   else if ($stock[$otherCity] > 0) {
      $avText = $spbTitle . $stock[ 'spb' ]
         . '<br>'
         . $mskTitle . $stock[ 'msk' ]
         . '<br>'
         . 'Свяжитесь с нами для уточнения условий предзаказа'
      ;
   }
   
   //$avText = $spbTitle . $stock[ 'spb' ] . '<br>' . $mskTitle . $stock[ 'msk' ];
   
   $available_array[ "availability" ] = $avText;
   
   return $available_array;
}

function resolveStockForCities($stockSpb, $stockMsk) {

}