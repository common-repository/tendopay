<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 05.08.2018
 * Time: 05:59
 */

namespace TendoPay;

if (! defined('ABSPATH')) {
    die();
}

/**
 * Configuration class.
 *
 * @package TendoPay\API
 */
class Constants
{
    public const PAYMANET_FAILED_QUERY_PARAM = 'tendopay_payment_failed';

    public const BASE_API_URL = 'https://app.tendopay.ph';

    public const VIEW_URI_PATTERN = 'https://app.tendopay.ph/view/transaction/%s';

    public const SANDBOX_VIEW_URI_PATTERN = 'https://sandbox.tendopay.ph/view/transaction/%s';

    public const TENDOPAY_ICON = 'https://static.tendopay.dev/tendopay/logo-icon-32x32.jpg';
    public const TENDOPAY_LOGO_BLUE = TENDOPAY_BASEURL . '/assets/img/tp-logo-blue.svg';
    public const TENDOPAY_MARKETING = 'https://app.tendopay.ph/register';

    public const REPAYMENT_SCHEDULE_API_ENDPOINT_URI = "payments/api/v1/repayment-calculator?tendopay_amount=%s&payin4=%s";

    /**
     * Below constant names are used as keys of data send to or received from TP API
     */
    public const MESSAGE_PARAM = 'tp_message';
    public const ORDER_ID_PARAM = 'order_id';
    public const ORDER_KEY_PARAM = 'order_key';
    public const REPAYMENT_CALCULATOR_INSTALLMENT_AMOUNT = 'installment_amount';
    public const REPAYMENT_CALCULATOR_TOTAL_INSTALLMENTS = 'total_installments';
    public const TENDOPAY_COMPLETE_TERMS_URL = 'https://tendopay.ph/terms';

    /**
     * Gets the view uri pattern. It checks whether to use SANDBOX pattern or Production pattern.
     *
     * @return string view uri pattern
     */
    public static function get_view_uri_pattern()
    {
        return Utils::isSandboxEnabled() ? self::SANDBOX_VIEW_URI_PATTERN : self::VIEW_URI_PATTERN;
    }
}
