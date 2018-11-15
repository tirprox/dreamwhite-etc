<?php
/*
Plugin Name: Дополнения товаров
Plugin URI:
Description: Вариации как отдельные товары
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moysklad-woocommerce
Domain Path: /languages
*/

add_action('woocommerce_before_variations_form', 'my_extra_button_on_product_page', 30);

function my_extra_button_on_product_page()
{
    global $product;

    $product_id = $product->get_id();

    $article = get_post_meta($product_id , 'article', true);

    $products = wc_get_products(
        [
            'article' => $article,
            'status' => 'publish',
            'limit' => 1000,
            //'exclude' => [ $product->get_id() ],
        ]
    );



    echo '<h4>Цвет</h4>';
    echo '<div class="variation-colors">';

    foreach ($products as $item) {
        if ($item->get_stock_quantity() > 0) {
            $item_id = $item->get_id();
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $item_id ), 'woocommerce_gallery_thumbnail' );
            if ($image[0] !== null) {
                $color = $item->get_attribute( 'pa_tsvet' );

                if ($item_id === $product_id) {
                    ?>
                    <div class="" style="box-sizing: border-box; display: inline-block; position:relative; margin-right: 4px; margin-bottom: 8px" >
                        <img src="<?php echo $image[0] ?>" alt="<?php echo $color ?>">
                        <div class="shadow"
                             style="
                     width: 100px;
                     height: 100px;
                     box-shadow: 0 0 0 4px #78BC9C inset;
                     top: 0;
                       left: 0;
                     position:absolute;" >

                        </div>
                    </div>
                    <?php
                }
                else {
                    ?>
                    <div class="single-product-color-thumbnail" style="box-sizing: border-box; display: inline-block; margin-right: 4px; margin-bottom: 8px">
                        <a href="<?php echo $item->get_permalink() ?>"><img src="<?php echo $image[0] ?>" alt="<?php echo $color ?>"></a>
                    </div>
                    <?php
                }

            }


       }

    }

    echo '</div>';

}


function handle_custom_query_var($query, $query_vars)
{
    if (!empty($query_vars['article'])) {
        $query['meta_query'][] = array(
            'key' => 'article',
            'value' => esc_attr($query_vars['article']),
        );
    }

    return $query;
}

add_filter('woocommerce_product_data_store_cpt_get_products_query', 'handle_custom_query_var', 10, 2);