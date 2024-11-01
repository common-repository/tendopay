<?php

namespace TendoPay\API\V2;

use Exception;
use InvalidArgumentException;
use TendoPay\SDK\Models\Transaction;
use TendoPay\SDK\Models\VerifyTransactionRequest;
use TendoPay\SDK\V2\TendoPayClient;
use UnexpectedValueException;

class TransactionVerificationService
{
    /** @var TendoPayClient */
    private $apiClient;

    public function __construct(TendoPayClient $apiClient = null)
    {
        if (empty($apiClient)) {
            $apiClientFactory = new TendoPayApiClientFactory();
            $this->apiClient = $apiClientFactory->createClient();
        } else {
            $this->apiClient = $apiClient;
        }
    }

    /**
     * @param $requestParams
     *
     * @return Transaction
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    public function getVerifiedTransaction($requestParams)
    {
        if ($this->apiClient::isCallbackRequest($requestParams)) {
            $transaction = $this->apiClient->verifyTransaction(new VerifyTransactionRequest($requestParams));

            if ( ! $transaction->isVerified()) {
                throw new UnexpectedValueException('Invalid signature for the verification');
            }

            return $this->apiClient->getTransactionDetail($transaction->getTransactionNumber());
        } else {
            throw new InvalidArgumentException("This is not a valid payment request callback!");
        }
    }
}