<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

use Braintree\Exception;

class Capture extends \Az2009\Cielo\Model\Method\Transaction
{

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $session,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $comment,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        parent::__construct($session, $comment, $data);
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

        $payment->getOrder()
                ->setStatus($this->helper->getStatusPay());

        $this->saveCardToken();

        if ($this->getPostback()) {
            $payment->registerCaptureNotification($this->_getCapturedAmount());
            $payment->getOrder()->save();
        }

        return $this;
    }

    protected function _getCapturedAmount()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        if (!isset($bodyArray['Payment']['CapturedAmount'])
            || !($capturedAmount = floatval($bodyArray['Payment']['CapturedAmount']))
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