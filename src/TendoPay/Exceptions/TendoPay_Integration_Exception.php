<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 04.08.2018
 * Time: 13:25
 */

namespace TendoPay\Exceptions;

if (! defined('ABSPATH')) {
    die();
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Class TendoPay_Integration_Exception
 * @package TendoPay\Exceptions
 */
class TendoPay_Integration_Exception extends \Exception
{
    /**
     * TendoPay_Integration_Exception constructor.
     */
    public function __construct($message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->report($message, $previous);
    }


    private function report($message, $previous = null)
    {
        $trace = '';
        if ($previous) {
            ob_start();
            print_r($previous) . PHP_EOL;
            $raw = ob_get_contents();
            ob_end_clean();


            $trace = (preg_match('/\[string:Exception:private\] => ([^#]+)/s', $raw, $match))
                ? $match[1] . PHP_EOL . $previous->getTraceAsString()
                : $previous->getTraceAsString();
        }

        $info = self::getActivePlugins($message);

        $backtrace = self::getBackTrace($message, explode("\n", $trace));

        self::sendReport($backtrace);
    }

    private static function getActivePlugins($message = null)
    {
        global $woocommerce;
        $info           = [ $message ];
        $info[]         = 'woocommerce_version:' . $woocommerce->version;
        $info[]         = 'active_plugins:';
        $active_plugins = array_reduce(
            get_option('active_plugins'),
            function ($hash, $item) {
                $hash[ md5($item) ] = 1;

                return $hash;
            },
            []
        );

        foreach (get_plugins() as $key => $item) {
            if (isset($active_plugins[ md5($key) ])) {
                $info[] = $item['Name'] . ': v' . $item['Version'];
            }
        }

        return $info;
    }

    public static function getBackTrace(string $message, array $trace = [])
    {
        $backtrace = array(
            'site' => get_site_url(),
            'message' => self::getActivePlugins($message),
            'trace'   => $trace,
        );

        return $backtrace;
    }

    public static function sendReport($backtrace)
    {
        try {
        } catch (\Exception $e) {
            $logger = wc_get_logger();
            $logger->debug(json_encode($backtrace));
        }
    }
}
