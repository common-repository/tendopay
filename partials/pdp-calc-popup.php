<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="<?php use TendoPay\Constants;

    echo TENDOPAY_BASEURL . "/assets/css/marketing-popup-box-iframe.css"; ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<body>
<div id="tendopay-pdp-popup">
    <div class="page-container px-3">
        <img src="<?php echo TENDOPAY_BASEURL; ?>/assets/img/tp-logo-blue.svg" alt="TendoPay logo" class="tendopay__pdp-details__logo" />
        <?php echo $icons; ?>
        <div class="text-align-center">
            <a href="<?php echo esc_url( Constants::TENDOPAY_MARKETING ); ?>" class="btn" target="_blank"><?php _e( 'How it works', 'tendopay' ); ?></a>
        </div>
        <div class="tendopay__pdp-details__disclaimer">
            <?php printf( __( 'You must be over 18, a resident of the Philippines and meet additional criteria to quality.'
            . ' Late fees apply. <a href="%s" target="_blank">Click here for complete terms</a>. &copy; 2022 TendoPay',
                'tendopay' ), Constants::TENDOPAY_COMPLETE_TERMS_URL); ?>
        </div>
    </div>
</div>
</body>
</html>
