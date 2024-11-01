<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 06.01.2020
 * Time: 00:17
 */

namespace TendoPay;


class Gateway_Constants {
	/**
	 * Unique ID of the gateway.
	 */
	const GATEWAY_ID = 'tendopay';
	const TENDOPAY_PAYMENT_INITIATED_KEY = '_tendopay_payment_initiated';
	const OPTION_METHOD_TITLE = 'method_title';
	const OPTION_ENABLED = 'enabled';
	const OPTION_METHOD_DESC = 'method_description';
	const OPTION_TENDOPAY_SANDBOX_ENABLED = 'tendo_sandbox_enabled';
    const OPTION_TENDOPAY_0PRC_INTEREST_ENABLED = 'tendo_0prc_interest_enabled';
	const OPTION_TENDOPAY_CLIENT_ID = 'tendo_client_id_v2';
	const OPTION_TENDOPAY_CLIENT_SECRET = 'tendo_client_secret_v2';
	const OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_LOCATION = 'tendo_example_installments_location';
	const OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_DEFAULT_LOCATION = 'woocommerce_after_add_to_cart_button';
    const OPTION_TENDOPAY_CUSTOMIZED_PAYMENT_METHOD_OPTION_DISABLED = 'tendo_customized_payment_method_option_disabled';
    const OPTION_TENDOPAY_DISABLE_PRODUCT_LISTING_EXAMPLE_INSTALLMENTS =
        'tendo_disable_product_listing_example_installments';
}
