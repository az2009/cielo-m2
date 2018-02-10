<?php

namespace Az2009\Cielo\Model\Method;

class AbstractPaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    public function validate()
    {

    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

    }

    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {

    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {

    }
}