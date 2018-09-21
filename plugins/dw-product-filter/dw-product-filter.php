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
    $value = strval($_POST['attr_value']);
    $term_id = $_POST['term_id'];

    $params = $_POST['params'];

    ob_get_clean();

    ob_start();
    set_query_parameter($term_id, $attr, $value, $params);

    ob_get_flush();
    die();

}

function set_query_parameter($term_id, $attr, $value, $getQueryParams)
{

    $tax = get_term($term_id, 'attr');
    $params = new TaxonomyParams($tax);

    $type = implode($params->getParameter('type'));
    $gender = implode($params->getParameter('gender'));

    $queryManager = new QueryManager();

    $queryManager->fromTaxonomyParams($params);

    if (!empty($getQueryParams)) {
        $queryManager->fromArrayWithWCKeys($getQueryParams);
    }

    $queryManager->setQueryParameter($attr, $value);
    //$queryManager->setQueryParameter('filterable', 1);
//    $queryManager->setQueryParameter('hasRecords', 1);

    $mongoQuery = $queryManager->getMongoQuery();

    $mongo = new MongoAdapter();

    $mongo->setCollection('tag-test');

    $results = $mongo->find($mongoQuery['attributes'], $mongoQuery['relations']);

    $attrCount = count($mongoQuery['attributes']);

    $resultCount = 0;

    $isFilterable = get_term_meta($term_id, 'filterable', true);


    if ($isFilterable != 0) {
        foreach ($results as $result) {
            $resultCount++;
            if (count($result->attributes) === $attrCount) {
                $url = '/catalog/' . $result->slug . '/';
                echo json_encode(['url' => $url, 'query' => $mongoQuery], JSON_UNESCAPED_UNICODE);
            }
            break;
        }

        if ($resultCount === 0) {

            $queryParams = $queryManager->getWooCommerceQuery();
            $url = '/catalog' . QueryManager::GENDER_TYPE_MAP[$gender][$type] . '?' . http_build_query($queryParams);
            echo json_encode(['url' => $url, 'query' => $mongoQuery], JSON_UNESCAPED_UNICODE);
        }
    }

    else {
        $queryParams = $queryManager->getWooCommerceQuery();

        $query = http_build_query($queryParams);

        $postfix = $query !== '' ? '?' . $query : '';

        $url = get_term_link($tax) . $postfix;
        //$url = '/catalog' . QueryManager::GENDER_TYPE_MAP[$gender][$type] . '?' . http_build_query($queryParams);
        echo json_encode(['url' => $url, 'query' => $mongoQuery], JSON_UNESCAPED_UNICODE);

    }




}

