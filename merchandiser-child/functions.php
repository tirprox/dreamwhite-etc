<?php

/**
 * Loads the child theme textdomain.
 */
function merchandiser_child_theme_setup() {
    load_child_theme_textdomain( 'merchandiser', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'merchandiser_child_theme_setup' );


register_nav_menu( 'mobile', __( 'Mobile Menu', 'theme-slug' ) );
//require_once("inc/shortcodes/wp/socials.php");
//include_once('inc/shortcodes/wp/socials.php');


/**
 * This code shows pagination for WooCommerce shortcodes when it's embeded on single pages.
 * Include into functions.php.
 */
if ( ! is_admin() ) {
// ---------------------- FRONTPAGE -------------------
    if ( defined('WC_VERSION') ) {
// ---------------------- WooCommerce active -------------------

//        /**
//         * Set Pagination for shortcodes custom loop on single-pages.
//         * @uses $woocommerce_loop;
//         */
//        add_action( 'pre_get_posts', 'kli_wc_pre_get_posts_query' );
//        function kli_wc_pre_get_posts_query( $query ) {
//            global $woocommerce_loop;
//
//            // Get paged from main query only
//            // ! frontpage missing the post_type
//            if ( is_main_query() && ( $query->query['post_type'] == 'product' ) || ! isset( $query->query['post_type'] ) ){
//
//                if ( isset($query->query['paged']) ){
//                    $woocommerce_loop['paged'] = $query->query['paged'];
//                }
//            }
//
//            if ( ! $query->is_post_type_archive || $query->query['post_type'] !== 'product' ){
//                return;
//            }
//
//            $query->is_paged = true;
//            $query->query['paged'] = $woocommerce_loop['paged'];
//            $query->query_vars['paged'] = $woocommerce_loop['paged'];
//        }
//
//        /** Prepare Pagination data for shortcodes on pages
//         * @uses $woocommerce_loop;
//         */
//        add_action( 'loop_end', 'kli_query_loop_end' );
//
//        function kli_query_loop_end( $query ) {
//
//            if ( ! $query->is_post_type_archive || $query->query['post_type'] !== 'product' ){
//                return;
//            }
//
//            // Cache data for pagination
//            global $woocommerce_loop;
//            $woocommerce_loop['pagination']['paged'] = $woocommerce_loop['paged'];
//            $woocommerce_loop['pagination']['found_posts'] = $query->found_posts;
//            $woocommerce_loop['pagination']['max_num_pages'] = $query->max_num_pages;
//            $woocommerce_loop['pagination']['post_count'] = $query->post_count;
//            $woocommerce_loop['pagination']['current_post'] = $query->current_post;
//        }
//        /**
//         * Pagination for shortcodes on single-pages
//         * @uses $woocommerce_loop;
//         */
//        add_action( 'woocommerce_after_template_part', 'kli_wc_shortcode_pagination' );
//        function kli_wc_shortcode_pagination( $template_name ) {
//            if ( ! ( $template_name === 'loop/loop-end.php' && is_page() ) ){
//                return;
//            }
//            global $wp_query, $woocommerce_loop;
//            if ( ! isset( $woocommerce_loop['pagination'] ) ){
//                return;
//            }
//            $wp_query->query_vars['paged'] = $woocommerce_loop['pagination']['paged'];
//            $wp_query->query['paged'] = $woocommerce_loop['pagination']['paged'];
//            $wp_query->max_num_pages = $woocommerce_loop['pagination']['max_num_pages'];
//            $wp_query->found_posts = $woocommerce_loop['pagination']['found_posts'];
//            $wp_query->post_count = $woocommerce_loop['pagination']['post_count'];
//            $wp_query->current_post = $woocommerce_loop['pagination']['current_post'];
//
//            // Custom pagination function or default woocommerce_pagination()
//            kli_woocommerce_pagination();
//        }
//
//
//        /**
//         * Custom pagination for WooCommerce instead the default woocommerce_pagination()
//         * @uses plugin Prime Strategy Page Navi, but added is_singular() on #line16
//         */
//        remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
//        add_action( 'woocommerce_after_shop_loop', 'kli_woocommerce_pagination', 10);
//        function kli_woocommerce_pagination() {
//            woocommerce_pagination();
//            echo("<p></p>");
//        }
//
//        add_action( 'wp_enqueue_scripts', 'child_theme_scripts', 20 );
//        function child_theme_scripts(){
//            wp_enqueue_script( 'yandex-target', '/metrika/yandex-target.js', array( 'jquery' ), false, true );
//            wp_enqueue_script( 'custom-js', '/js/custom-js.js', array( 'jquery' ), false, true );
//        }


    }// END WOOCOMMERCE
}// END FRONTPAGE
