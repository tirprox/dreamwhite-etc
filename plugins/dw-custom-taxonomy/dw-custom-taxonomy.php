<?php
/*
Plugin Name: DreamWhite SEO-Метки (Custom Taxonomy)
Plugin URI:
Description: Добавляет таксономию SEO-Метки
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-seo-taxonomy
Domain Path: /languages
*/

add_action('init', 'dw_custom_seo_taxonomy', 0);
function dw_custom_seo_taxonomy()
{

    $labels = array(
        'name' => 'SEO-Метки',
        'singular_name' => 'SEO-Метка',
        'menu_name' => 'SEO-Метки',
        'all_items' => 'Все SEO-Метки',
        'parent_item' => 'Родительская',
        'parent_item_colon' => 'Родительская:',
        'new_item_name' => 'Новая метка',
        'add_new_item' => 'Добавить метку',
        'edit_item' => 'Редактировать',
        'update_item' => 'Обновить',
        'separate_items_with_commas' => 'Separate Brand with commas',
        'search_items' => 'Поиск по меткам',
        'add_or_remove_items' => 'Add or remove Brands',
        'choose_from_most_used' => 'Choose from the most used Brands',
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => false,
        'rewrite' => [
            'slug' => 'catalog',
            'with_front' => false
        ]
    );
    register_taxonomy('attr', 'product', $args);

}

