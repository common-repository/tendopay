<?php

namespace TendoPay\Marketing;

use TendoPay\Gateway;
use TendoPay\Gateway_Constants;

class PaymentMethodCustomizer
{
    public static function getPaymentMethodTemplate($template, $template_name, $args)
    {
        $gatewayOptions = get_option('woocommerce_' . Gateway_Constants::GATEWAY_ID . '_settings');

        $wcTemplateFilePath = WC()->plugin_path() . '/templates/checkout/payment-method.php';

        if ($gatewayOptions[Gateway_Constants::OPTION_TENDOPAY_CUSTOMIZED_PAYMENT_METHOD_OPTION_DISABLED] === 'yes'
            || ! isset($args['gateway'])
            || ! ($args['gateway'] instanceof Gateway)
            || $wcTemplateFilePath !== $template) {
            return $template;
        }

        return TENDOPAY_BASEPATH . '/partials/payment-method.php';
    }
}