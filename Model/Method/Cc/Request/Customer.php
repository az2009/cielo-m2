<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Az2009\Cielo\Model\Method\Cc\Request;

class Customer extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Sales\Model\Order\Address
     */
    protected $billingAddress;

    /**
     * @var \Magento\Sales\Model\Order\Shipping
     */
    protected $shippingAddress;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @return string
     */
    public function getRequest()
    {
        $this->order = $this->getOrder();
        $this->billingAddress = $this->order->getBillingAddress();
        $this->shippingAddress = $this->order->getShippingAddress();

        $this->setData(
            'Customer',
            [
                'Name' => $this->billingAddress->getFirstname(),
                'Identity' => $this->billingAddress->getIdentify(),
                'IdentityType' => $this->billingAddress->getIdentityType(),
                'Email' => $this->order->getCustomerEmail(),
                'Address' => $this->getBillingAddress(),
                'DeliveryAddress' => $this->getShippingAddress(),
            ]
        )->toArray();
    }

    /**
     * @return array
     */
    public function getBillingAddress()
    {
        return  [
                    'Street' => $this->billingAddress->getStreetLine(1),
                    'Number' => $this->billingAddress->getStreetLine(2),
                    'Complement' => $this->billingAddress->getStreetLine(3),
                    'ZipCode' => $this->billingAddress->getPostcode(),
                    'City' => $this->billingAddress->getCity(),
                    'State' => $this->billingAddress->getRegion(),
                    'Country' => $this->billingAddress->getCountryId(),
                ];
    }

    /**
     * @return array
     */
    public function getShippingAddress()
    {
        return [
                   'Street' => $this->shippingAddress->getStreetLine(1),
                   'Number' => $this->shippingAddress->getStreetLine(2),
                   'Complement' => $this->shippingAddress->getStreetLine(3),
                   'ZipCode' => $this->shippingAddress->getPostcode(),
                   'City' => $this->shippingAddress->getCity(),
                   'State' => $this->shippingAddress->getRegion(),
                   'Country' => $this->shippingAddress->getCountryId(),
               ];
    }

}