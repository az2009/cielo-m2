<?php

namespace Az2009\Cielo\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_asset;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Asset\Repository $asset
    ) {
        $this->_asset = $asset;
        parent::__construct($context);
    }

    public function getMerchantId()
    {
        $config = $this->scopeConfig->getValue(
            'payment/general/merchant_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getMerchantKey()
    {
        $config = $this->scopeConfig->getValue(
            'payment/general/merchant_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getImgUrlCard($code)
    {
        return $this->_asset->getUrl('Az2009_Cielo::images/');
    }

    public function getCardTypesAvailable()
    {
        $config = $this->scopeConfig->getValue(
            'payment/general/cctypes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $config = explode(',', $config);

        return $config;
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