<?php

namespace TendoPay\Marketing;

use TendoPay\API\RepaymentCalculatorService;
use TendoPay\Gateway_Constants;
use TendoPay\Utils;

class PdpCalculatorHelper
{
    private function __construct()
    {
    }

    public static function collectProductDetails()
    {
        if (static::isProductListingExampleInstallmentsDisabled()) {
            return;
        }

        include TENDOPAY_BASEPATH . "/partials/product-list-item-pdp-calc-collection.php";
    }

    public static function executeCalculation()
    {
        if (static::isProductListingExampleInstallmentsDisabled()) {
            return;
        }

        include TENDOPAY_BASEPATH . "/partials/pdp-calc-exec.php";
    }

    public static function initializeProductDetailsCollection()
    {
        include TENDOPAY_BASEPATH . "/partials/pdp-calc-init.php";
    }

    public static function calculatePrice()
    {
        $productDetails = $_REQUEST['productDetails'];

        $response = [];
        foreach ($productDetails as $product) {
            $repayment_calculator = new RepaymentCalculatorService();
            $paymentDetails = $repayment_calculator->getPaymentsDetails($product['price']);

            $response[] = [
                'productId' => $product['productId'],
                'html'      => sprintf(
                    _x(
                        'Or %d payments of <span class="tendopay__pdp-details__single-payment">%s</span> with ',
                        'Displayed on the product page list item. The replacement should be number of payments'
                        . ' and price with currency symbol',
                        'tendopay'),
                    $paymentDetails->getNumberOfPayments(),
                    wc_price($paymentDetails->getSinglePaymentAmount())
                ),
            ];
        }

        wp_send_json_success(['response' => $response]);
    }

    public static function enqueueResources()
    {
        if ( ! is_shop() && ! is_product_category() && ! is_product_tag() && ! is_product_taxonomy()) {
            return;
        }

        $localized_script_handler = "tp-pdp-calc-helper";
        wp_register_script($localized_script_handler, TENDOPAY_BASEURL . "/assets/js/pdp-calc.js",
            ["jquery"], false, true);
        wp_localize_script($localized_script_handler, "urls", ["adminajax" => admin_url("admin-ajax.php")]);
        wp_enqueue_script($localized_script_handler);
    }

    public static function renderPopup()
    {
        ob_start();

        if (Utils::isNoInterestEnabled()) {
            include TENDOPAY_BASEPATH . "/partials/pdp-calc-popup-no-interest.php";
        } else {
            include TENDOPAY_BASEPATH . "/partials/pdp-calc-popup-interest.php";
        }

        $icons = ob_get_clean();

        include TENDOPAY_BASEPATH . "/partials/pdp-calc-popup.php";
        die();
    }

    private static function isProductListingExampleInstallmentsDisabled() {
        $gatewayOptions = get_option('woocommerce_' . Gateway_Constants::GATEWAY_ID . '_settings');
        if (isset($gatewayOptions[Gateway_Constants::OPTION_TENDOPAY_DISABLE_PRODUCT_LISTING_EXAMPLE_INSTALLMENTS])
            && $gatewayOptions[Gateway_Constants::OPTION_TENDOPAY_DISABLE_PRODUCT_LISTING_EXAMPLE_INSTALLMENTS] ===
               'yes') {
            return true;
        }

        return false;
    }
}