<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Authorize extends \Az2009\Cielo\Model\Method\Transaction
{
    /**
     * process the authorization
     * @return $this
     */
    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);

        if (!$payment->getTransactionId()) {
            $paymentId = $this->getBody()->Payment->PaymentId;
            $payment->setTransactionId($paymentId)
                    ->setLastTransId($paymentId);
            $payment->setAdditionalInformation(
                'transaction_authorization',
                $paymentId
            );
        }

        $this->prepareBodyTransaction($bodyArray);

        $payment->setTransactionAdditionalInfo(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $this->getTransactionData()
        );

        $payment->setIsTransactionClosed(false);

        return $this;
    }

}