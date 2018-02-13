<?php

namespace Az2009\Cielo\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getMerchantId()
    {

    }

    public function getMerchantKey()
    {

    }

    public function getKeyRequest()
    {
        $key = urlencode(mt_rand(0, 999) .
               mt_rand(1000, 1999) .
               time() .
               $_SERVER['SERVER_ADDR']);

        return $key;
    }
}