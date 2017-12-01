<?php
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global  $post,
        $product,
        $custom_product_image_gallery,
        $custom_product_image_zoom,
        $custom_shop_stock_label,
        $custom_related_products;

?>

<?php

    //woocommerce_before_single_product_summary
    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

    add_action( 'woocommerce_before_single_product_summary_sale_flash', 'woocommerce_show_product_sale_flash', 10 );
    add_action( 'woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_images', 20 );

    //woocommerce_single_product_summary
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

    add_action( 'woocommerce_single_product_summary_single_title', 'woocommerce_template_single_title', 5 );
    add_action( 'woocommerce_single_product_summary_single_rating', 'woocommerce_template_single_rating', 10 );
    add_action( 'woocommerce_single_product_summary_single_price', 'woocommerce_template_single_price', 10 );
    add_action( 'woocommerce_single_product_summary_single_excerpt', 'woocommerce_template_single_excerpt', 20 );
    add_action( 'woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30 );
    add_action( 'woocommerce_single_product_summary_single_meta', 'woocommerce_template_single_meta', 40 );
    add_action( 'woocommerce_single_product_summary_single_sharing', 'woocommerce_template_single_sharing', 50 );

    //woocommerce_after_single_product_summary
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    add_action( 'woocommerce_after_single_product_summary_data_tabs', 'woocommerce_output_product_data_tabs', 10 );

    //custom actions
    add_action( 'woocommerce_before_main_content_breadcrumb', 'woocommerce_breadcrumb', 20, 0 );
    add_action( 'woocommerce_product_summary_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="product_content_wrapper layout_classic">

        <div class="row">
            <div class="large-12 columns">
                <?php do_action( 'woocommerce_before_single_product' ); ?>
            </div>
        </div>

        <div class="row_split">

            <div class="row_split_inside">

                <div class="first_col_split">

                    <div class="product-thumbnails-vertical-wrapper">
                        <?php do_action( 'woocommerce_product_summary_thumbnails' ); ?>
                    </div>

                    <div class="product-images-wrapper <?php if ($custom_product_image_zoom == 1) echo 'zoom_enabled'; ?>">
                        <?php do_action( 'woocommerce_before_single_product_summary_product_images' ); ?>                    
                    </div>

                    <div class="product-thumbnails-horizontal-wrapper">
                        <?php do_action( 'woocommerce_product_summary_thumbnails' ); ?>
                    </div>
                    
                </div><!-- .columns -->

                <div class="second_col_split">

                    <div class="product_infos">

                        <?php if (is_product()): do_action('woocommerce_before_main_content_breadcrumb'); endif;?>

                        <?php
                        do_action( 'woocommerce_before_single_product_summary' );

                        do_action( 'woocommerce_single_product_summary_single_title' );

                        if ( post_password_required() ) {
                            echo get_the_password_form();
                            return;
                        }                        
                        ?>

                        <div class="after_title_wrapper">

                            <div class="product_sale_badge">
                                <?php do_action( 'woocommerce_before_single_product_summary_sale_flash' ); ?>
                            </div>

                            <div class="product_price">
                                <?php do_action( 'woocommerce_single_product_summary_single_price' ); ?>
                            </div>

                            <div class="product_ratings">
                                <?php do_action( 'woocommerce_single_product_summary_single_rating' ); ?>
                            </div>

                        </div>

                        <div class="product_excerpt">
                            <?php do_action( 'woocommerce_single_product_summary_single_excerpt' ); ?>

                            <?php if ( !$product->is_in_stock() && !empty($custom_shop_stock_label)) : ?>            
                                <div class="out_of_stock"><?php esc_html_e( $custom_shop_stock_label, 'getbowtied' ); ?></div>            
                            <?php endif; ?>
                        </div>

                        <div class="product_add_to_cart_button">
                            <?php do_action( 'woocommerce_single_product_summary_single_add_to_cart' ); ?>
                        </div>

                        <?php do_action( 'woocommerce_single_product_summary' ); ?>

                        <div class="after_single_product_summary">
                            
                            <div class="product_meta_wrapper">
                                <?php do_action( 'woocommerce_single_product_summary_single_meta' ); ?>
                            </div>

                            <div class="single_product_share_wrapper">
                                <?php do_action( 'woocommerce_single_product_summary_single_sharing' ); ?>
                            </div>

                        </div>

                        <?php
                        do_action( 'woocommerce_after_single_product_summary' );
                        do_action( 'woocommerce_after_single_product' );
                        ?>

                        <meta itemprop="url" content="<?php the_permalink(); ?>" />

                    </div>

                </div><!-- .columns -->

            </div>

        </div><!-- .row -->

        <div class="woocommerce_tabs_wrapper">
            <div class="row">
                <div class="large-12 large-centered columns">                    
                    <?php do_action( 'woocommerce_after_single_product_summary_data_tabs' ); ?>                    
                </div>
            </div>
        </div>
        
    </div><!-- .product_content_wrapper -->

</div><!-- #product-<?php the_ID(); ?> -->  

<?php if (!is_product() && $custom_related_products): woocommerce_output_related_products_merch_shortcode(); endif; // OUTPUT RELATED PRODUCTS FOR PRODUCT_PAGE SHORTCODE?>