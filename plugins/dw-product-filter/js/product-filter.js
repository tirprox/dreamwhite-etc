jQuery( document ).on( 'click', '.dw-color-button', function() {
    let attr_type = jQuery(this).data('attr-type');
    let attr_value = jQuery(this).data('attr-value');

    jQuery.ajax({
        url : dwf.ajax_url,
        type : 'post',
        data : {
            action : 'post_filter_var',
            attr_type: attr_type,
            attr_value: attr_value
        },
        success : function( response ) {
            jQuery('.matching-taxonomies').html(response);
        }
    });
})