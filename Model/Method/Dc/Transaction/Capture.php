<?php

namespace Az2009\Cielo\Model\Method\Dc\Transaction;

class Capture extends \Az2009\Cielo\Model\Method\Cc\Transaction\Capture
{

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $session,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $comment,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct(
            $messageManager, $session, $helper,
            $comment, $registry, $data
        );
    }

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

        if ($this->isCompleteCaptured()) {
            $this->_registry->register('payment_captured', true);
            return $this;
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

        $this->addReturnMessageToTransaction($bodyArray);

        if ($this->getPostback() && !$this->isCompleteCaptured()) {
            $payment->registerCaptureNotification($this->_getCapturedAmount());
            $payment->getOrder()->save();
        }

        $this->_registry->register('payment_captured', true);
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