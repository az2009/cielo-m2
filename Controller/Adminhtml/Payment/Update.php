<?php

namespace Az2009\Cielo\Controller\Adminhtml\Payment;

class Update extends AbstractController
{
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {

            $order = $this->_order->load($orderId);

            if (!$order->getId()) {
                throw new \Exception((string)__('Order %1 Not Found', $orderId));
            }

            $paymentId = $order->getPayment()->getLastTransId();
            $postback = $this->helper->getPostbackByTransId($paymentId);

            if (!($postback instanceof \Az2009\Cielo\Model\Method\AbstractMethod)) {
                throw new \Exception((string)__('Order Not Found to PaymentId %1', $paymentId));
            }

            $postback->setPaymentId($paymentId)
                     ->process();

            $this->helper->getMessage()->addSuccess(__('Update Payment'));

        } catch (\Exception $e) {
            $this->helper->getMessage()->addError($e->getMessage());
        }

        return $this->_redirect('sales/order/view/', ['order_id' => $orderId]);
    }
}