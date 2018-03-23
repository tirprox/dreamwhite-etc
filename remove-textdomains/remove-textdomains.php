<?php
/*
Plugin Name: Отключение переводов
Plugin URI:
Description: Отключает TextDomain для плагинов в файле
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: disable-textdomains-list
Domain Path: /languages
*/

add_filter( 'override_load_textdomain', 'stop_language_files', 10, 3 );
function stop_language_files( $bool, $domain, $mofile )
{
    $blocked = [
        'clearfy',
        'comments-plus',
        'ivpawoo',
    ];

    if (in_array($domain, $blocked)) {
        return true;
    }

    if('clearfy' === $domain) return true;

    return $bool;
}