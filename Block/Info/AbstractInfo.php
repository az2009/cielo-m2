<?php

namespace Az2009\Cielo\Block\Info;

class AbstractInfo extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    public $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Payment\Model\Config $paymentConfig, array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $paymentConfig, $data);
    }

    public function onlyShowAdmin()
    {
        if ($this->_appState->getAreaCode()
            == \Magento\Framework\App\Area::AREA_ADMINHTML
        ) {
            return true;
        }

        return false;
    }
}