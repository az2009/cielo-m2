<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Az2009\Cielo\Model\Method\BankSlip\Request;

class Customer extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Sales\Model\Order\Address
     */
    protected $billingAddress;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Az2009\Cielo\Helper\BankSlip
     */
    protected $helper;

    public function __construct(
        \Az2009\Cielo\Helper\BankSlip $helper,
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
                                'Identity' => $this->helper->prepareString($info->getAdditionalInformation('bs_identification'), 14, 0),
                                'Address' => $this->getBillingAddress()
                            ]
                        ]
                    )->toArray();
    }

    /**
     * @return array
     */
    public function getBillingAddress()
    {
        return  [
                    'Street' => $this->helper->prepareString($this->billingAddress->getStreetLine(1), 70, 0),
                    'Number' => $this->helper->prepareString($this->billingAddress->getStreetLine(2), 10, 0),
                    'District' => $this->helper->prepareString($this->billingAddress->getStreetLine(4), 50, 0),
                    'Complement' => $this->helper->prepareString($this->billingAddress->getStreetLine(3), 20, 0),
                    'ZipCode' => $this->helper->prepareString($this->billingAddress->getPostcode(), 9, 0),
                    'City' => $this->helper->prepareString($this->billingAddress->getCity(), 18, 0),
                    'State' => $this->helper->prepareString($this->billingAddress->getRegionCode(), 2, 0),
                    'Country' => $this->helper->prepareString($this->billingAddress->getCountryId(), 35, 0),
                ];
    }

}