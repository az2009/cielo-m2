<?php

namespace Az2009\Cielo\Controller\Adminhtml\Payment;

class Authorize extends AbstractController
{
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {

            $order = $this->_order->load($orderId);

            if (!$order->getId()) {
                throw new \Exception((string)__('Order %1 Not Found', $orderId));
            }

            if ($order->isPaymentReview() ) {
                $payment = $order->getPayment();
                $transactionId = $payment->getLastTransId();
                $payment->setParentTransactionId($transactionId);
                $transactionId .= '-capture-offline';
                $payment->setTransactionId($transactionId);

                $user = $this->_auth->getUser()->getUsername();

                $payment->setTransactionAdditionalInfo(
                    \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                    ['User' => $user]
                );

                $payment->registerCaptureNotification($payment->getAmountAuthorized());
                $order->addStatusHistoryComment(__('Order Captured Offline/Manual. Captured by User %1', $user));
                $order->save();
            }

            $this->helper->getMessage()->addSuccess(__('Order Captured Offline'));

        } catch (\Exception $e) {
            $this->helper->getMessage()->addError($e->getMessage());
        }

        return $this->_redirect('sales/order/view/', ['order_id' => $orderId]);
    }
}