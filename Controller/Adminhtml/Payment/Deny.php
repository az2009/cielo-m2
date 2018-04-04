<?php

namespace Az2009\Cielo\Controller\Adminhtml\Payment;

class Deny extends AbstractController
{
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {

            $order = $this->_order->load($orderId);

            /** @var \Magento\Sales\Model\Order\Payment $payment*/
            $payment = $order->getPayment();

            if (!$order->getId()) {
                throw new \Exception((string)__('Order %1 Not Found', $orderId));
            }

            if ($order->hasInvoices()) {
                /** @var \Magento\Sales\Model\Order\Invoice $invoice*/
                foreach ($order->getInvoiceCollection() as $invoice) {
                    $this->cancelInvoice($invoice);
                }
            }

            $order->registerCancellation();

            if (!$order->isCanceled()) {
                throw new \Exception('Occurred an error during cancellation of order');
            }

            $transactionId = $payment->getLastTransId();
            $payment->setParentTransactionId($transactionId);
            $transactionId .= '-deny-offline';
            $payment->setTransactionId($transactionId);

            $user = $this->_auth->getUser()->getUsername();

            $payment->setTransactionAdditionalInfo(
                \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                ['User' => $user]
            );

            $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID);

            $order->addStatusHistoryComment(__('Order Denied Offline/Manual. Denied by User %1', $user));
            $order->save();

            $this->helper->getMessage()->addSuccess(__('Order Denied Offline'));

        } catch (\Exception $e) {
            $this->helper->getMessage()->addError($e->getMessage());
        }

        return $this->_redirect('sales/order/view/', ['order_id' => $orderId]);
    }

    protected function cancelInvoice(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        if ($invoice->isCanceled() || !$invoice->canCancel()) {
            return $this;
        }

        $invoice->cancel();
        $this->_objectManager->create(
            \Magento\Framework\DB\Transaction::class
        )->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        )->save();

    }
}