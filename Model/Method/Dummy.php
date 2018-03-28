<?php

namespace Az2009\Cielo\Model\Method;

class Dummy extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'az2009_cielo_core';

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return false;
    }

    public function isActive($storeId = null)
    {
        return false;
    }
}