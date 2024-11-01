<?php
/*
Plugin Name: TendoPay
Description: TendoPay is a 'Buy now. Pay later' financing platform for online shopping. This plugin allows your ecommerce site to use TendoPay as a payment method.
Version:     3.2.0
Author:      TendoPay
Author URI:  http://tendopay.ph/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

use TendoPay\TendoPay;
use TendoPay\Utils;
use TendoPay\Exceptions\TendoPay_Integration_Exception;

defined('ABSPATH') or die('No script kiddies please!');
define('TENDOPAY_ROOT_FILE', __FILE__);
define('TENDOPAY_BASEPATH', WP_PLUGIN_DIR . '/' . basename(__DIR__));
define('TENDOPAY_BASEURL', WP_PLUGIN_URL . '/' . basename(__DIR__));

if (! defined('TENDOPAY')) {
    define('TENDOPAY', true);

    require_once "vendor/autoload.php";

    function tendopay_fatal_error()
    {
        $error = error_get_last();
        $trace = [];
        if (is_array($error)) {
            foreach ($error as $k => $v) {
                if ($k == 'message') {
                    $trace = array_merge($trace, explode("\n", $v));
                } else {
                    $trace[] = $k . ':' . $v;
                }
            }
        }
        if (empty($trace)) {
            return;
        }
        $backtrace = TendoPay_Integration_Exception::getBackTrace('Fatal Error', $trace);
        TendoPay_Integration_Exception::sendReport($backtrace);
    }

    register_shutdown_function('tendopay_fatal_error');


    /**
     * The main function responsible for plugin's initialization.
     * You can access the plugin simply by using <?php $tendopay = tendopay(); ?>
     */
    function tendopay()
    {
        return TendoPay::get_instance();
    }

    add_action('woocommerce_init', 'tp_on_wc_init');
    function tp_on_wc_init() {
        if (Utils::emptyCredentials()) {
            add_action('admin_notices', [TendoPay::class, 'no_credentials_admin_notice']);
        }

        if (!Utils::isPhpCurrencyActive()) {
            add_action('admin_notices', [TendoPay::class, 'no_php_currency_admin_notice']);
        }

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [ TendoPay::class, 'add_settings_link' ]);
        add_filter('plugin_row_meta', [ TendoPay::class, 'add_plugin_row_meta_links' ], 10, 2);
        tendopay();
    }

    if (!Utils::is_woocommerce_active()) {
        add_action('admin_notices', [TendoPay::class, 'no_woocommerce_admin_notice']);
    }
}
