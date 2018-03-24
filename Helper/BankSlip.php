<?php

namespace Az2009\Cielo\Helper;

class BankSlip extends Data
{
    /**
     * get additional days for the ticket to expire
     */
    public function getAdditionalDays()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_bank_slip/additional_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $config = intval($config);
        $config = "P{$config}D";

        return $config;
    }

    /**
     * demonstration Text.
     * @return mixed
     */
    public function getDemonstrative()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_bank_slip/demonstrative',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    /**
     * name of the Seller.
     * @return mixed
     */
    public function getAssignor()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_bank_slip/assignor',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    /**
     * ticket number sent by the shopkeeper. Used to count tickets issued ("OurNumber").
     */
    public function getBoletoNumber()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_bank_slip/boleto_number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    /**
     * get Assignor Address
     * @return mixed
     */
    public function getAssignorAddress()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_bank_slip/assignor_address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    /**
     * get provider Bradesco2 | BancoDoBrasil2
     * @return mixed
     */
    public function getProvider()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo_bank_slip/provider',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }
}