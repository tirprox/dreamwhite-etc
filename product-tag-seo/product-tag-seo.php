<?php
/*
Plugin Name: Метки товаров
Plugin URI:
Description: Дополнительные описания к меткам и категориям товаров, rewrite на правильные фильтры
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moysklad-woocommerce
Domain Path: /languages
*/

//доп описание категорий
add_action( 'product_cat_edit_form_fields', 'wpm_taxonomy_edit_meta_field', 10, 2 );

function wpm_taxonomy_edit_meta_field($term) {
	$t_id = $term->term_id;
	$term_meta = get_option( "taxonomy_$t_id" );
	$content = $term_meta['custom_term_meta'] ? wp_kses_post( $term_meta['custom_term_meta'] ) : '';
	$settings = array( 'textarea_name' => 'term_meta[custom_term_meta]' );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[custom_term_meta]">Второе описание или банеры внизу для категории</label></th>
		<td>
			<?php wp_editor( $content, 'product_cat_details', $settings ); ?>
		
		</td>
	</tr>
	<?php
}

add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );

function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = wp_kses_post( stripslashes($_POST['term_meta'][$key]) );
			}
		}
		
		update_option( "taxonomy_$t_id", $term_meta );
	}
}

add_action( 'woocommerce_after_shop_loop', 'wpm_product_cat_archive_add_meta' );

function wpm_product_cat_archive_add_meta() {
	$t_id = get_queried_object()->term_id;
	$term_meta = get_option( "taxonomy_$t_id" );
	$term_meta_content = $term_meta['custom_term_meta'];
	if ( $term_meta_content != '' ) {
		echo '<div class="woo-sc-box normal rounded full">';
		echo apply_filters( 'the_content', $term_meta_content );
		echo '</div>';
	}
}

//доп описание меток
add_action( 'product_tag_edit_form_fields', 'wpm_taxonomy_edit_tag_meta_field', 10, 2 );

function wpm_taxonomy_edit_tag_meta_field($term) {
	$t_id = $term->term_id;
	$term_meta = get_option( "taxonomy_$t_id" );
	$content = $term_meta['custom_term_meta'] ? wp_kses_post( $term_meta['custom_term_meta'] ) : '';
	$settings = array( 'textarea_name' => 'term_meta[custom_term_meta]' );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[custom_term_meta]">Второе описание или банеры внизу для меток</label></th>
		<td>
			<?php wp_editor( $content, 'product_tag_details', $settings ); ?>
		
		</td>
	</tr>
	<?php
}

add_action( 'product_tag_edit_form_fields', 'wpm_taxonomy_edit_tag_shortcode', 10, 2 );
function wpm_taxonomy_edit_tag_shortcode($term) {
	$t_id = $term->term_id;
	$term_meta = get_option( "taxonomy_$t_id" );
	$content = $term_meta['custom_tag_shortcode'] ? wp_kses_post( $term_meta['custom_tag_shortcode'] ) : '';
	$settings = array( 'textarea_name' => 'term_meta[custom_tag_shortcode]' );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[custom_tag_shortcode]">Шорткод для фильтра метки</label></th>
		<td>
			<?php wp_editor( $content, 'product_tag_shortcode', $settings ); ?>
		
		</td>
	</tr>
	<?php
}

add_action( 'woocommerce_before_shop_loop', 'wpm_product_tag_archive_add_custom_shortcode' );

function wpm_product_tag_archive_add_custom_shortcode() {
	$t_id = get_queried_object()->term_id;
	$term_meta = get_option( "taxonomy_$t_id" );
	$term_meta_content = $term_meta['custom_tag_shortcode'];
	if ( $term_meta_content != '' ) {
		echo do_shortcode($term_meta_content);
		//do_action( 'my_ivpa_archive_product_action' );
		// echo '<div class="woo-sc-box normal rounded full">';
		//   echo apply_filters( 'the_content', $term_meta_content );
		// echo '</div>';
	}
}


add_action( 'edited_product_tag', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_tag', 'save_taxonomy_custom_meta', 10, 2 );


include (dirname(__DIR__) . "/import-export/import-products/TagRewriteRules.php");
function custom_rewrite_rules() {
	$colors = TagRewriteRules::$rules;
	foreach($colors as $cat => $color) {
		add_rewrite_rule('^product-tag/' . $cat ."$",'index.php?product_tag=' . $cat . '&pa_tsvet=' . $color,'top');
	}
	add_rewrite_rule("^(.*)(.*zhenskie-palto)(.*bolotnyj-146106)$", "/product-tag/zhenskie-palto-bolotnogo-tsveta/", "top");
	
}
add_action('init', 'custom_rewrite_rules', 10, 0);