<?php
/*
Plugin Name: DreamWhite SEO - улучшения (мелкие)
Plugin URI:
Description: Символ валюты и тд.
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-seo-other
Domain Path: /languages
*/

add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);
function change_existing_currency_symbol( $currency_symbol, $currency ) {
    switch( $currency ) {
        case 'RUB': $currency_symbol = 'руб.'; break;
    }
    return $currency_symbol;
}

