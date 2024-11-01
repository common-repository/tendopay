<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 02.01.18
 * Time: 06:10
 */

namespace TendoPay;

use TendoPay\API\V2\PaymentRequestService;
use TendoPay\API\V2\TendoPayApiClientFactory;
use TendoPay\Exceptions\TendoPay_Integration_Exception;
use TendoPay\SDK\Models\Payment;
use TendoPay\SDK\V2\TendoPayClient;
use WC_Order;
use WC_Payment_Gateway;

/**
 * This class implements the woocommerce gateway mechanism.
 *
 * @package TendoPay
 */
class Gateway extends WC_Payment_Gateway
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Prepares the gateway configuration.
     */
    public function __construct()
    {
        $this->logger = new Logger();
        $this->id = Gateway_Constants::GATEWAY_ID;
        $this->has_fields = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option(Gateway_Constants::OPTION_METHOD_TITLE);
        $this->method_title = $this->get_option(Gateway_Constants::OPTION_METHOD_TITLE);
        $this->description = $this->get_option(Gateway_Constants::OPTION_METHOD_DESC);
        $this->order_button_text = apply_filters(
            'tendopay_order_button_text',
            __('Complete Order', 'tendopay')
        );


        $this->maybe_add_payment_initiated_notice();
        add_action('before_woocommerce_pay', [$this, 'maybe_add_payment_failed_notice']);

        $this->view_transaction_url = Constants::get_view_uri_pattern();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [
            $this,
            'process_admin_options'
        ]);

        add_action('woocommerce_checkout_init', [$this, 'maybe_add_outstanding_balance_notice']);
    }

    public function get_icon()
    {
        ob_start();
        include TENDOPAY_BASEPATH . "/partials/payment-gateway-icon.php";
        $icon_html = ob_get_clean();

        return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    public function maybe_add_outstanding_balance_notice()
    {
        $witherror = isset($_GET['witherror']) ? $_GET['witherror'] : '';
        $errors = explode(':', $witherror);
        $errors = is_array($errors) ? array_map('htmlspecialchars', $errors) : [];
        $error = isset($errors[0]) ? $errors[0] : '';
        $extra = isset($errors[1]) ? $errors[1] : '';

        switch ($error) {
            case 'outstanding_balance':
                $notice =
                    __(
                        "Your account has an outstanding balance, please repay your payment so you make an additional purchase.",
                        'tendopay'
                    );
                wc_print_notice($notice, 'error');
                break;
            case 'minimum_purchase':
            case 'maximum_purchase':
                $notice = __($extra, 'tendopay');
                wc_add_notice($notice, 'error');
                wp_redirect(wc_get_cart_url());
                die;
        }
    }

    public function maybe_add_payment_failed_notice()
    {
        $payment_failed = $_GET[Constants::PAYMANET_FAILED_QUERY_PARAM];

        if ($payment_failed) {
            $payment_failed_notice =
                __(
                    "The payment attempt with TendoPay has failed. Please try again or choose other payment method.",
                    'tendopay'
                );
            wc_print_notice($payment_failed_notice, 'error');
        }
    }

    private function maybe_add_payment_initiated_notice()
    {
        $order_id = absint(get_query_var('order-pay'));
        $payment_initiated = get_post_meta($order_id, Gateway_Constants::TENDOPAY_PAYMENT_INITIATED_KEY, true);

        if ($payment_initiated) {
            $payment_initiated_notice = __(
                "<strong>Warning!</strong><br><br>You've already initiated payment attempt with TendoPay once. If you continue you may end up finalizing two separate payments for single order.<br><br>Are you sure you want to continue?",
                'tendopay'
            );
            $notices = wc_get_notices();
            if (isset($notices['notice']) && ! empty($notices['notice'])) {
                $payment_initiated_notice .= "<br><br>";
            } else {
                $notices['notice'] = [];
            }

            array_unshift($notices['notice'], $payment_initiated_notice);
            wc_set_notices($notices);
        }
    }

    /**
     * Prepares settings forms for plugin's settings page.
     */
    public function init_form_fields()
    {
        $this->form_fields = [
            Gateway_Constants::OPTION_ENABLED                                => [
                'title'   => __('Enable/Disable', 'tendopay'),
                'type'    => 'checkbox',
                'label'   => __('Enable TendoPay Integration', 'tendopay'),
                'default' => 'yes'
            ],
            Gateway_Constants::OPTION_METHOD_TITLE                           => [
                'title'       => __('Payment gateway title', 'tendopay'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'tendopay'),
                'default'     => __('Buy now, pay later with TendoPay', 'tendopay'),
                'desc_tip'    => true,
            ],
            Gateway_Constants::OPTION_METHOD_DESC                            => [
                'title'       => __('Payment method description', 'tendopay'),
                'description' => __(
                    'Additional information displayed to the customer after selecting TendoPay method',
                    'tendopay'
                ),
                'type'        => 'textarea',
                'default'     => 'Click "Complete Order" to be redirected to TendoPay and choose installment terms that'
                                 . ' fit your schedule!',
                'desc_tip'    => true,
            ],
            Gateway_Constants::OPTION_TENDOPAY_SANDBOX_ENABLED               => [
                'title'       => __('Enable SANDBOX', 'tendopay'),
                'description' => __(
                    'Enable SANDBOX if you want to test integration with TendoPay without real transactions.',
                    'tendopay'
                ),
                'type'        => 'checkbox',
                'default'     => 'no',
                'desc_tip'    => true,
            ],
            Gateway_Constants::OPTION_TENDOPAY_0PRC_INTEREST_ENABLED         => [
                'title'   => __('Enable 0% interest', 'tendopay'),
                'type'    => 'checkbox',
                'default' => 'no'
            ],
            Gateway_Constants::OPTION_TENDOPAY_CLIENT_ID                     => [
                'title'   => __('API Client ID', 'tendopay'),
                'type'    => 'text',
                'default' => ''
            ],
            Gateway_Constants::OPTION_TENDOPAY_CLIENT_SECRET                 => [
                'title'   => __('API Client Secret', 'tendopay'),
                'type'    => 'password',
                'default' => ''
            ],
            Gateway_Constants::OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_LOCATION => [
                'title'   => sprintf(
                    __('"Or as low as %s/installment" label', 'tendopay'),
                    get_woocommerce_currency_symbol()
                ),
                'type'    => 'select',
                'default' => Gateway_Constants::OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_DEFAULT_LOCATION,
                'options' => [
                    'no'                                       => 'Disabled',
                    'woocommerce_before_single_product'        => 'Before the product title',
                    'woocommerce_single_product_summary'       => 'After the product title',
                    'woocommerce_before_add_to_cart_form'      => 'After the price and short description',
                    'woocommerce_before_add_to_cart_button'    => 'Before the add to cart button',
                    Gateway_Constants::OPTION_TENDOPAY_EXAMPLE_INSTALLMENTS_DEFAULT_LOCATION
                                                               => 'After the add to cart button (default)',
                    'woocommerce_product_meta_start'           => 'Before the product meta data section',
                    'woocommerce_product_meta_end'             => 'After the product meta data section',
                    'woocommerce_after_single_product_summary' => 'Before the product long description section',
                    'woocommerce_after_single_product'         => 'After the product long description section and related products list',
                ]
            ],
            Gateway_Constants::OPTION_TENDOPAY_DISABLE_PRODUCT_LISTING_EXAMPLE_INSTALLMENTS => [
                'title' => __('Disable example installments on products listings', 'tendopay'),
                'type' => 'checkbox',
                'default' => 'no'
            ],
            Gateway_Constants::OPTION_TENDOPAY_CUSTOMIZED_PAYMENT_METHOD_OPTION_DISABLED               => [
                'title'       => __('Disable customized payment method option in checkout', 'tendopay'),
                'description' => __(
                    'Check this checkbox if you\'re experiencing issues with TendoPay\'s customized payment method'
                    . ' option in checkout',
                    'tendopay'
                ),
                'type'        => 'checkbox',
                'default'     => 'no',
                'desc_tip'    => true,
            ],
        ];
    }

    /**
     * Processes the payment. This method is called right after customer clicks the `Place order` button.
     *
     * @param int $order_id ID of the order that customer wants to pay.
     *
     * @return array status of the payment and redirect url. The status is always `success` because if there was
     *         any problem, this method would fail due to unhandled exception thrown.
     *
     * @throws TendoPay_Integration_Exception
     */
    public function process_payment($order_id)
    {
        update_post_meta($order_id, Gateway_Constants::TENDOPAY_PAYMENT_INITIATED_KEY, true);

        $order = new WC_Order((int)$order_id);

        $paymentRequest = new PaymentRequestService();
        $redirectURL = $paymentRequest->initialize($order);

        return [
            'result'   => 'success',
            'redirect' => $redirectURL
        ];
    }

    /**
     * Processes and saves options.
     * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
     * Additionally it removes the TendoPay bearer token, because some changes may cause it to be invalid.
     *
     * @return bool was anything saved?
     */
    public function process_admin_options()
    {
        delete_option('tendopay_bearer_token');

        return parent::process_admin_options();
    }
}
