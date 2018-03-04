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
            'payment/az2009_cielo/merchant_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getMerchantKey()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/merchant_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getRequestUriStage()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/uri_request_stage'
        );

        return $config;
    }

    public function getUriQueryStage()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/uri_query_stage'
        );

        return $config;
    }

    public function getStatusPending()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/order_status_pending',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getStatusPay()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/order_status_pay',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getCardTypesAvailable()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/cctypes',
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