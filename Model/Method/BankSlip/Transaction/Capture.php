<?php

namespace Az2009\Cielo\Model\Method\BankSlip\Transaction;

class Capture extends \Az2009\Cielo\Model\Method\Cc\Transaction\Capture
{
    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = null;

        if (!property_exists($this->getBody(), 'Payment') && !$payment->getLastTransId()) {
            throw new \Az2009\Cielo\Exception\Cc(__('Payment not authorized'));
        } elseif(property_exists($this->getBody(), 'Payment')) {
            $paymentId = $this->getBody()->Payment->PaymentId;
        }

        if (empty($paymentId) && !$payment->getLastTransId()) {
            throw new \Az2009\Cielo\Exception\Cc(__('Payment not authorized'));
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

        if ($payment->getCapturePartial()) {
            $this->messageManager->addNotice(
                __('*Obs: To capture partial: 
                    Cielo only supports one partial or full capture. 
                    On the next capture for this request. 
                    Capture offline at the store and online at Cielo\'s backoffice.')
            );
        }

        $this->saveCardToken();

        if ($this->getPostback() && ($payment->getAmountPaid() != $payment->getAmountAuthorized())) {
            $payment->registerCaptureNotification($this->_getCapturedAmount());
            $payment->getOrder()->save();
        }

        return $this;
    }

    protected function _getCapturedAmount()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($bodyArray['Payment']['Amount'])
            || !($capturedAmount = floatval($bodyArray['Payment']['Amount']))
        ) {
            throw new \Exception(
                __(
                    'Not exists values to capture in order %1',
                    $this->getPayment()->getOrder()->getId()
                )
            );
        }

        return $capturedAmount;
    }
}