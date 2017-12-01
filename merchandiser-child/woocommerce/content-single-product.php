<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $custom_product_image_gallery;

switch ($custom_product_image_gallery)
{        
    case "classic":
        include(locate_template('woocommerce/content-single-product-classic.php'));
        break;
    case "half_page":
        include(locate_template('woocommerce/content-single-product-half.php'));
        break;
    default:
        include(locate_template('woocommerce/content-single-product-classic.php'));
        break;
}