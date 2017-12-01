<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	global $custom_related_products;

	//woocommerce_before_main_content
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

	//woocommerce_after_single_product_summary
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	add_action( 'woocommerce_after_single_product_summary_upsell_display', 'getbowtied_output_upsells', 15 );
	add_action( 'woocommerce_after_single_product_summary_related_products', 'getbowtied_output_related', 20 );

?>

<?php get_header('shop'); ?>

	<?php do_action('woocommerce_before_main_content'); ?>

	<?php while ( have_posts() ) : the_post(); ?>
	    <?php wc_get_template_part( 'content', 'single-product' ); ?>
	<?php endwhile; // end of the loop. ?>

	<?php
		do_action('woocommerce_after_single_product_summary_upsell_display'); 
	?>

	<?php
		if ($custom_related_products == 1) :
			do_action('woocommerce_after_single_product_summary_related_products');
		endif; 
	?>

	<?php do_action('woocommerce_after_main_content'); ?>

<?php get_footer('shop'); ?>