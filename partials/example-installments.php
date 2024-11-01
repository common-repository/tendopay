<?php

use TendoPay\Constants;

$product = wc_get_product();

?>
    <div id="example-payment-<?php echo $product->get_id(); ?>" class="tendopay__example-payment" style="clear: both; padding: 0 0 2rem;">
            <span class="tendopay_example-payment__loading">
                <?php _e( 'Loading the best price for you', 'tendopay' ); ?>
                <div class="tp-loader">
                    <div class="tp-loader-dots">
                        <div class="tp-loader-dot"></div>
                        <div class="tp-loader-dot"></div>
                        <div class="tp-loader-dot"></div>
                    </div>
                </div>
            </span>
        <span class="tendopay_example-payment__received"></span>

        <img src="<?php echo esc_url( Constants::TENDOPAY_LOGO_BLUE ); ?>" alt="TendoPay logo"
             class="tendopay__example-payment__logo">

        <br><div class="tendopay__example-payment__disclaimer clickable"
               style="font-size: 0.8em;display: block;color: #999;"><?php _e( '(See if you qualify <u>here</u>)',
				'tendopay' ); ?></div>
    </div>
    <script>
        (function ($) {
            $.ajax('<?php echo admin_url( "admin-ajax.php?action=example-payment&price={$product->get_price()}" ); ?>')
                .always(function () {
                    $("#example-payment-<?php echo $product->get_id(); ?> .tendopay_example-payment__loading").css({display: "none"});
                })
                .fail(function () {
                    $("#example-payment-<?php echo $product->get_id(); ?>").hide();
                })
                .done(function (data) {
                    if (data && data.hasOwnProperty('data') && data.data.hasOwnProperty('response')) {
                        $("#example-payment-<?php echo $product->get_id(); ?> .tendopay_example-payment__received").css({display: "inline"}).html(data.data.response);
                    } else {
                        $("#example-payment-<?php echo $product->get_id(); ?>").hide();
                    }
                });

            $('#example-payment-<?php echo $product->get_id(); ?> .tendopay__example-payment__logo, '
                + '#example-payment-<?php echo $product->get_id(); ?> .tendopay__example-payment__disclaimer')
                .click(function () {
                $('.tendopay__popup__container').show();
            });
        })(jQuery);
    </script>
<?php
