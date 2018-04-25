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

add_filter( 'woocommerce_checkout_fields', 'reorder_woo_fields' );
add_filter( 'woocommerce_default_address_fields', 'reorder_default_address_fields' );

add_action('woocommerce_after_order_notes', 'agree_to_terms', 9);

function agree_to_terms() {
	?>
	<p class="form-row terms">
		<input type="checkbox" class="input-checkbox" name="agreed" id="agreed" />
		<label for="deliverycheck" class="checkbox">Я согласен(а) с политикой конфиденциальности и даю согласие на обработку моих персональных данных.</label>
	</p>
	<?php
}

add_action('woocommerce_checkout_process', 'not_agreed_to_terms');
function not_agreed_to_terms() {
	if ( ! $_POST['agreed'] )
		wc_add_notice( __( 'Для оформления заказа необходимо согласиться на обработку персональных данных' ), 'error' );
}


function reorder_default_address_fields( $fields ) {
	$fields[ 'address_1' ][ 'priority' ]    = 90;
	$fields[ 'address_1' ][ 'placeholder' ] = "Адрес: улица, номер дома, квартира и пр.";
	$fields[ 'postcode' ][ 'priority' ]     = 60;
	$fields[ 'postcode' ][ 'clear' ]        = true;
	
	return $fields;
}

function reorder_woo_fields( $fields ) {
	
	$fields2[ 'billing' ][ 'billing_first_name' ] = $fields[ 'billing' ][ 'billing_first_name' ];
	$fields2[ 'billing' ][ 'billing_last_name' ]  = $fields[ 'billing' ][ 'billing_last_name' ];
	$fields2[ 'billing' ][ 'billing_email' ]      = $fields[ 'billing' ][ 'billing_email' ];
	$fields2[ 'billing' ][ 'billing_phone' ]      = $fields[ 'billing' ][ 'billing_phone' ];
	
	$fields2[ 'billing' ][ 'billing_country' ]  = $fields[ 'billing' ][ 'billing_country' ];
	$fields2[ 'billing' ][ 'billing_postcode' ] = $fields[ 'billing' ][ 'billing_postcode' ];
	//$fields2['billing']['billing_postcode'] = $fields['billing']['billing_postcode'];
	$fields2[ 'billing' ][ 'billing_city' ]      = $fields[ 'billing' ][ 'billing_city' ];
	$fields2[ 'billing' ][ 'billing_state' ]     = $fields[ 'billing' ][ 'billing_state' ];
	$fields2[ 'billing' ][ 'billing_address_1' ] = $fields[ 'billing' ][ 'billing_address_1' ];
	//$fields2['billing']['billing_address_2'] = $fields['billing']['billing_address_2'];
	
	$fields2[ 'shipping' ][ 'shipping_first_name' ] = $fields[ 'shipping' ][ 'shipping_first_name' ];
	$fields2[ 'shipping' ][ 'shipping_last_name' ]  = $fields[ 'shipping' ][ 'shipping_last_name' ];
	$fields2[ 'shipping' ][ 'shipping_country' ]    = $fields[ 'shipping' ][ 'shipping_country' ];
	$fields2[ 'shipping' ][ 'shipping_address_1' ]  = $fields[ 'shipping' ][ 'shipping_address_1' ];
	$fields2[ 'shipping' ][ 'shipping_address_2' ]  = $fields[ 'shipping' ][ 'shipping_address_2' ];
	$fields2[ 'shipping' ][ 'shipping_city' ]       = $fields[ 'shipping' ][ 'shipping_city' ];
	$fields2[ 'shipping' ][ 'shipping_postcode' ]   = $fields[ 'shipping' ][ 'shipping_postcode' ];
	$fields2[ 'shipping' ][ 'shipping_state' ]      = $fields[ 'shipping' ][ 'shipping_state' ];
	
	// Add full width Classes and Clears to Adjustments
	$fields2[ 'billing' ][ 'billing_email' ]              = [
		'label'    => __( 'Email', 'woocommerce' ),
		'required' => true,
		'class'    => [ 'form-row-last' ],
		'clear'    => true
	];
	$fields2[ 'billing' ][ 'billing_phone' ]              = [
		'label'    => __( 'Phone', 'woocommerce' ),
		'required' => false,
		'class'    => [ 'form-row-first' ],
		'clear'    => true
	];
	$fields2[ 'billing' ][ 'billing_country' ][ 'class' ] = [ 'form-row-first' ];
	$fields2[ 'billing' ][ 'billing_country' ][ 'clear' ] = true;
	
	$fields2[ 'billing' ][ 'billing_postcode' ][ 'priority' ] = 60;
	$fields2[ 'billing' ][ 'billing_postcode' ][ 'class' ]    = [ 'form-row-last' ];
	$fields2[ 'billing' ][ 'billing_postcode' ][ 'clear' ]    = true;
	
	$fields2[ 'billing' ][ 'billing_city' ][ 'class' ]  = [ 'form-row-first' ];
	$fields2[ 'billing' ][ 'billing_state' ][ 'class' ] = [ 'form-row-last' ];
	
	$fields2[ 'billing' ][ 'billing_city' ][ 'clear' ]  = true;
	$fields2[ 'billing' ][ 'billing_state' ][ 'clear' ] = true;
	
	$fields2[ 'billing' ][ 'billing_address_1' ][ 'priority' ] = 90;
	$fields2[ 'billing' ][ 'billing_address_1' ][ 'clear' ]    = true;
	
	$fields2[ 'billing' ][ 'billing_first_name' ][ 'placeholder' ] = "Имя";
	$fields2[ 'billing' ][ 'billing_last_name' ][ 'placeholder' ]  = "Фамилия";
	$fields2[ 'billing' ][ 'billing_email' ][ 'placeholder' ]      = "Email";
	$fields2[ 'billing' ][ 'billing_phone' ][ 'placeholder' ]      = "Телефон";
	$fields2[ 'billing' ][ 'billing_country' ][ 'placeholder' ]    = "Страна";
	$fields2[ 'billing' ][ 'billing_postcode' ][ 'placeholder' ]   = "Почтовый индекс";
	$fields2[ 'billing' ][ 'billing_city' ][ 'placeholder' ]       = "Город";
	$fields2[ 'billing' ][ 'billing_state' ][ 'placeholder' ]      = "Область или регион";
	$fields2[ 'billing' ][ 'billing_address_1' ][ 'placeholder' ]  = "Адрес";
	
	$fields2[ 'order' ][ 'order_comments' ] = $fields[ 'order' ][ 'order_comments' ];
    $fields2[ 'order' ][ 'order_comments' ][ 'placeholder' ] = "Укажите свои параметры: обхват груди, талии, бёдер, а такжке рост и длину рукава.";

    $fields2[ 'account' ][ 'account_password' ] = $fields[ 'account' ][ 'account_password' ];
    //$fields2[ 'account' ][ 'account_password-2' ] = $fields[ 'account' ][ 'account_password-2' ];
	
	return $fields2;
}