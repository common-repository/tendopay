<?php

namespace TendoPay\API\V2;

use TendoPay\Gateway_Constants;
use TendoPay\SDK\V2\TendoPayClient;
use TendoPay\Utils;

class TendoPayApiClientFactory
{
    /**
     * @return TendoPayClient
     */
    public function createClient() {
        $gatewayOptions = get_option("woocommerce_" . Gateway_Constants::GATEWAY_ID . "_settings");

        return new TendoPayClient(
            apply_filters('tendopay_api_client_config', [
                'CLIENT_ID'                => $gatewayOptions[ Gateway_Constants::OPTION_TENDOPAY_CLIENT_ID ],
                'CLIENT_SECRET'            => $gatewayOptions[ Gateway_Constants::OPTION_TENDOPAY_CLIENT_SECRET ],
                'REDIRECT_URL'             => '',
                'TENDOPAY_SANDBOX_ENABLED' => Utils::isSandboxEnabled(),
            ], $gatewayOptions)
        );
    }
}