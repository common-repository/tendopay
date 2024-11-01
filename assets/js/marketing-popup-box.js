(function($){

    $('body').append(
        '<div class="tendopay__popup__container" style="display: none;">' +
        '<div class="tendopay__popup__iframe-wrapper">' +
        '<div class="tendopay__popup__close"></div>' +
        '<iframe src="' + urls.adminajax + '?action=popupbox" class="tendopay__popup__iframe"></iframe>' +
        '</div>' +
        '</div>');
    $('.tendopay__popup__close').click(function () {
        $('.tendopay__popup__container').hide();
    });
})(jQuery);