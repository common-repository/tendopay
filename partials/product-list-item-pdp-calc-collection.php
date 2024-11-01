<?php

use TendoPay\Constants;

$product = wc_get_product();

?>
<div id="pdp-details-<?php echo $product->get_id(); ?>" class="tendopay__pdp-details tendopay__example-payment">
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
</div>
<script>pdpCalcProducts.push({productId: <?php echo esc_js($product->get_id()); ?>, price: <?php echo esc_js($product->get_price('edit')); ?>});</script>
