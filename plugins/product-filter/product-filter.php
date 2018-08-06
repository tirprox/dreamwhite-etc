<?php
/*
Plugin Name: Фильтр товаров
Plugin URI:
Description: Работает с метками и
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-product-filter
Domain Path: /languages
*/

namespace Dreamwhite\Site;

function ess_custom_taxonomy_Item()  {

    $labels = array(
        'name'                       => 'SEO-Метки',
        'singular_name'              => 'SEO-Метка',
        'menu_name'                  => 'SEO-Метки',
        'all_items'                  => 'Все SEO-Метки',
        'parent_item'                => 'Родительская',
        'parent_item_colon'          => 'Родительская:',
        'new_item_name'              => 'Новая метка',
        'add_new_item'               => 'Добавить метку',
        'edit_item'                  => 'Редактировать',
        'update_item'                => 'Обновить',
        'separate_items_with_commas' => 'Separate Brand with commas',
        'search_items'               => 'Поиск по меткам',
        'add_or_remove_items'        => 'Add or remove Brands',
        'choose_from_most_used'      => 'Choose from the most used Brands',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => true
    );
    register_taxonomy( 'attr', 'product', $args );

}

add_action( 'init', 'ess_custom_taxonomy_item', 0 );

// Add Shortcode
function custom_shortcode() {


    $tax = get_queried_object();
    $meta = get_term_meta($tax->term_id);

    echo '<h4>Цвет</h4>';
    echo '<div class="variation-colors">';

    $colors = explode(',', $meta['color'][0]);
    var_dump($colors);

    echo '</div>';
}


add_shortcode( 'custom_filter', 'custom_shortcode' );