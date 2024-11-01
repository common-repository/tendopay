<?php

namespace TendoPay\API;

class RepaymentDetails
{
    private $singlePaymentAmount;
    private $numberOfPayments;

    /**
     * @param $singlePaymentAmount
     * @param $numberOfPayments
     */
    public function __construct($singlePaymentAmount, $numberOfPayments)
    {
        $this->singlePaymentAmount = $singlePaymentAmount;
        $this->numberOfPayments = $numberOfPayments;
    }

    /**
     * @return mixed
     */
    public function getSinglePaymentAmount()
    {
        return $this->singlePaymentAmount;
    }

    /**
     * @return mixed
     */
    public function getNumberOfPayments()
    {
        return $this->numberOfPayments;
    }
}