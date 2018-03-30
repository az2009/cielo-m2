<?php

namespace Az2009\Cielo\Controller\Adminhtml\Payment;

class Deny extends AbstractController
{
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {

            $order = $this->_order->load($orderId);

            if (!$order->getId()) {
                throw new \Exception((string)__('Order %1 Not Found', $orderId));
            }

            $order->registerCancellation();
            $order->addStatusHistoryComment(__('Order Denied Offline/ Manual'));
            $order->save();

            $this->helper->getMessage()->addSuccess(__('Order Denied Offline'));

        } catch (\Exception $e) {
            $this->helper->getMessage()->addError($e->getMessage());
        }

        return $this->_redirect('sales/order/view/', ['order_id' => $orderId]);
    }
}