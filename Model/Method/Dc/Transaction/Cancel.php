<?php

namespace Az2009\Cielo\Model\Method\Dc\Transaction;

class Cancel extends \Az2009\Cielo\Model\Method\Cc\Transaction\Cancel
{
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $comment,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($session, $comment, $registry, $data);
    }

    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = '';
        $order = $payment->getOrder();

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

        if ($this->_registry->registry('process_fetch_update_payment')) {
            $payment->setIsTransactionDenied(true);
            return $this;
        }

        if ($order->canCreditmemo()) {
            $payment->registerRefundNotification($this->_getVoidedAmount());

        } elseif ($order->canCancel()) {
            $payment->registerVoidNotification($this->_getVoidedAmount());
            $order->registerCancellation()->save();

        }  elseif($order->isPaymentReview()) {
            $payment->setTransactionId($payment->getLastTransId() . '-void');
            $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID);
            $payment->deny();
        }

        $this->addReturnMessageToTransaction($bodyArray);
        $order->save();

        return $this;
    }

    protected function _getVoidedAmount()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($bodyArray['Payment']['Amount'])
            || !($authorizeAmount = doubleval($bodyArray['Payment']['Amount']))
        ) {
            $authorizeAmount = $this->getPayment()->getAmount();
            if ($this->getPayment()->getActionCancel() && (int)$this->getPayment()->getAmount() <= 0) {
                $authorizeAmount = $this->getPayment()->getAmountPaid() ?: $this->getPayment()->getAmountAuthorized();
            }
        }

        return $authorizeAmount;
    }
}