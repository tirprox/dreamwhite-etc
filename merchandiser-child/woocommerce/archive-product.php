<?php
/**
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global  $shop_display_type,
        $custom_shop_layout_style,
        $custom_shop_sidebar,
        $custom_accent_color,
        $woocommerce_loop,
        $custom_shop_category_height;


//woocommerce_before_shop_loop
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

add_action( 'woocommerce_before_shop_loop_result_count', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop_catalog_ordering', 'woocommerce_catalog_ordering', 30 );

//woocommerce_before_main_content
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );

$shop_page_header_src       = "";
$category_header_src        = "";

$listing_header_src         = "";
//$listing_header_padding   = "";
$listing_header_styles      = "";
$listing_page_title_class   = "";

if ( is_shop() ) {
        
    $shop_page_id = get_option( 'woocommerce_shop_page_id' );
    
    if ( has_post_thumbnail( $shop_page_id ) ) {
        $listing_header_src = wp_get_attachment_url( get_post_thumbnail_id( $shop_page_id ), 'full' );
    }

    if (get_post_meta( $shop_page_id, 'page_title_meta_box_check', true )) {
        $page_title_option = get_post_meta( $shop_page_id, 'page_title_meta_box_check', true );
    } else {
        $page_title_option = "on";
    }

} else {

    if (function_exists('getbowtied_woocommerce_get_header_image_url')) {       
        $listing_header_src = getbowtied_woocommerce_get_header_image_url();
    }

}

$cat_desc = category_description();

$shop_page_title_wrapper_class    = "";
$shop_page_header_class = "";

get_header( 'shop' ); 

if ( ! empty( $listing_header_src ) ) {
        
    $listing_header_padding           = $custom_shop_category_height? 'padding:'.$custom_shop_category_height.'px 0 '.($custom_shop_category_height+50).'px;' : '';
    $listing_header_styles            = 'style="background-image:url(' . esc_url($listing_header_src) . ');'.$listing_header_padding.'"';
    $listing_page_title_class         = "transparent";
    $shop_page_title_wrapper_class    = "pulldown";
    $shop_page_header_class           = "with-padding";

    if ( is_shop() ) {

        $shop_page_title_wrapper_class    .= " pulldown-height";
    }

}

if ( ! empty( $cat_desc ) && empty( $listing_header_src) )
{
    $listing_header_padding           = $custom_shop_category_height? 'padding:'.$custom_shop_category_height.'px 0 '.($custom_shop_category_height+50).'px;' : '';
    $listing_header_styles            = 'style=" background-color:'.$custom_accent_color. ';'.$listing_header_padding.'"';
    $shop_page_title_wrapper_class    = "pulldown";
    $shop_page_header_class           = "with-padding";
}


$term          = get_queried_object();
$parent_id      = empty( $term->term_id ) ? 0 : $term->term_id;

$shop_display_type = "products";

if ( is_shop() && (get_option('woocommerce_shop_page_display') == '') )                                             $shop_display_type = "products";
if ( is_shop() && (get_option('woocommerce_shop_page_display') == 'products') )                                     $shop_display_type = "products";
if ( is_shop() && (get_option('woocommerce_shop_page_display') == 'subcategories') )                                $shop_display_type = "subcategories";
if ( is_shop() && (get_option('woocommerce_shop_page_display') == 'both') )                                         $shop_display_type = "both";

if ( is_product_category() && (get_option('woocommerce_category_archive_display') == '') )                          $shop_display_type = "products";
if ( is_product_category() && (get_option('woocommerce_category_archive_display') == 'products') )                  $shop_display_type = "products";
if ( is_product_category() && (get_option('woocommerce_category_archive_display') == 'subcategories') )             $shop_display_type = "subcategories";
if ( is_product_category() && (get_option('woocommerce_category_archive_display') == 'both') )                      $shop_display_type = "both";

if ( is_product_category() && (get_woocommerce_term_meta($parent_id, 'display_type', true) == 'products') )         $shop_display_type = "products";
if ( is_product_category() && (get_woocommerce_term_meta($parent_id, 'display_type', true) == 'subcategories' ) )   $shop_display_type = "subcategories";
if ( is_product_category() && (get_woocommerce_term_meta($parent_id, 'display_type', true) == 'both') )             $shop_display_type = "both";

if ( isset($_GET["s"]) && $_GET["s"] != '' )                                                                        $shop_display_type = "products";

?>

    <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

        <div class="shop-page-header <?php echo $shop_page_header_class; ?>" <?php echo ent2ncr($listing_header_styles); ?>>
            
        
            
            <div class="shop-page-category-description <?php if (empty($shop_page_header_class) && empty($cat_desc) ) echo 'no-padding'; ?>">
                <?php if (!empty($cat_desc)): ?>
                    <?php echo ent2ncr($cat_desc); ?>
                <?php endif; ?>       
            </div>
            
            <div class="shop-page-title-wrapper <?php echo ent2ncr($shop_page_title_wrapper_class); ?>">

                <?php if ( is_active_sidebar( 'catalog-widget-area' ) ) : ?>
                    <div class="mobile-sidebar-toggle">
                    </div>
                <?php endif; ?>

                <?php if ( !is_shop() || "on" == $page_title_option ) : ?>
                
                    <h1 class="shop-page-title entry-title page-title <?php echo ent2ncr($listing_page_title_class); ?>"><?php woocommerce_page_title(); ?></h1>

                <?php endif; ?>

                <?php do_action( 'woocommerce_before_shop_loop' ); ?>

                <?php if ( ( ( is_shop() && ! empty( $listing_header_src ) ) || ( is_shop() && "on" == $page_title_option && empty( $listing_header_src ) ) || !is_shop() ) && ($shop_display_type == 'products' || $shop_display_type == 'both') ) : ?>

                <ul class="shop-tools">                                    

                    <li>
                        <div class="shop-tools-button">
                            <div class="shop-sort-wrapper shop-tools-icon">
                                <div id="shop-catalog-ordering" class="shop-catalog-ordering">
                                    <?php do_action( 'woocommerce_before_shop_loop_catalog_ordering' ); ?>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li id="shop-display-options">
                        <div class="shop-tools-button">
                            <div class="change_layout shop-tools-icon">                                  
                                <div class="nl-field nl-dd">
                                    <a class="nl-field-toggle"></a>
                                    <ul>
                                        <li data-newval="2" data-bg="<?php echo get_template_directory_uri() . '/images/shop-display-2.svg'; ?>"><?php echo getbowtied_get_local_file_contents(get_template_directory() . '/images/shop-display-2.svg'); ?></li>                                            
                                        <li data-newval="3" data-bg="<?php echo get_template_directory_uri() . '/images/shop-display-3.svg'; ?>"><?php echo getbowtied_get_local_file_contents(get_template_directory() . '/images/shop-display-3.svg'); ?></li>
                                        <li data-newval="4" data-bg="<?php echo get_template_directory_uri() . '/images/shop-display-4.svg'; ?>"><?php echo getbowtied_get_local_file_contents(get_template_directory() . '/images/shop-display-4.svg'); ?></li>
                                        <li data-newval="5" data-bg="<?php echo get_template_directory_uri() . '/images/shop-display-5.svg'; ?>"><?php echo getbowtied_get_local_file_contents(get_template_directory() . '/images/shop-display-5.svg'); ?></li>
                                        <li data-newval="6" data-bg="<?php echo get_template_directory_uri() . '/images/shop-display-6.svg'; ?>"><?php echo getbowtied_get_local_file_contents(get_template_directory() . '/images/shop-display-6.svg'); ?></li>
                                    </ul>
                                </div>
                                <div class="nl-overlay"></div>
                            </div>
                        </div>
                    </li>

                </ul>

                <?php endif; ?>

            </div>      

        </div>

    <?php endif; ?>

    <div class="row">
        
        <?php if ( $custom_shop_sidebar == '1' ) : ?>
        
        <div class="shop-listing-with-sidebar large-9 large-push-3 columns">
        
        <?php else : ?>
       
        <div class="shop-listing-no-sidebar large-12 columns">
        
        <?php endif; ?>

            <?php do_action( 'woocommerce_before_main_content' ); ?>

            <?php //do_action( 'woocommerce_archive_description' ); ?>

            <?php if ( have_posts() ) : ?>

                <?php if ( $shop_display_type == "subcategories" ) : ?>
                <ul class="shop_categories_with_thumb">
                    <?php woocommerce_output_product_categories( array( 'parent_id' => is_product_category() ? get_queried_object_id() : 0, ) ); ?>
                <?php elseif ( $shop_display_type == "both" ) : ?>
                <ul class="shop_categories_list">
                    <?php woocommerce_output_product_categories( array( 'parent_id' => is_product_category() ? get_queried_object_id() : 0, ) ); ?>
                <?php endif; ?>
                </ul>

                <?php if ( $shop_display_type == "both" || $shop_display_type == "products" ) : ?>

                    <?php if ( $custom_shop_layout_style == "regular" ) : ?>
                    
                        <?php woocommerce_product_loop_start(); ?>
                    
                    <?php else : ?>
                    
                        <ul id="masonry_grid" class="products masonry_columns_<?php echo (isset($woocommerce_loop['columns']) && $woocommerce_loop['columns'] != "" ) ? $woocommerce_loop['columns'] : '6'; ?>" data-columns>

                    <?php endif; ?>

                            <?php while ( have_posts() ) : the_post(); ?>

                                <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; // end of the loop. ?>

                    <?php if ( $custom_shop_layout_style == "regular" ) : ?>
                    
                        <?php woocommerce_product_loop_end(); ?>
                    
                    <?php else : ?>
                    
                        </ul>

                    <?php endif; ?>

                    <?php do_action( 'woocommerce_after_shop_loop' ); ?>

                <?php endif; ?>

            <?php else : ?>

                <?php wc_get_template( 'loop/no-products-found.php' ); ?>

            <?php endif; ?>

            <?php do_action( 'woocommerce_after_main_content' ); ?>

        </div>

        <?php if ( $custom_shop_sidebar == '1' ) : ?>
        
            <div class="shop-sidebar large-3 large-pull-9 columns">
                <?php if ( is_active_sidebar( 'catalog-widget-area' ) ) : ?>
                    <div id="secondary" class="widget-area" role="complementary">
                        <?php dynamic_sidebar( 'catalog-widget-area' ); ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>

<?php get_footer( 'shop' ); ?>