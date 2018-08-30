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
use Dreamwhite\Plugins\TagBlock\QueryManager;
use Dreamwhite\Plugins\TagBlock\Renderer;
use Dreamwhite\Plugins\TagBlock\Config;

require_once "includes.php";

dw_tag_block_init();

function dw_tag_block_init()
{
    add_shortcode('dw_tag_block', 'dw_tag_block_shortcode');


    add_action( 'wp_enqueue_scripts', 'dw_tag_block_enqueue_scripts' );
    add_action( 'wp_enqueue_scripts', 'dw_tag_block_enqueue_styles' );

    add_action( 'wp_ajax_nopriv_post_filter_var', 'post_filter_var' );
    add_action( 'wp_ajax_post_filter_var', 'post_filter_var' );

    add_action( 'woocommerce_before_shop_loop', 'dw_tag_block_shortcode', 15 );

}

function dw_tag_block_enqueue_scripts() {
    wp_enqueue_script( 'dw-tag_block', plugins_url( '/js/tag-block.js', __FILE__ ), array('jquery'), '1.0', true );
    wp_localize_script( 'dw-tag_block', 'dwf', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}


function dw_tag_block_enqueue_styles() {
    wp_enqueue_style( 'dw-tag_block-style', plugins_url( '/css/tag-block.css', __FILE__ ) );
}


function dw_tag_block_shortcode()
{
    $current_term = get_queried_object();

    if ($current_term->taxonomy === Config::TAX_NAME) {

        echo '<div style="padding: 8px 16px">';

        $childrenQueryManager = new QueryManager();
        $childrenQueryManager->setQueryParameter('parent', $current_term->name);

        $args = $childrenQueryManager->getQueryArgs();
        $terms = get_terms($args);

        $parent = get_term_meta($current_term->term_id, 'parent', true);
        $topParent = $parent;


        if (empty($terms)) {
            $childrenQueryManager->setQueryParameter('parent', $topParent);
            $args = $childrenQueryManager->getQueryArgs();
            $terms = get_terms($args);
        }

        if  ($parent !== '') {
            $parentTerm = get_term_by( 'name', $parent, Config::TAX_NAME);

            $parents = [];
            $parents[] = $parentTerm;

            while ($parent !== '' && isset($parentTerm)) {
                $parent = get_term_meta($parentTerm->term_id, 'parent', true);
                $parentTerm = get_term_by( 'name', $parent, Config::TAX_NAME);
                $parents[] = $parentTerm;
            }

            foreach (array_reverse($parents) as $parentTerm) {
                if (isset($parentTerm) && $parentTerm->name != '') {
                    $level = get_term_meta($parentTerm->term_id, 'level', true);
                    //echo $level;

                    if ($level > 1) {

                        $short_name = get_term_meta($parentTerm->term_id, 'short_name', true);
                        Renderer::tag_block_parent($short_name, '/catalog/' . $parentTerm->slug . '/');
                    }
                    else {
                        Renderer::tag_block_parent($parentTerm->name, '/catalog/' . $parentTerm->slug . '/');
                    }

                    echo '<span style="font-size: 12px"> / </span>';
                }

            }

            $current_level = get_term_meta($current_term->term_id, 'level', true);
            if ($current_level > 1 ) {
                $short_name = get_term_meta($current_term->term_id, 'short_name', true);
                Renderer::tag_block_parent($short_name, '/catalog/' . $current_term->slug . '/');
            }
            else {
                Renderer::tag_block_parent($current_term->name, '/catalog/' . $current_term->slug . '/');
            }
        }


        echo '<span class="dw-tag-block-expand-button dw-tag-block-expand">Больше меток ▾</span>';

        echo '<div class="dw-tag-block dw-tag-block-collapsed">';
        foreach ($terms as $term) {
            if ($term->term_id !== $current_term->term_id) {
                Renderer::a(get_term_meta($term->term_id, 'short_name', true), get_term_link($term));
            }
        }
        echo '</div>';
        echo '</div>';

    }



}
