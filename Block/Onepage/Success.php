<?php

namespace Az2009\Cielo\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_order = $order;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
    }

    public function getOrder()
    {
        $order = $this->_checkoutSession->getLastRealOrder();

        if ((int)$order->getId() <= 0) {
            return false;
        }
        $this->registry->register('current_order', $order);
        return $order;
    }

    public function getInfo(\Magento\Sales\Model\Order $order)
    {
        $output = '';
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $infoBlock = $method->getInfoBlockType();

        if (empty($infoBlock)) {
            return $output;
        }

        $info = $this->_objectManager->get($infoBlock);

        if (!($info instanceof \Magento\Payment\Block\Info)) {
            return $output;
        }

        $info->setData('info', $method->getInfoInstance());

        $output =  $info->toHtml();

        return $output;
    }
}