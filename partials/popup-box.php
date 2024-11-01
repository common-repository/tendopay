<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="<?php use TendoPay\Constants;

    echo TENDOPAY_BASEURL . "/assets/css/marketing-popup-box-iframe.css"; ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<body>
<div id="tendopay-popup">
    <header class="background-color-lightgrey p-2">
        <div class="text-align-center">
            <img src="https://static.tendopay.dev/logo/logo.png" class="img-logo" alt="Tendopay Logo">
        </div>
        <div class="text-align-center">
            <h1 class="font-blue-color font-family-verdana heading-text font-weight-500">
                <?php _e( 'Get your purchase now, pay it on installments', 'tendopay' ); ?></h1>
        </div>
    </header>
    <div class="page-container px-3 px-sm-0">
        <!-- DESKTOP VERSION -->
        <div class="text-align-center row d-md-flex d-block justify-content-center pt-2">
            <div class="px-2 pb-1">
                <img src="https://static.tendopay.dev/plugins/images/pop_up_img_2.png"
                     class="img-icon" alt="Stopwatch">
                <h3 class="font-blue-color font-family-verdana font-weight-500 h3-font-size"><?php _e( 'Quick and Easy', 'tendopay' ); ?></h3>
                <p class="font-grey-color font-family-verdana p-font-size">
	                <?php _e( 'Enter a few pieces of information and get approved quickly.', 'tendopay' ); ?>
                </p>
            </div>
            <div class="px-2 pb-1">
                <img src="https://static.tendopay.dev/plugins/images/pop_up_img_1.png"
                     class="img-icon" alt="Dollar icon">
                <h3 class="font-blue-color font-family-verdana font-weight-500 h3-font-size"><?php _e( 'No Hidden Fees', 'tendopay' ); ?></h3>
                <p class="font-grey-color font-family-verdana p-font-size">
	                <?php _e( 'Know up front what you\'ll owe, with no hidden costs and no surprises.', 'tendopay' ); ?>
                </p>
            </div>
        </div>
        <div class="text-align-center">
            <a href="<?php echo esc_url( Constants::TENDOPAY_MARKETING ); ?>" class="btn font-family-verdana" target="_blank"><?php _e( 'See If You Qualify', 'tendopay' ); ?></a>
        </div>
    </div>
    <footer class="text-align-center font-family-verdana background-color-grey">
        <p class="footer"><?php _e( 'TendoPay offers a credit line which let\'s you shop on this store and many others. To see if ' .
            'you qualify for a TendoPay credit just click the link above and follow the application instructions. We will ' .
            'give you a decision as quickly as 30 minutes later.', 'tendopay' ); ?></p>
    </footer>
</div>
</body>
</html>
