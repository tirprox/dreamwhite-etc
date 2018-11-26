jQuery( document ).on( 'click', '#facebook-header-link', function() {
    fbq('track', 'ViewContent', {
        content_name: 'Palto',
        contents: [
            {
                id: '301',
                quantity: 1,
                item_price: 85.00
            },
            {
                id: '401',
                quantity: 2,
                item_price: 15.00
            }],
        content_type: 'product',
        content_ids: ['123','345']
    });
    console.log("Facebook header icon click")
});

jQuery( document ).ready( function() {
    console.log(window.yaCounter42085949.getClientID())
});

