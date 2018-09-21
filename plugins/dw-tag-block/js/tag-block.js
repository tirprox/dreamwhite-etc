jQuery( document ).on( 'click', '.dw-tag-block-expand', function() {
    jQuery('.dw-tag-block-expand').removeClass('dw-tag-block-expand').addClass('dw-tag-block-collapse').text('Меньше меток ▴');
    jQuery('.dw-tag-block').removeClass('dw-tag-block-collapsed').addClass('dw-tag-block-expanded');
});

jQuery( document ).on( 'click', '.dw-tag-block-collapse', function() {
    jQuery('.dw-tag-block-collapse').removeClass('dw-tag-block-collapse').addClass('dw-tag-block-expand').text('Больше меток ▾');
    jQuery('.dw-tag-block').removeClass('dw-tag-block-expanded').addClass('dw-tag-block-collapsed');
});
jQuery(function () {
    block = jQuery('.dw-tag-block');
    height = block.height();
    console.log(height);
    if (height < 33) {
        jQuery('.dw-tag-block-expand').hide();
    }
    else {
        block.addClass('dw-tag-block-collapsed');
    }

});

