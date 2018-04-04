<?php

namespace Az2009\Cielo\Helper;

class Dc extends Data
{
    const URL_REDIRECT = 'cielo/process/payment/';

    /**
     * get return url to redirect after authentication in provider
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->_getUrl(self::URL_REDIRECT);
    }

    public function getCardTypesAvailable()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_dc/cctypes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $config = explode(',', $config);

        return $config;
    }
}