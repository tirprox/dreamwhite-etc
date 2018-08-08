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

//namespace Dreamwhite\Site;

function custom_seo_taxonomy()  {

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
    );
    register_taxonomy( 'attr', 'product', $args );

}

add_action( 'init', 'custom_seo_taxonomy', 0 );

// Add Shortcode
function custom_shortcode() {

    $tax = get_queried_object();
    $meta = get_term_meta($tax->term_id);

    $term_id = $tax->term_id;

    echo '<h4>TERM ID</h4>';
    echo $tax->term_id . '<br>';


    echo '<h4>Цвет</h4>';
    echo '<div class="variation-colors">';

    $colors = explode(',', $meta['color'][0]);

    $params = [
      'color' => 'Черный',
    ];

    set_query_var('color', 'Черный');
    set_query_var('uteplitel', 'Нет');

    //set_query_var('type', 'Пальто');
    //set_query_var('gender', 'Женский');

    foreach ($colors as $color) {
        echo $color . '<br>';
    }
    echo '<br>';

    $q_color = get_query_var( 'color');
    $q_uteplitel = get_query_var( 'uteplitel');


    $args = array(
        'taxonomy'   => 'attr',
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key'       => 'color',
                'value'     => $q_color,
                'compare'   => 'LIKE'
            ),
            [
                'key'       => 'uteplitel',
                'value'     => $q_uteplitel,
                'compare'   => 'LIKE'
            ],
        )
    );

    $terms = get_terms($args);

    echo '</div>';
    echo '<h4>QUERY VAR</h4>';
    echo $q_color . '<br>';

    echo '<h4>TAXES</h4>';

    foreach ($terms as $term) {
        echo $term->name . '<br>';
    }

    echo '<br>';

    if (isset($terms) && $terms[0]->term_id !== $term_id) {
        wp_redirect(get_term_link($terms[0]->term_id));
    }


}

add_shortcode( 'custom_filter', 'custom_shortcode' );

function handle_custom_filter_var($query, $query_vars)
{
    if (!empty($query_vars['color'])) {
        $query['meta_query'][] = array(
            'key' => 'color',
            'value' => esc_attr($query_vars['article']),
        );
    }

    return $query;
}

add_filter('woocommerce_product_data_store_cpt_get_products_query', 'handle_custom_filter_var', 10, 2);


function add_query_vars_filter( $vars ) {
    $vars[] = "color";
    return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );