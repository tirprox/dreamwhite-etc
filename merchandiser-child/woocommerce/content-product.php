<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global  $product,
        $custom_shop_product_details,
        $custom_shop_second_image,
        $custom_shop_stock_label,
        $custom_catalog_mode,
        $custom_shop_quick_view;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

//woocommerce_before_shop_loop_item
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

//woocommerce_after_shop_loop_item
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

//woocommerce_after_shop_loop_item_title
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

add_action( 'woocommerce_after_shop_loop_item_title_loop_price', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title_loop_rating', 'woocommerce_template_loop_rating', 5 );

//woocommerce_before_shop_loop_item_title
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

?>

<li class="product">
   
   <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>	

    <?php
        $attachment_ids = $product->get_gallery_image_ids();
        if ( $attachment_ids ) {
            $loop = 0;
            foreach ( $attachment_ids as $attachment_id ) {
                $image_link = wp_get_attachment_url( $attachment_id );
                if (!$image_link) continue;
                $loop++;
                $product_thumbnail_second = wp_get_attachment_image_src($attachment_id, 'shop_catalog');
                if ($loop == 1) break;
            }
        }
    ?>

    <?php
    $style = '';
    $class = '';        
    if (isset($product_thumbnail_second[0])) {            
        $style = 'background-image:url(' . $product_thumbnail_second[0] . ')';
        $class = 'with_second_image';     
    }

    if ( $custom_shop_second_image == "0" ) {
        $style = '';
        $class = '';
    }
    ?>

    <div class="product_thumbnail <?php echo ent2ncr($class); ?>">
        <a href="<?php the_permalink(); ?>">
            <?php if ( $custom_shop_second_image == "1" ) : ?>
            <span class="product_thumbnail_secondary" style="<?php echo ent2ncr($style); ?>"></span>
            <?php endif; ?>
            <?php
                $id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
                if ( has_post_thumbnail( $id ) ) { 	
                    echo  get_the_post_thumbnail( $id, 'shop_catalog');
                } else {
                    echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $id );
                }
            ?>
        </a>

        <?php if (!($custom_catalog_mode) || $custom_shop_quick_view ) : ?>
        <div class="shop_product_buttons_wrapper">
            <div class="shop_product_buttons">                
                <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( GETBOWTIED_WISHLIST_IS_ACTIVE ) : ?>
            <div class="shop_archive_wishlist">
                <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
            </div>
        <?php endif; ?>

    </div><!--.product_thumbnail-->

    <?php if ( $custom_shop_product_details == '1' ) : ?>

        <div class="shop_product_metas">

            <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>

            <h3><a class="shop_product_title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

            <?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>

            <?php do_action( 'woocommerce_after_shop_loop_item_title_loop_rating' ); ?>

            <div class="shop-product-badges">
                <?php wc_get_template( 'loop/sale-flash.php' ); ?>

                <?php if ( !$product->is_in_stock() && !empty($custom_shop_stock_label) ) : ?>            
                    <div class="out_of_stock"><?php esc_html_e( $custom_shop_stock_label, 'getbowtied' ); ?></div>            
                <?php endif; ?>
            </div>

            <div class="shop_product_price">
                <?php do_action( 'woocommerce_after_shop_loop_item_title_loop_price' ); ?>
            </div>

        </div>   

    <?php endif; ?>
	
</li>