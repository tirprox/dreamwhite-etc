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
use Dreamwhite\Plugins\ProductFilter\MongoAdapter;


require_once "includes.php";

dw_product_filter_init();

function dw_product_filter_init()
{
    //add_action('init', 'dw_custom_seo_taxonomy', 0);
    add_shortcode('custom_filter', 'dw_filter_tags_shortcode');


    add_action('wp_enqueue_scripts', 'dw_product_filter_enqueue_scripts');
    add_action('wp_enqueue_scripts', 'dw_product_filter_enqueue_styles');

    add_action('wp_ajax_nopriv_post_filter_var', 'post_filter_var');
    add_action('wp_ajax_post_filter_var', 'post_filter_var');
}

function dw_product_filter_enqueue_scripts()
{
    wp_enqueue_script('dw-product-filter', plugins_url('/js/product-filter.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('dw-product-filter', 'dwf', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}


function dw_product_filter_enqueue_styles()
{
    wp_enqueue_style('dw-product-filter-style', plugins_url('/css/product-filter.css', __FILE__));
}


function post_filter_var()
{

    $attr = $_POST['attr_type'];
    $value = $_POST['attr_value'];
    $term_id = $_POST['term_id'];

    ob_get_clean();

    ob_start();
    set_query_parameter($term_id, $attr, $value);

    ob_get_flush();
    die();

}

function set_query_parameter($term_id, $attr, $value)
{

//    $tax = get_queried_object();
    $tax = get_term($term_id, 'attr');


    $params = new TaxonomyParams($tax);
    //var_dump($params);


    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);
    $queryManager->setQueryParameter($attr, $value);
    $queryManager->setQueryParameter('filterable', 1);

    $query = $queryManager->getMongoQuery();
    //var_dump($params );

    $mongo = new MongoAdapter();

    $results = $mongo->find($query['attributes'], $query['relations']);

    $attrCount = count($query['attributes']);

    foreach ($results as $result) {
        if (count($result->attributes) === $attrCount) {
            Renderer::a($result->name, 'https://new.dreamwhite.ru/catalog/' . $result->slug . '/');
        }

//        Renderer::a($result->name, 'https://new.dreamwhite.ru/catalog/' . $result->slug . '/');


    }


    //mock_meta_query_args($queryManager);
    //var_dump($queryManager->getQueryArgs());
    //$terms = get_terms($queryManager->getQueryArgs());


    /*foreach ($terms as $term) {
        //var_dump(get_term_link($term));
        Renderer::a($term->name, get_term_link($term));
    }*/
}

function mock_meta_query_args(&$queryManager)
{
    $queryManager->setQueryParameter('gender', 'Женский');
    $queryManager->setQueryParameter('type', 'Пальто');
    $queryManager->setQueryParameter('filterable', '1');
}

function dw_filter_tags_shortcode()
{
    $tax = get_queried_object();

    echo '<div class="dw-product-filter-wrapper" style="padding: 8px 16px" data-term-id="' . $tax->term_id . '">';


    //$taxDataHolder = new TaxonomyDataHolder();
    $mongo = new MongoAdapter();


    $mongoQuery = ['name' => 'GLOBAL_TAG'];
    $globalAttrs = $mongo->findOne($mongoQuery);

    $glob = [];

    foreach ($globalAttrs as $result) {
        $glob = $result;
    }





    $params = new TaxonomyParams($tax);

    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);

    $data = function ($attr, $value) {
        return 'data-attr-type="' . $attr . '" data-attr-value="' . $value . '"';
    };

    $class = function ($attr, $value) use ($queryManager) {
        $isActive = mb_strpos($queryManager->getQueryParameter($attr), $value) !== false;
        return $isActive ? 'class="dw-filterable dw-filter-button dw-filterable-active"' : 'class="dw-filterable dw-filter-button"';
    };

    $colorClass = function ($attr, $value) use ($queryManager) {
        $isActive = mb_strpos($queryManager->getQueryParameter($attr), $value) !== false;
        return $isActive ? 'class="dw-filterable dw-color-button dw-filterable-color-active"' : 'class="dw-filterable dw-color-button"';
    };

    Renderer::header('Matching Taxonomies:');
    echo '<div class="matching-taxonomies">';
    echo '</div>';


    Renderer::header('Цвет');

    foreach ($glob->attributes->colorGroup as $color) {
        echo '<a style="background: ' . Colors::COLORMAP[$color] . '"' . $colorClass('colorGroup', $color) . $data('colorGroup', $color) . '></a>';
    }

    Renderer::header('Размер');
    foreach ($glob->attributes->size as $size) {
        echo '<a style="background: #757575"' . $colorClass('size', $size) . $data('size', $size) . '>' . $size . '</a>';
    }

    Renderer::header('Капюшон');
    foreach ($glob->attributes->kapushon as $kapushon) {
        echo '<a ' . $class('kapushon', $kapushon) . $data('kapushon', $kapushon) . '><span class="dw-filter-button-text">' . $kapushon . ' </span></a>';
    }
    //echo '<a ' . $class('kapushon', 'Есть') . $data('kapushon', 'Есть') . '><span class="dw-filter-button-text">Есть</span></a>';

    Renderer::header('Сезон');
    foreach ($glob->attributes->season as $season) {
        echo '<a ' . $class('season', $season) . $data('season', $season) . '><span class="dw-filter-button-text">' . $season . ' </span></a>';
    }

    Renderer::header('Текстура');
    foreach ($glob->attributes->texture as $texture) {
        echo '<a ' . $class('texture', $texture) . $data('texture', $texture) . '><span class="dw-filter-button-text">' . $texture . ' </span></a>';
    }





    echo '</div>';
}

class Colors
{
    public const COLORMAP = [
        'Синий' => '#1c4e8a',
        'Желтый'=> '#ecc13c',
        'Серый'=> '#6e6e6e',
        'Коричневый'=> '#894517',
        'Бежевый'=> '#e9b281',
        'Красный'=> '#ff2126',
        'Марсала'=> '#952b3b',
        'Хаки'=> '#52682d',
        'Розовый'=> '#ffb3b9',
        'Зеленый'=> '#1d6b3e',
        'Персиковый'=> '#d49600',
        'Черный'=> '#1f1f1f',
        'Фиолетовый'=> '#73387c',
        'Белый'=> '#f1f1f1',
        'Сиреневый'=> '#fca2cf',
        'Голубой'=> '#45d5ff',
        'Бордовый' => '#720000',
    ];


}

class FilterView
{
    private $sections = [];

    function render()
    {
        echo '<div style="padding: 8px 16px">';

        foreach ($this->sections as $section) {
            Renderer::header($section['title']);
            foreach ($section['Attrs'] as $attr) {
            }
        }
        echo '</div>';
    }

    function addSection($title, $attrs)
    {
        $sections[] = [
            'title' => $title,
            'Attrs' => $attrs
        ];
    }
}

