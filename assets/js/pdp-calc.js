(function ($) {
    $('body').on('click', '.tendopay__pdp-details', function (e) {
        e.preventDefault();
        e.stopPropagation();

        return false;
    });

    $('body').append(
        '<div class="tendopay__pdp-popup__container" style="display: none;">' +
        '<div class="tendopay__pdp-popup__iframe-wrapper">' +
        '<div class="tendopay__pdp-popup__close"></div>' +
        '<iframe src="' + urls.adminajax + '?action=pdp-calc-popup" class="tendopay__popup__iframe"></iframe>' +
        '</div>' +
        '</div>');
    $('.tendopay__pdp-popup__close').click(function () {
        $('.tendopay__pdp-popup__container').hide();
        $('html').removeClass('hide-scrollers');
    });
})(jQuery);