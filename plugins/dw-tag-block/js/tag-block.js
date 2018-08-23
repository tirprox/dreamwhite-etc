jQuery( document ).on( 'click', '.dw-tag-block-expand', function() {
    jQuery('.dw-tag-block-expand').removeClass('dw-tag-block-expand').addClass('dw-tag-block-collapse').text('Меньше меток ▴');
    jQuery('.dw-tag-block').removeClass('dw-tag-block-collapsed').addClass('dw-tag-block-expanded');
});

jQuery( document ).on( 'click', '.dw-tag-block-collapse', function() {
    jQuery('.dw-tag-block-collapse').removeClass('dw-tag-block-collapse').addClass('dw-tag-block-expand').text('Больше меток ▾');
    jQuery('.dw-tag-block').removeClass('dw-tag-block-expanded').addClass('dw-tag-block-collapsed');
});

