<?php
/*
Plugin Name: DreamWhite Фильтр товаров
Plugin URI:
Description: Фильтрация меток как атрибутов
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-product-filter
Domain Path: /languages
*/

use Dreamwhite\Plugins\ProductFilter\TaxonomyDataHolder;
use Dreamwhite\Plugins\ProductFilter\TaxonomyParams;
use Dreamwhite\Plugins\ProductFilter\QueryManager;
use Dreamwhite\Plugins\ProductFilter\Renderer;
//use Dreamwhite\Plugins\ProductFilter\MongoAdapter;


require_once "includes.php";

dw_product_filter_init();

function dw_product_filter_init()
{
    //add_action('init', 'dw_custom_seo_taxonomy', 0);
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
    $term_id = $_POST['term_id'];

    ob_get_clean();

    ob_start();
    set_query_parameter($term_id, $attr, $value);

    ob_get_flush();
    die();

}

function set_query_parameter($term_id, $attr, $value) {

//    $tax = get_queried_object();
    $tax = get_term($term_id, 'attr');


    $params = new TaxonomyParams($tax);



    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);
    $queryManager->setQueryParameter($attr, $value);
    $queryManager->setQueryParameter('filterable', 1);

    $query = $queryManager->getMongoQuery();
    //var_dump($query);

    $mongo = new MongoAdapter();

    /*$results = $mongo->find($query['attributes'], $query['relations']);
    foreach ($results as $result) {
        //var_dump(get_term_link($term));
        Renderer::a($result->name, 'https://new.dreamwhite.ru/catalog/' . $result->slug . '/');
    }*/


    //mock_meta_query_args($queryManager);
    //var_dump($queryManager->getQueryArgs());
    //$terms = get_terms($queryManager->getQueryArgs());



    /*foreach ($terms as $term) {
        //var_dump(get_term_link($term));
        Renderer::a($term->name, get_term_link($term));
    }*/
}

function mock_meta_query_args(&$queryManager) {
    //$queryManager->setQueryParameter('gender', 'Женский');
    //$queryManager->setQueryParameter('type', 'Пальто');
    $queryManager->setQueryParameter('filterable', '1');
}

function dw_filter_tags_shortcode()
{
    $tax = get_queried_object();

    echo '<div class="dw-product-filter-wrapper" style="padding: 8px 16px" data-term-id="' . $tax->term_id . '">';


    //$taxDataHolder = new TaxonomyDataHolder();
    $mongo = new MongoAdapter();

    $params = new TaxonomyParams($tax);

    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);

    $data = function($attr, $value) {
        return 'data-attr-type="' . $attr . '" data-attr-value="' . $value .'"';
    };

    $class = function($attr, $value) use ($queryManager) {
        $isActive = mb_strpos($queryManager->getQueryParameter($attr), $value) !== false;
        return $isActive ? 'class="dw-color-button dw-filter-active"' : 'class="dw-color-button"';
    };


    echo '<div>';
    echo '<a style="background: #e9b281" ' . $class('colorGroup', 'Бежевый') . $data('colorGroup', 'Бежевый') . '></a>';
    echo '<a style="background: #1f1f1f" ' . $class('colorGroup', 'Черный') . $data('colorGroup', 'Черный') . '></a>';
    echo '<a style="background: #45d5ff" ' . $class('colorGroup', 'Бирюзовый') . $data('colorGroup', 'Голубой') . '></a>';
    echo '</div>';

    echo '<div>';
    echo '<a style="background: black" ' . $class('kapushon', 'Есть') . $data('kapushon', 'Есть') . '><span class="dw-filter-button-text">Капюшон</span></a>';
    echo '</div>';

    //$queryManager->setQueryParameter('color', 'Бежевый');

//    $matches = $taxDataHolder->matchValue('kapushon', $queryManager->getQueryParameter('kapushon'));
//    var_dump($matches);


    //$args = $queryManager->getQueryArgs();


    Renderer::header('Matching Taxonomies:');

    //$terms = get_terms($args);
    echo '<div class="matching-taxonomies">';
    /*foreach ($terms as $term) {
        Renderer::a($term->name, get_term_link($term));
    }*/
    echo '</div>';
    echo '</div>';
}

class FilterView {
    private $sections = [];

    function render() {
        echo '<div style="padding: 8px 16px">';

        foreach ($this->sections as $section) {
            Renderer::header($section['title']);
            foreach ($section['Attrs'] as $attr) {
            }
        }
        echo '</div>';
    }

    function addSection($title, $attrs) {
        $sections[] = [
            'title' => $title,
            'Attrs' => $attrs
        ];
    }
}

