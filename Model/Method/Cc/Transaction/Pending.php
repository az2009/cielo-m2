<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Pending extends \Az2009\Cielo\Model\Method\Transaction
{
    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;


    public function __construct(\Az2009\Cielo\Helper\Data $helper, array $data = [])
    {
        $this->helper = $helper;
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

        $payment->getOrder()
                ->setStatus($this->helper->getStatusPending());

        $payment->setIsTransactionClosed(false)
                ->setIsTransactionPending(true);

        return $this;
    }

}