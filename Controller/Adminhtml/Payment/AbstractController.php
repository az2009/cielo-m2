<?php

namespace Az2009\Cielo\Controller\Adminhtml\Payment;

abstract class AbstractController extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Framework\App\State $state
    ) {
        $this->_order = $order;
        $this->helper = $helper;
        $this->state = $state;
        parent::__construct($context);
    }

}