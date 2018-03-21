<?php

namespace Az2009\Cielo\Block\Form;

class Cc extends \Magento\Payment\Block\Form\Cc
{
    /**
     * @var \Az2009\Cielo\Model\CieloConfigProvider
     */
    public $config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Az2009\Cielo\Model\CieloConfigProvider $config,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->config = $config;
    }

    /**
     * get the flags availables
     * @return array|mixed
     */
    public function getCcAvailableTypes()
    {
        return $this->config->getCcAvailableTypes();
    }

    /**
     * get flag image of card
     * @param $code
     * @return \stdClass
     */
    public function getIcon($code)
    {
        return $this->config->getIconByCode($code);
    }

    /**
     * get code of method payment
     * @return string
     */
    public function getCode()
    {
        return \Az2009\Cielo\Model\Method\Cc\Cc::CODE_PAYMENT;
    }

}