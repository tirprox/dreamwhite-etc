<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$linked_image = false;

//Featured

$image_title             = esc_attr( get_the_title( get_post_thumbnail_id() ) );
$image_src               = wp_get_attachment_image_src( get_post_thumbnail_id(), 'shop_thumbnail' );
$image_data_src          = wp_get_attachment_image_src( get_post_thumbnail_id(), 'shop_single' );
$image_data_src_original = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
$image_link              = wp_get_attachment_url( get_post_thumbnail_id() );
$image                   = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );

?>

<?php if ( has_post_thumbnail() ) { ?>

  <div class="product-image-temp"></div>

  <div class="swiper-container product-images-carousel woocommerce-product-gallery__wrapper images">

    <div class="swiper-wrapper">
		
		<?php
		
		//Featured
		
		?>

      <div class="swiper-slide woocommerce-product-gallery__image">

        <div class="images">
			
			<?php if ( $linked_image == true ) : ?>
          <a href="<?php echo esc_url( $image_link ); ?>">
			  <?php else : ?>
            <span>
                    <?php endif; ?>

              <img src="<?php echo esc_url( $image_data_src[ 0 ] ); ?>"
                   data-src="<?php echo esc_url( $image_data_src_original[ 0 ] ); ?>"
                   alt="<?php echo esc_html( $image_title ); ?>" class="swiper-lazy wp-post-image">
                            <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
				
				<?php if ( $linked_image == true ) : ?>
          </a>
		<?php else : ?>
          </span>
		<?php endif; ?>

        </div>


      </div><!-- /.swiper-slide -->
		
		<?php
		
		//Thumbs
		
		$attachment_ids = $product->get_gallery_image_ids();
		
		if ( $attachment_ids ) {
			
			foreach ( $attachment_ids as $attachment_id ) {
				
				$image_link = wp_get_attachment_url( $attachment_id );
				
				if ( ! $image_link ) {
					continue;
				}
				
				$image_title             = esc_attr( get_the_title( $attachment_id ) );
				$image_src               = wp_get_attachment_image_src( $attachment_id, 'shop_single_small_thumbnail' );
				$image_data_src          = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
				$image_data_src_original = wp_get_attachment_image_src( $attachment_id, 'full' );
				$image_link              = wp_get_attachment_url( $attachment_id );
				$image                   = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
				?>

              <div class="swiper-slide">
				  
				  <?php if ( $linked_image == true ) : ?>
                <a href="<?php echo esc_url( $image_link ); ?>">
					<?php else : ?>
                  <span>
                        <?php endif; ?>

                    <img src="<?php echo esc_url( $image_data_src[ 0 ] ); ?>"
                         data-src="<?php echo esc_url( $image_data_src_original[ 0 ] ); ?>"
                         alt="<?php echo esc_html( $image_title ); ?>" class="swiper-lazy">
                                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
					  
					  <?php if ( $linked_image == true ) : ?>
                </a>
			  <?php else : ?>
                </span>
			  <?php endif; ?>

              </div><!-- /.swiper-slide -->
				
				<?php
				
			}
			
		}
		
		?>

    </div><!-- /.swiper-wrapper -->

    <!--
	  <div class="swiper-button-prev"></div>
	  <div class="swiper-button-next"></div>
	  -->

  </div><!-- /.swiper-container -->
	
	<?php
	
} else {
	echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
}

?>