function dw_filter_tags_shortcode()
{
    $tax = get_queried_object();

    echo '<div class="dw-product-filter-wrapper" style="padding: 8px 16px" data-term-id="' . $tax->term_id . '">';



    $mongo = new MongoAdapter();
    $mongo->setCollection('tags');

    /*$mongoQuery = ['name' => 'GLOBAL_TAG'];
    $globalAttrs = $mongo->findOne($mongoQuery);

    $glob = [];

    foreach ($globalAttrs as $result) {
        $glob = $result;
    }*/

    $params = new TaxonomyParams($tax);

    $type = implode($params->getParameter('type'));
    $gender = implode($params->getParameter('gender'));

    $colors = $mongo->getDistinct('colorGroup', $gender, $type);

    $textures = $mongo->getDistinct('texture', $gender, $type);

    $lengths = $mongo->getDistinct('lengthGroup', $gender, $type);
    //$lengths = $mongo->getDistinct('dlina', $gender, $type);
    $seasons = $mongo->getDistinct('season', $gender, $type);

    $siluets = $mongo->getDistinct('siluet', $gender, $type);
    $kapushons = $mongo->getDistinct('kapushon', $gender, $type);
    $poyasa = $mongo->getDistinct('poyas', $gender, $type);
    $rukava = $mongo->getDistinct('rukav', $gender, $type);
    $materials = $mongo->getDistinct('material', $gender, $type);
    $zastezhki = $mongo->getDistinct('zastezhka', $gender, $type);
    $podkladki = $mongo->getDistinct('podkladka', $gender, $type);

    $vorotniki = $mongo->getDistinct('vorotnik', $gender, $type);
    $koketki = $mongo->getDistinct('koketka', $gender, $type);
    $karmany= $mongo->getDistinct('karmany', $gender, $type);

    $queryManager = new QueryManager();

    $getParams = $queryManager->getParamsFromGetQuery();
    $queryManager->fromTaxonomyParams($params);

    if (!empty($getParams)) {
        $queryManager->fromGetQuery();
    }

    /*if (empty($getParams)) {
        $queryManager->fromTaxonomyParams($params);
    } else {
        $queryManager->fromGetQuery();
    }*/

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

    $sizeClass = function ($attr, $value) use ($queryManager) {
        $isActive = mb_strpos($queryManager->getQueryParameter($attr), $value) !== false;
        return $isActive ? 'class="dw-filterable dw-size-button dw-filterable-size-active"' : 'class="dw-filterable dw-size-button"';
    };

    echo "<div class='dw-filter-attr-block'>";
    Renderer::header('Цвет');

    foreach (AttributeHelper::DARK_COLORS as $color => $value) {
        if (in_array($color, $colors)) {
            echo '<a style="background: ' . AttributeHelper::COLORMAP[$color] . '"' . $colorClass('colorGroup', $color) . $data('colorGroup', $color) . "title='$color'" . '></a>';
        }
    }

    foreach (AttributeHelper::BRIGHT_COLORS as $color => $value) {
        if (in_array($color, $colors)) {
            echo '<a style="background: ' . AttributeHelper::COLORMAP[$color] . '"' . $colorClass('colorGroup', $color) . $data('colorGroup', $color) . "title='$color'" . '></a>';
        }
    }

    echo "</div>";

    echo "<div class='dw-filter-attr-block'>";
    Renderer::header('Размер');
    foreach (AttributeHelper::SIZES as $size) {
        echo '<a ' . $sizeClass('size', $size) . $data('size', $size) . '>' . $size . '</a>';
    }
    echo "</div>";

    Renderer::attribute('Текстура', 'texture', $textures, $class);


    Renderer::attribute('Длина', 'lengthGroup', $lengths, $class);
    Renderer::attribute('Сезон', 'season', $seasons, $class);

    // Collapsible segment

    echo "<div class='dw-filter-show-more-wrapper'>";
        echo "<span class='dw-filter-show-more'>Показать все параметры</span>";
    echo "</div>";


    echo "<div class='dw-product-filter__collapsible'>";
    Renderer::attribute('Силуэт', 'siluet', $siluets, $class);
    Renderer::attribute('Капюшон', 'kapushon', $kapushons, $class);

    Renderer::attribute('Пояс', 'poyas', $poyasa, $class);
    Renderer::attribute('Рукав', 'rukav', $rukava, $class);
    Renderer::attribute('Материал', 'material', $materials, $class);
    Renderer::attribute('Застежка', 'zastezhka', $zastezhki, $class);
    Renderer::attribute('Подкладка', 'podkladka', $podkladki, $class);

    Renderer::attribute('Воротник', 'vorotnik', $vorotniki, $class);
    Renderer::attribute('Кокетка', 'koketka', $koketki, $class);
    Renderer::attribute('Карманы', 'karmany', $karmany, $class);
    echo '</div>';

    echo '</div>';
}

class AttributeHelper
{
    public const DARK_COLORS = [
        'Черный' => '#1f1f1f',
        'Коричневый' => '#894517',
        'Бордовый' => '#720000',
        'Марсала' => '#952b3b',
        'Фиолетовый' => '#73387c',
        'Синий' => '#1c4e8a',
        'Хаки' => '#565044',
        'Серый' => '#6e6e6e',
    ];

    public const BRIGHT_COLORS = [
        'Зеленый' => '#1d6b3e',
        'Красный' => '#ff2126',
        'Голубой' => '#45d5ff',
        'Желтый' => '#ecc13c',
        'Персиковый' => '#d49600',
        'Бежевый' => '#e9b281',
        'Сиреневый' => '#d6acdb',
        'Розовый' => '#ffb3b9',
        'Белый' => '#eeeeee',
    ];

    public const COLORMAP = [
        'Черный' => '#1f1f1f',
        'Коричневый' => '#894517',
        'Бордовый' => '#720000',
        'Марсала' => '#952b3b',
        'Фиолетовый' => '#73387c',
        'Синий' => '#1c4e8a',
        'Хаки' => '#565044',
        'Красный' => '#ff2126',
        'Розовый' => '#ffb3b9',
        'Желтый' => '#ecc13c',
        'Персиковый' => '#d49600',
        'Бежевый' => '#e9b281',
        'Сиреневый' => '#d6acdb',
        'Голубой' => '#45d5ff',
        'Зеленый' => '#1d6b3e',
        'Серый' => '#6e6e6e',
        'Белый' => '#eeeeee',
    ];

    public const SIZES = ['38', '40', '42', '44', '46', '48', '50', '52'];

}