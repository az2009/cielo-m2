<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Capture extends \Az2009\Cielo\Model\Method\Transaction
{

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;


    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        parent::__construct($data);
    }

    public function process()
    {
        $payment = $this->getPayment();
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);
        $paymentId = '';

        if (property_exists($this->getBody(), 'Payment')) {
            $paymentId = $this->getBody()->Payment->PaymentId;
        }

        if (!$payment->getLastTransId()) {
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

        return $this;
    }

}