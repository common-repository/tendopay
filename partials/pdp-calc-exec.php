<script>
    (function ($) {
        $.ajax({
            method: 'POST',
            url: '<?php echo admin_url("admin-ajax.php?action=pdp-calculate-price"); ?>',
            data: {productDetails: pdpCalcProducts}
        }).always(function () {
            $(".tendopay_example-payment__loading").css({display: "none"});
        }).fail(function () {
            $(".pdp-details").hide();
        }).done(function (data) {
            if (data && data.hasOwnProperty('data') && data.data.hasOwnProperty('response')) {
                data.data.response.forEach(function (singleResponse) {
                    $("#pdp-details-" + singleResponse.productId + " .tendopay_example-payment__received")
                        .css({display: "inline"})
                        .html(singleResponse.html);
                });
            } else {
                $(".pdp-details").hide();
            }
        });

        $('.pdp-details').click(function () {
            $('.tendopay__pdp-popup__container').show();
            $('html').addClass('hide-scrollers');
        });
    })(jQuery);
</script>