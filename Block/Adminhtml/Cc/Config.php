<?php

namespace Az2009\Cielo\Block\Adminhtml\Cc;

class Config extends \Magento\Payment\Block\Form
{
    /**
     * @var \Az2009\Cielo\Model\CieloConfigProvider
     */
    protected $config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Az2009\Cielo\Model\CieloConfigProvider $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    public function getPaymentConfig()
    {
        return json_encode($this->config->getConfig());
    }
}