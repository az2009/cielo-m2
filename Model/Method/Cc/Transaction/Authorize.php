<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Authorize extends \Az2009\Cielo\Model\Method\Transaction
{

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $comment,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_registry = $registry;
        parent::__construct($session, $comment, $data);
    }

    /**
     * process the authorization
     * @return $this
     */
    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);

        if (!property_exists($this->getBody(), 'Payment')) {
            throw new \Az2009\Cielo\Exception\Cc(__('Payment not authorized'));
        }

        $paymentId = $this->getBody()->Payment->PaymentId;

        if (empty($paymentId)) {
            throw new \Az2009\Cielo\Exception\Cc(__('Payment not authorized'));
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

        $payment->setIsTransactionClosed(false);

        $this->saveCardToken();

        if ($this->getPostback()) {
            $payment->registerAuthorizationNotification($this->_getAuthorizedAmount());
            $payment->getOrder()
                    ->save();
        }

        return $this;
    }

    protected function _getAuthorizedAmount()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($bodyArray['Payment']['Amount'])
            || !($authorizeAmount = doubleval($bodyArray['Payment']['Amount']))
        ) {
            throw new \Exception(
                __(
                    'Not exists values to authorize in order %1',
                    $this->getPayment()->getOrder()->getId()
                )
            );
        }

        return $authorizeAmount;
    }

}