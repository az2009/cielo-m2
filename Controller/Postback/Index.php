<?php

namespace Az2009\Cielo\Controller\Postback;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Az2009\Cielo\Helper\Data $helper,
        \Az2009\Cielo\Model\CieloConfigProvider $config
    ) {
        $this->helper = $helper;
        $this->config = $config;
        parent::__construct($context);
    }

    public function execute()
    {

        $r = var_export($this->config->getIcons(), true);

        return $this->getResponse()->setBody($r);
    }
}