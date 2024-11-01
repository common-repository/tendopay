<?php

namespace TendoPay\API\V2;

use Exception;
use TendoPay\Exceptions\TendoPay_Integration_Exception;
use TendoPay\Logger;
use TendoPay\SDK\Models\Payment;
use TendoPay\SDK\V2\TendoPayClient;
use WC_Order;

class PaymentRequestService
{
    /** @var TendoPayClient */
    private $apiClient;
    /** @var Logger */
    private $logger;

    public function __construct(TendoPayClient $apiClient = null)
    {
        if (empty($apiClient)) {
            $apiClientFactory = new TendoPayApiClientFactory();
            $this->apiClient = $apiClientFactory->createClient();
        } else {
            $this->apiClient = $apiClient;
        }

        $this->logger = new Logger();
    }

    /**
     * @param WC_Order $order
     *
     * @return string
     * @throws TendoPay_Integration_Exception
     */
    public function initialize(WC_Order $order) {
        try {
            $payment = new Payment();
            $payment->setMerchantOrderId($order->get_id())
                    ->setDescription("Order #{$order->get_id()} - " . get_bloginfo('blogname'))
                    ->setRequestAmount($order->get_total())
                    ->setCurrency('PHP')
                    ->setRedirectUrl($this->get_redirect_url($order));

            $this->apiClient->setPayment($payment);

            return $this->apiClient->getAuthorizeLink();
        } catch (Exception $e) {
            $customException = new TendoPay_Integration_Exception(__('Could not communicate with TendoPay', 'tendopay'),
                $e);
            $this->logger->error($customException);
            $this->logger->error($e);
            throw $customException;
        }
    }

    private function get_redirect_url(WC_Order $order)
    {
        return apply_filters('tendopay_redirect_url', admin_url('admin-ajax.php?') . http_build_query([
                'action'    => 'tendopay-result',
                'order_id'  => $order->get_id(),
                'order_key' => $order->get_order_key()
            ]));
    }
}