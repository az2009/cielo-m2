<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Pending extends \Az2009\Cielo\Model\Method\Transaction
{
    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $comment,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($session, $comment, $data);
    }

    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = '';

        if (property_exists($this->getBody(), 'Payment')) {
            $paymentId = $this->getBody()->Payment->PaymentId;
            $paymentId .= '-order';
        }

        if (!$payment->getTransactionId() && !empty($paymentId)) {
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

        $payment->setIsTransactionClosed(true);
        $payment->setIsTransactionPending(true);
        $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_ORDER);
        $payment->setTransactionId(null);

        return $this;
    }

}