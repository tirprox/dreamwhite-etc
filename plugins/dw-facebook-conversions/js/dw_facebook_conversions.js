jQuery( document ).on( 'click', '.add_to_wishlist', function() {
    //var element = $( this );
    var productId = this.getAttribute('data-product-id')

    fbq('track', 'AddToWishlist', {

        content_type: 'product',
        content_ids: [productId]
    });

    console.log(productId)
});

