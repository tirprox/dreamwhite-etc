<?php
/*
Plugin Name: DreamWhite Блок Тэгов в верху страницы
Plugin URI:
Description: Добавляет метки для дальнейшей навигации
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: dw-tag-block
Domain Path: /languages
*/

require_once(dirname(__DIR__) . "/dw-common/vendor/autoload.php");

use MongoDB\Client;

use Dreamwhite\Plugins\TagBlock\Renderer;
use Dreamwhite\Plugins\TagBlock\Config;

require_once "includes.php";

dw_tag_block_init();

function dw_tag_block_init()
{
    add_shortcode('dw_tag_block', 'dw_tag_block_shortcode');


    add_action('wp_enqueue_scripts', 'dw_tag_block_enqueue_scripts');
    add_action('wp_enqueue_scripts', 'dw_tag_block_enqueue_styles');

    add_action('wp_ajax_nopriv_post_filter_var', 'post_filter_var');
    add_action('wp_ajax_post_filter_var', 'post_filter_var');

    add_action('woocommerce_before_shop_loop', 'dw_tag_block_shortcode', 15);

}

function dw_tag_block_enqueue_scripts()
{
    wp_enqueue_script('dw-tag_block', plugins_url('/js/tag-block.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('dw-tag_block', 'dwf', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}


function dw_tag_block_enqueue_styles()
{
    wp_enqueue_style('dw-tag_block-style', plugins_url('/css/tag-block.css', __FILE__));
}


function dw_tag_block_shortcode()
{
    $current_term = get_queried_object();


    if ($current_term->taxonomy === Config::TAX_NAME) {


        $LOGIN = 'admin';
        $PASSWORD = '6h8s4ksoq';
        $URI = 'mongodb://@localhost:27017';

        $client = new Client($URI, [
            "username" => $LOGIN,
            "password" => $PASSWORD
        ]);

        $db = $client->selectDatabase('tags');
        $collection = $db->selectCollection('tags');

        $mongo = new TagBlockMongoHelper($collection);

        $term = $collection->findOne([
            'name' => $current_term->name
        ]);

        $parent = $collection->findOne([
            'name' => $term->relations->parent
        ]);


        $childrenCount = $collection->countDocuments(
            [
                'relations.parent' => $term->name,
                'relations.filterable' => 0,
                'relations.hasRecords' => 1,
            ]
        );
        if ($childrenCount > 0) {
            $children = $mongo->getChildren($term->name);
        }
        else {
            $childrenCount = $collection->countDocuments(
                [
                    'relations.parent' => $term->relations->parent,
                    'relations.filterable' => 0,
                    'relations.hasRecords' => 1,
                ]
            );
            $children = $mongo->getChildren($term->relations->parent);
        }

        $parents = [];
        $parents[] = $parent;

        while ($parent->relations->level >= 1) {
            $parent = $collection->findOne([
                'name' => $parent->relations->parent
            ]);
            $parents[] = $parent;
        }

        echo '<div style="padding: 8px 16px">';

        echo '<ul class="dw-breadcrumb-list" itemscope itemtype="http://schema.org/BreadcrumbList">';
        $position = 1;
        foreach (array_reverse($parents) as $parentTerm) {
                    $level = $parentTerm->relations->level;

                    if ($level > 1) {

                        $short_name = $parentTerm->seo->short_name;
                        Renderer::tag_block_parent($short_name, '/catalog/' . $parentTerm->slug . '/', $position);
                    } else {
                        if ($level > 0) {
                            Renderer::tag_block_parent($parentTerm->name, '/catalog/' . $parentTerm->slug . '/', $position);

                        } else {
                            Renderer::tag_block_parent('Главная', '/', $position);

                        }
                    }

                    $position++;
                    echo '<span style="font-size: 12px"> / </span>';
        }

        $current_level = $term->relations->level;
        if ($current_level > 1) {
            $short_name = $term->seo->short_name;
            Renderer::tag_block_parent($short_name, '/catalog/' . $current_term->slug . '/', $position);
        } else {
            Renderer::tag_block_parent($current_term->name, '/catalog/' . $current_term->slug . '/', $position);
        }
        echo '</ul>';


        if ($childrenCount > 0) {
            echo '<span class="dw-tag-block-expand-button dw-tag-block-expand">Больше меток ▾</span>';

            echo '<div class="dw-tag-block">';

            foreach ($children as $item) {

                if ($item->slug !== $term->slug) {
                    Renderer::a($item->seo->short_name, TagLinkHelper::a($item));

                }
            }
            echo '</div>';

        }



        echo '</div>';

    }


}

class TagBlockMongoHelper
{
    private $collection;

    function __construct($collection)
    {
        $this->collection = $collection;
    }

    function getChildren($name)
    {
        return $this->collection->find(
            [
                'relations.parent' => $name,
                'relations.hasRecords' => 1,
                'relations.filterable' => 0
            ]
        );
    }
}

class TagLinkHelper
{
    public static function a($item)
    {
        return "/catalog/$item->slug/";
    }
}
