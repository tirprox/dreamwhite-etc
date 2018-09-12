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
            attr_value: attr_value,
            params: getQueryParams(document.location.search)
        },
        success : function( response ) {
            response= JSON.parse(response);
            if (response.url !== undefined) {
                window.location.href = response.url;
            }

            jQuery('.matching-taxonomies').html(response);
        }
    });
})

function getQueryParams(qs) {
    qs = qs.split('+').join(' ');

    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}