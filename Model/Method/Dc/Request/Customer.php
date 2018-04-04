<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Az2009\Cielo\Model\Method\Dc\Request;

class Customer extends \Magento\Framework\DataObject
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Az2009\Cielo\Helper\Dc
     */
    protected $helper;

    public function __construct(
        \Az2009\Cielo\Helper\Dc $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        $this->order = $this->getOrder();
        $this->billingAddress = $this->order->getBillingAddress();

        $payment = $this->order
                        ->getPayment()
                        ->getMethodInstance();

        $info = $payment->getInfoInstance();

        $this->setInfo($info);
        $this->setPayment($payment);

        return $this->setData(
                        [
                            'Customer' => [
                                'Name' => $this->helper->prepareString($this->billingAddress->getFirstname(), 34, 0),
                            ]
                        ]
                    )->toArray();
    }

}