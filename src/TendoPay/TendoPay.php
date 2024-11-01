<?php

/**
 * Created by PhpStorm.
 * User: robert
 * Date: 16.01.18
 * Time: 22:03
 */

namespace TendoPay;

use TendoPay\API\RepaymentCalculatorService;
use TendoPay\API\V2\TransactionVerificationService;
use TendoPay\Exceptions\TendoPay_Integration_Exception;
use TendoPay\Marketing\Popup_Box_Helper;
use \WC_Order_Factory;

/**
 * Class TendoPay
 * @package TendoPay
 */
class TendoPay
{
    const COMPLETED_AT_KEY = "_tendopay_completed_at";
    const LAST_DISPOSITION_KEY = "_tendopay_last_disposition";

    /**
     * @var TendoPay $instance the only instance of this class
     */
    private static $instance;

    /**
     * Private constructor required for singleton implementation. Registers hooks.
     */
    private function __construct()
    {
        new Popup_Box_Helper();
        $this->register_hooks();
    }

    /**
     * Returns the only instance of this class. If instance wasn't created yet - it creates the instance before returning.
     *
     * @return TendoPay the only instance of this class
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new TendoPay();
        }

        return self::$instance;
    }

    /**
     * Registers hooks required by the plugin:
     * - payment gateway initialization and registration in the woocommerce
     * - Setting up rewirte rules
     * - handing redirect with disposition from TendoPay after the transaction is completed
     */
    public function register_hooks()
    {
        add_action('plugins_loaded', [$this, 'init_gateway']);
        add_filter('woocommerce_payment_gateways', [$this, 'register_gateway']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_resources']);
        add_action('wp_ajax_tendopay-result', [$this, 'handle_redirect_from_tendopay']);
        add_action('wp_ajax_nopriv_tendopay-result', [$this, 'handle_redirect_from_tendopay']);
        add_action('wp_ajax_example-payment', [$this, 'example_installment_ajax_handler']);
        add_action('wp_ajax_nopriv_example-payment', [$this, 'example_installment_ajax_handler']);

        $gateway_options =
            get_option('woocommerce_' . Gateway_Constants::GATEWAY_ID . '_settings');

        $example_installments_location = Gateway_Constants::OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_DEFAULT_LOCATION;
        if ( ! empty($gateway_options) &&
             isset($gateway_options[Gateway_Constants::OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_LOCATION])) {
            $example_installments_location =
                $gateway_options[Gateway_Constants::OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_LOCATION];
        }

        if ('no' !== $example_installments_location) {
            add_action($example_installments_location, [$this, 'output_example_payment']);
        }
    }

    public function output_example_payment()
    {
        include TENDOPAY_BASEPATH . "/partials/example-installments.php";
    }

    public function enqueue_resources()
    {
        wp_enqueue_style("tendopay", TENDOPAY_BASEURL . "/assets/css/tendopay.css");

        if (is_product() || is_checkout() || is_checkout_pay_page()) {
            wp_enqueue_style("tendopay-marketing-popup-box", TENDOPAY_BASEURL . "/assets/css/marketing-popup-box.css");

            $localized_script_handler = "tendopay-marketing-popup-box";
            wp_register_script($localized_script_handler, TENDOPAY_BASEURL . "/assets/js/marketing-popup-box.js",
                ["jquery"], false, true);
            wp_localize_script($localized_script_handler, "urls", ["adminajax" => admin_url("admin-ajax.php")]);
            wp_enqueue_script($localized_script_handler);
        }
    }

    /**
     * @throws TendoPay_Integration_Exception
     */
    public function example_installment_ajax_handler()
    {
        $price = $_REQUEST["price"];
        $repayment_calculator = new RepaymentCalculatorService();
        $paymentDetails = $repayment_calculator->getPaymentsDetails($price);

        wp_send_json_success(
            [
                'response' => sprintf(
                    _x('Or as low as <strong>%s/installment</strong> with ',
                        'Displayed on the product page. The replacement should be price with currency symbol',
                        'tendopay'),
                    wc_price($paymentDetails->getSinglePaymentAmount())
                )
            ]
        );
    }

    /**
     * @hook admin_post_tendopay-result 10
     * @hook admin_post_nopriv_tendopay-result 10
     *
     * Handles redirect with disposition from TendoPay after the transaction is completed.
     *
     * When the redirect comes in this function verifies the outcome of transaction. It does that first by checking if
     * the hash from incoming parameters is calculated properly. Only if it is valid it will call TendoPay API
     * Verification Endpoint with verification token to get the confirmed status of this transaction.
     *
     * Please note you should not trust only the parameters that comes with the redirect.
     */
    function handle_redirect_from_tendopay()
    {
        $posted_data = apply_filters('tendopay_posted_data', $_REQUEST);

        if (isset($posted_data['action'])) {
            unset($posted_data['action']);
        }

        $order = WC_Order_Factory::get_order((int)$posted_data[Constants::ORDER_ID_PARAM]);
        $order_key = $posted_data[Constants::ORDER_KEY_PARAM];

        if ($order->get_order_key() !== $order_key) {
            wp_die(new \WP_Error('wrong-order-key', __('Wrong order key provided', 'tendopay')),
                __('Wrong order key provided', 'tendopay'), 403);
        }

        if ($this->is_awaiting_payment($order)) {
            $this->perform_verification($order, $posted_data);
        } else {
            wp_redirect($order->get_checkout_order_received_url());
        }

        exit;
    }

    /**
     * Checks if the order is awaiting payment.
     *
     * @param \WC_Order $order the order to be checked for payment status
     *
     * @return bool true if the order is awaiting payment
     */
    private function is_awaiting_payment(\WC_Order $order)
    {
        return $order->has_status(apply_filters('woocommerce_valid_order_statuses_for_payment_complete',
            ['on-hold', 'pending', 'failed', 'cancelled'], $order));
    }

    /**
     *
     * Does the actual verification, updates the stocks and empties the cart.
     *
     * @param \WC_Order $order order to be verified
     * @param array $posted_data posted data
     */
    private function perform_verification(\WC_Order $order, $posted_data)
    {
        update_post_meta($order->get_id(), self::LAST_DISPOSITION_KEY, $posted_data);

        try {
            $transactionVerificationService = new TransactionVerificationService();
            $transaction = $transactionVerificationService->getVerifiedTransaction($_REQUEST);
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
            error_log($exception->getTraceAsString());
            $errorMessage = 'Could not communicate with TendoPay properly or transaction not verified';
            wp_die(new \WP_Error('tendopay-integration-error',
                __($errorMessage, 'tendopay')),
                __($errorMessage, 'tendopay'), 403);
        }

        if ($transaction->getStatus() == 'PAID' && $transaction->getAmount() >= $order->get_total('edit')) {
            global $woocommerce;
            $woocommerce->cart->empty_cart();

            wc_reduce_stock_levels($order->get_id());

            $current_datetime = new \DateTime();
            update_post_meta($order->get_id(), self::COMPLETED_AT_KEY,
                $current_datetime->format(\DateTime::ISO8601));

            $order->payment_complete();
            wp_redirect($order->get_checkout_order_received_url());
        } elseif (!empty($_REQUEST[Constants::MESSAGE_PARAM]) && $_REQUEST[Constants::MESSAGE_PARAM] == 'User cancelled.') {
            wc_add_notice(__("You've cancelled processing of the payment.", "tendopay"),
                "notice");
            wp_redirect(wc_get_checkout_url());
        } else {
            wc_add_notice(__("The payment could not be processed. Please use other payment method.", "tendopay"),
                "error");
            wp_redirect(wc_get_checkout_url());
        }

        exit;
    }


    /**
     * @hook woocommerce_payment_gateways 10
     *
     * Registers TendoPay gateway in the system.
     *
     * @param array $methods Other methods registered in the system
     *
     * @return array modified list of gateways (including tendopay)
     */
    public function register_gateway($methods)
    {
        $methods[] = Gateway::class;

        return $methods;
    }

    /**
     * @hook plugins_loaded 10
     *
     * Initializes gateway
     */
    public function init_gateway()
    {
        include_once dirname(__FILE__) . "/Gateway.php";
    }

    /**
     * @hook admin_notices 10
     *
     * Shows notice that Woocommerce plugin must be enabled.
     */
    public static function no_woocommerce_admin_notice()
    {
        ?>
        <div class="notice notice-warning">
            <p><?php
                _e('<strong>TendoPay</strong> requires <strong>WooCommerce</strong> plugin enabled.',
                    'tendopay');
                ?></p>
        </div>
        <?php
    }

    /**
     * @hook admin_notices 10
     *
     * Shows notice that Woocommerce plugin must be enabled.
     */
    public static function no_php_currency_admin_notice()
    {
        ?>
        <div class="notice notice-warning">
            <p><?php
                _e('<strong>TendoPay</strong> only works when <strong>Filipino currency (PHP)</strong> is active on the store.',
                    'tendopay');
                ?></p>
        </div>
        <?php
    }

    /**
     * @hook admin_notices 10
     *
     * Shows notice that Woocommerce plugin must be enabled.
     */
    public static function no_credentials_admin_notice()
    {
        $plugin_data = get_plugin_data(dirname(__FILE__, 3) . '/tendopay.php');
        $plugin_version = $plugin_data['Version'];

        ?>
        <div class="notice notice-error">
            <p><?php
                printf(__('Thank you for updating your <strong>TendoPay</strong> plugin to version <strong>%s</strong>.'
                          . ' Please read the <a href="%s" target="_blank">plugin upgrade documentation</a> to complete the upgrade.',
                    'tendopay'), $plugin_version,
                    "https://doc.merchant.tendopay.dev/docs/2.0/wordpress/plugin-upgrade");
                ?></p>
        </div>
        <?php
    }

    /**
     * @hook plugin_action_links_tendopay/tendopay.php 10
     *
     * @param array $links List of other links
     *
     * @return array list of plugin action links with added link to plugin settings
     */
    public static function add_settings_link($links)
    {
        $settings_link = [
            '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=tendopay') . '">'
            . __('Settings', 'tendopay') . '</a>'
        ];

        return array_merge($settings_link, $links);
    }

    public static function add_plugin_row_meta_links($links, $file)
    {
        if (plugin_basename(TENDOPAY_ROOT_FILE) === $file) {
            $row_meta = array(
                'terms' => '<a target="_blank" href="' . esc_url('https://tendopay.ph/terms') . '" aria-label="' . esc_attr__('View TendoPay Terms of Use',
                        'tendopay') . '">' . esc_html__('Terms of Use', 'tendopay') . '</a>',
            );

            return array_merge($links, $row_meta);
        }

        return (array)$links;
    }

    private function __wakeup()
    {
    }

    private function __clone()
    {
    }
}
