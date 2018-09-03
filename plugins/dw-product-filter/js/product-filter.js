jQuery( document ).on( 'click', '.dw-filterable', function() {
    let term_id = jQuery('.dw-product-filter-wrapper').data('term-id');

    let attr_type = jQuery(this).data('attr-type');
    let attr_value = jQuery(this).data('attr-value');

    jQuery.ajax({
        url : dwf.ajax_url,
        type : 'post',
        data : {
            action : 'post_filter_var',
            term_id: term_id,
            attr_type: attr_type,
            attr_value: attr_value
        },
        success : function( response ) {
            jQuery('.matching-taxonomies').html(response);
        }
    });
})