<?php
/*
Plugin Name: Поля заказа
Plugin URI:
Description: Изменение полей при оформлении заказа
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: woocommerce-checkout-reorder
Domain Path: /languages
*/

add_filter('woocommerce_checkout_fields','reorder_woo_fields');

function reorder_woo_fields($fields) {
	$fields2['billing']['billing_first_name'] = $fields['billing']['billing_first_name'];
	$fields2['billing']['billing_last_name'] = $fields['billing']['billing_last_name'];
	$fields2['billing']['billing_email'] = $fields['billing']['billing_email'];
	$fields2['billing']['billing_phone'] = $fields['billing']['billing_phone'];
	
	$fields2['billing']['billing_country'] = $fields['billing']['billing_country'];
	$fields2['billing']['billing_postcode'] = array(
		'label'     => __('Postcode', 'woocommerce'),
		'required'  => true,
		'class'     => array('form-row-last'),
		'clear'     => false,
		'priority' => 10
	);
	//$fields2['billing']['billing_postcode'] = $fields['billing']['billing_postcode'];
	$fields2['billing']['billing_city'] = $fields['billing']['billing_city'];
	$fields2['billing']['billing_state'] = $fields['billing']['billing_state'];
	$fields2['billing']['billing_address_1'] = $fields['billing']['billing_address_1'];
	//$fields2['billing']['billing_address_2'] = $fields['billing']['billing_address_2'];
	
	$fields2['shipping']['shipping_first_name'] = $fields['shipping']['shipping_first_name'];
	$fields2['shipping']['shipping_last_name'] = $fields['shipping']['shipping_last_name'];
	$fields2['shipping']['shipping_country'] = $fields['shipping']['shipping_country'];
	$fields2['shipping']['shipping_address_1'] = $fields['shipping']['shipping_address_1'];
	$fields2['shipping']['shipping_address_2'] = $fields['shipping']['shipping_address_2'];
	$fields2['shipping']['shipping_city'] = $fields['shipping']['shipping_city'];
	$fields2['shipping']['shipping_postcode'] = $fields['shipping']['shipping_postcode'];
	$fields2['shipping']['shipping_state'] = $fields['shipping']['shipping_state'];
	
	
	// Add full width Classes and Clears to Adjustments
	$fields2['billing']['billing_email'] = array(
		'label'     => __('Email', 'woocommerce'),
		'required'  => true,
		'class'     => array('form-row-first'),
		'clear'     => false
	);
	$fields2['billing']['billing_phone'] = array(
		'label'     => __('Phone', 'woocommerce'),
		'required'  => false,
		'class'     => array('form-row-last'),
		'clear'     => false
	);
	//$fields2['billing']['billing_country']['class'] = ['form-row-first'];
	

	$fields2['billing']['billing_postcode'] = array(
		'label'     => __('Postcode', 'woocommerce'),
		'required'  => true,
		'class'     => array('form-row-last'),
		'clear'     => false,
		'priority' => 10
	);
	
	return $fields2;
}