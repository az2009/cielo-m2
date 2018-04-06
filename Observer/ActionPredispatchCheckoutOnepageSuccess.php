<?php
namespace Az2009\Cielo\Observer;

class ActionPredispatchCheckoutOnepageSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    public function __construct(
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->registry = $registry;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observe)
    {
        $request = $observe->getEvent()->getRequest();

        $order = $this->checkoutSession->getLastRealOrder();

        if (!$this->registry->registry('current_order')) {
            $this->registry->register('current_order', $order);
            $request->setParam('order_id', $order->getEntityId());
        }
    }
}