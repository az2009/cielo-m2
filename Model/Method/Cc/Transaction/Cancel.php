<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Cancel extends \Az2009\Cielo\Model\Method\Transaction
{

    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = '';
        $order = $payment->getOrder();

        if (!property_exists($this->getBody(), 'Payment') && !$payment->getLastTransId()) {
            throw new \Az2009\Cielo\Exception\Cc(_('Payment not authorized'));
        } elseif(property_exists($this->getBody(), 'Payment')) {
            $paymentId = $this->getBody()->Payment->PaymentId;
        }

        if (empty($paymentId) && !$payment->getLastTransId()) {
            throw new \Az2009\Cielo\Exception\Cc(_('Payment not authorized'));
        }

        //check if is the first capture of order
        if (!$payment->getLastTransId() && !empty($paymentId)) {
            $payment->setTransactionId($paymentId)
                    ->setLastTransId($paymentId);
        } else {
            $payment->setParentTransactionId(
                $payment->getAdditionalInformation('transaction_authorization')
            );
        }

        $this->prepareBodyTransaction($bodyArray);

        $payment->setTransactionAdditionalInfo(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $this->getTransactionData()
        );

        $payment->setIsTransactionClosed(true);

        if ($order->canCancel()) {
            $payment->registerVoidNotification($this->_getVoidedAmount());
            $order->registerCancellation()
                  ->save();
        }

        if ($order->canCreditmemo()) {
            $payment->registerRefundNotification($this->_getVoidedAmount());
            $order->save();
        }

        return $this;
    }

    protected function _getVoidedAmount()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($bodyArray['Payment']['VoidedAmount'])
            || !($authorizeAmount = doubleval($bodyArray['Payment']['VoidedAmount']))
        ) {
            throw new Exception(
                __(
                    'not exists values to void in order %1',
                    $this->getPayment()->getOrder()->getId()
                )
            );
        }

        return $authorizeAmount;
    }
}