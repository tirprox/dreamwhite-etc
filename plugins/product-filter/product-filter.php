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

require_once "includes.php";

init();

function init()
{
    add_action('init', 'dw_custom_seo_taxonomy', 0);
    add_shortcode('custom_filter', 'dw_filter_tags_shortcode');


    add_action( 'wp_enqueue_scripts', 'dw_product_filter_enqueue_scripts' );
    add_action( 'wp_enqueue_scripts', 'dw_product_filter_enqueue_styles' );

    add_action( 'wp_ajax_nopriv_post_filter_var', 'post_filter_var' );
    add_action( 'wp_ajax_post_filter_var', 'post_filter_var' );
}

function dw_product_filter_enqueue_scripts() {
    wp_enqueue_script( 'dw-product-filter', plugins_url( '/js/product-filter.js', __FILE__ ), array('jquery'), '1.0', true );
    wp_localize_script( 'dw-product-filter', 'dwf', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}


function dw_product_filter_enqueue_styles() {
    wp_enqueue_style( 'dw-product-filter-style', plugins_url( '/css/product-filter.css', __FILE__ ) );
}


function post_filter_var() {

    $attr = $_POST['attr_type'];
    $value = $_POST['attr_value'];

    ob_get_clean();

    ob_start();
    set_query_parameter($attr, $value);

    ob_get_flush();
    die();

}

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
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
        'rewrite' => [
            'slug' => 'catalog',
            'with_front' => false
        ]
    );
    register_taxonomy(FilterConfig::TAX_NAME, 'product', $args);

}


function set_query_parameter($attr, $value) {

    $tax = get_queried_object();

    $params = new TaxonomyParams($tax);

    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);
    $queryManager->setQueryParameter($attr, $value);


    //mock_meta_query_args($queryManager);

    $terms = get_terms($queryManager->getQueryArgs());

    foreach ($terms as $term) {
        //var_dump(get_term_link($term));
        Renderer::a($term->name, get_term_link($term));
    }
}

function mock_meta_query_args(&$queryManager) {
    $queryManager->setQueryParameter('gender', 'Женский');
    $queryManager->setQueryParameter('type', 'Пальто');
}

function dw_filter_tags_shortcode()
{

    echo '<div style="padding: 8px 16px">';




    $taxDataHolder = new TaxonomyDataHolder();

    $tax = get_queried_object();

    $params = new TaxonomyParams($tax);

    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);

    $data = function($attr, $value) {
        return 'data-attr-type="' . $attr . '" data-attr-value="' . $value .'"';
    };

    echo '<div>';
    echo '<a style="background: #e9b281" class="dw-color-button"' . $data('color', 'Бежевый') . '>Бежевый</a>';
    echo '<a style="background: black" class="dw-color-button"' . $data('color', 'Черный') . '>Черный</a>';
    echo '<a style="background: cyan" class="dw-color-button"' . $data('color', 'Бирюзовый') . '>Бирюзовый</a>';
    echo '</div>';

    echo '<div>';
    echo '<a style="background: black" class="dw-color-button"' . $data('kapushon', 'Есть') . '>Капюшон</a>';
    echo '</div>';

    //$queryManager->setQueryParameter('color', 'Бежевый');

//    $matches = $taxDataHolder->matchValue('kapushon', $queryManager->getQueryParameter('kapushon'));
//    var_dump($matches);


    $args = $queryManager->getQueryArgs();


    Renderer::header('Matching Taxonomies:');

    $terms = get_terms($args);
    echo '<div class="matching-taxonomies">';
    foreach ($terms as $term) {
        Renderer::a($term->name, get_term_link($term));
    }
    echo '</div>';
    echo '</div>';
}



