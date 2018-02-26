<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

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
        \Az2009\Cielo\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        parent::__construct($data);
    }

    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = '';

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

        if ($payment->getCapturePartial()) {
            $this->messageManager->addNotice(
                __('
                *Obs: To capture partial: 
                    Cielo only supports one partial or full capture. 
                    On the next capture for this request. 
                    Capture offline at the store and online at Cielo\'s backoffice.'
                )
            );
        }

        $payment->getOrder()
                ->setStatus($this->helper->getStatusPay());

        return $this;
    }

}