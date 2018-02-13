<?php

namespace Az2009\Cielo\Model\Method\Cc\Request;

use Magento\Framework\Exception\LocalizedException;

class Request extends \Magento\Framework\DataObject
{

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Payment
     */
    protected $payment;


    public function __construct(
        array $data = [],
        Customer $customer,
        Payment $payment
    ) {
        $this->customer = $customer;
        $this->payment = $payment;
        parent::__construct($data);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function buildRequest()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getPayment()->getOrder();

        if (!($this->getPayment() instanceof \Magento\Sales\Model\Order\Payment)
                || !(($order = $this->getPayment()->getOrder())
                        instanceof \Magento\Sales\Model\Order)) {
            throw new LocalizedException(__('Instance Invalid'));
        }

        $this->setOrder($order);
        $extra = array('MerchantOrderId' => $this->getOrder()->getIncrementId());
        $customer = $this->getCustomer();
        $payment = $this->getPayment();

        return $this->setData(array_merge($extra,$customer,$payment))
                    ->toArray();
    }

    /**
     * @return array
     */
    public function getCustomer()
    {
        return $this->customer
                    ->setOrder($this->getOrder())
                    ->getRequest();
    }

    /**
     * @return array
     */
    public function getPayment()
    {
        return $this->payment
                    ->setOrder($this->getOrder())
                    ->getRequest();
    }
}