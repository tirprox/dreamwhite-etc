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

init();

function init()
{
    add_action('init', 'dw_custom_seo_taxonomy', 0);
    add_shortcode('custom_filter', 'dw_filter_tags_shortcode');
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
    register_taxonomy('attr', 'product', $args);

}

function dw_filter_tags_shortcode()
{

    echo '<div style="padding: 8px 16px">';

    $tax = get_queried_object();

    $params = new TaxonomyParams($tax);

    $queryManager = new QueryManager();
    $queryManager->fromTaxonomyParams($params);

    $term_id = $tax->term_id;


    $args = $queryManager->getQueryArgs();



    Renderer::header('Matching Taxonomies:');

    $terms = get_terms($args);
    foreach ($terms as $term) {
        Renderer::a($term->name, get_term_link($term));
    }


    /*if (isset($terms) && $terms[0]->term_id !== $term_id) {
        wp_redirect(get_term_link($terms[0]->term_id));
    }*/

    echo '</div>';
}


class QueryManager
{

    private $queryParams = [];

    public function setQueryParameter($name, $value)
    {
        if($value !== '') {
            $this->queryParams[$name] = $value;
            set_query_var($name, $value);
        }

    }

    public function getQueryParameter($name)
    {
        return get_query_var($name);
    }

    public function deleteQueryParameter($name)
    {
        unset($this->queryParams[$name]);
        set_query_var($name, '');
    }


    // Accepts TaxonomyParams as an argument
    public function fromTaxonomyParams($taxonomy)
    {
        foreach ($taxonomy->getParams() as $name => $value) {
            $this->setQueryParameter($name, implode(',', $value));
        }
    }

    public function getQueryArgs()
    {
        $metaQuery = [];

        foreach ($this->queryParams as $name => $value) {
            $metaQuery[] = [
                'key' => $name,
                'value' => $value,
                'compare' => '='
            ];
        }

        $args = [
            'taxonomy' => 'attr',
            'hide_empty' => false,
            'meta_query' => $metaQuery
        ];


        return $args;
    }
}

class TagRouter
{

}

class TaxonomyParams
{
    private $params = [];

    public function getParams()
    {
        return $this->params;
    }

    public function __construct($taxonomy)
    {
        $meta = get_term_meta($taxonomy->term_id);

        foreach ($meta as $param => $value) {
            $this->params[$param] = $value[0] !== '' ? explode(',', $value[0]) : [];
//            echo $param . ": " . implode(',', $this->params[$param]) . '<br>';
        }

        foreach ($this->params as $param => $value) {
            if (!empty($value)) {
                Renderer::header($param);
                foreach ($value as $item) {
                    Renderer::tag($item);
                }
            }
        }
    }

}

class Renderer
{

    public static function tag($content)
    {
        echo "<div style='font-size: 12px; display: inline-block; padding: 0px 4px; margin: 2px;border: 1px solid #cccccc'>$content</div>";
    }

    public static function header($content)
    {
        echo "<h3 style=''>$content</h3>";
    }

    public static function p($content)
    {
        echo "<p style='font-size: 12px; margin-bottom: 4px'>$content</p>";
    }

    public static function a($content, $href)
    {
        echo "<a style='font-size: 12px; margin-bottom: 0px; display: block;' href='$href'>$content</a>";
    }

}