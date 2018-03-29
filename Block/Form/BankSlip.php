<?php

namespace Az2009\Cielo\Block\Form;

class BankSlip extends \Magento\Payment\Block\Form
{
    /**
     * get code of method payment
     * @return string
     */
    public function getCode()
    {
        return \Az2009\Cielo\Model\Method\BankSlip\BankSlip::CODE_PAYMENT;
    }
}