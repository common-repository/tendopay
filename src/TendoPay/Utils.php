<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 14.08.2018
 * Time: 07:26
 */

namespace TendoPay;


if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Utils class to provide utility methods used in the plugin.
 *
 * @package TendoPay
 */
class Utils {
	/**
	 * Checks whether the basic woocommerce plugin is enabled (wc is a required dependency)
	 *
	 * @return bool returns true if woocommerce is active
	 */
	public static function is_woocommerce_active() {
		return in_array( 'woocommerce/woocommerce.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

    public static function isPhpCurrencyActive() {
        return get_woocommerce_currency() == 'PHP';
    }

    /**
     *
     * @return bool true if sandbox is enabled
     */
    public static function isSandboxEnabled()
    {
        $gatewayOptions = get_option("woocommerce_" . Gateway_Constants::GATEWAY_ID . "_settings");

        return apply_filters(
            'tendopay_sandbox_enabled',
            $gatewayOptions[ Gateway_Constants::OPTION_TENDOPAY_SANDBOX_ENABLED ] === 'yes'
        );
    }

    public static function isNoInterestEnabled() {
        $gatewayOptions = get_option("woocommerce_" . Gateway_Constants::GATEWAY_ID . "_settings");

        return apply_filters(
            'tendopay_no_interest_enabled',
            $gatewayOptions[Gateway_Constants::OPTION_TENDOPAY_0PRC_INTEREST_ENABLED] === 'yes'
        );
    }

    public static function emptyCredentials()
    {
        $gatewayOptions = get_option("woocommerce_" . Gateway_Constants::GATEWAY_ID . "_settings");

        return apply_filters(
            'tendopay_empty_credentials',
            empty($gatewayOptions[ Gateway_Constants::OPTION_TENDOPAY_CLIENT_ID ])
            || empty($gatewayOptions[ Gateway_Constants::OPTION_TENDOPAY_CLIENT_SECRET ])
        );
    }
}
