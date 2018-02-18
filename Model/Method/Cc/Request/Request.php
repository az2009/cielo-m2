<?php

namespace Az2009\Cielo\Model\Method\Cc\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ManagerInterface;

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

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;


    public function __construct(
        Customer $customer,
        Payment $payment,
        ManagerInterface $eventManager,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->payment = $payment;
        $this->_eventManager = $eventManager;
        parent::__construct($data);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function buildRequest()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getPaymentData()->getOrder();

        if (!($this->getPaymentData() instanceof \Magento\Sales\Model\Order\Payment)
                || !(($order = $this->getPaymentData()->getOrder())
                        instanceof \Magento\Sales\Model\Order)) {
            throw new LocalizedException(__('Instance Invalid'));
        }

        $this->setOrder($order);

        $request = $this->setData($this->merge());

        $this->_eventManager->dispatch(
            'after_prepare_request_params_cielo_cc',
            ['data_object' => $this]
        );

        return $request->toJson();
    }

    /**
     * merge all params
     * @return array
     */
    public function merge()
    {
        $merchantOrderId = $this->getMerchantOrderId();
        $customer = $this->getCustomer();
        $payment = $this->getPayment();
        return array_merge($merchantOrderId, $customer, $payment);
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

    /**
     * @return array
     */
    public function getMerchantOrderId()
    {
        return  [
            'MerchantOrderId' => $this->getOrder()->getIncrementId()
        ];
    }
}