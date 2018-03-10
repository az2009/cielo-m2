<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Az2009\Cielo\Model\Method\Cc\Request;

class Customer extends \Magento\Framework\DataObject
{
    const CPF = 'CFP';

    const CNPJ = 'CNPJ';

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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Az2009\Cielo\Helper\Data $helper,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
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
        $this->shippingAddress = $this->order->getShippingAddress();

        $payment = $this->order
                        ->getPayment()
                        ->getMethodInstance();

        $this->setPayment($payment);

        return $this->setData(
                        [
                            'Customer' => [
                                'Name' => $this->helper->substr($this->billingAddress->getFirstname(), 255, 0),
                                'Identity' => $this->helper->substr($this->getIdentity(), 14, 0),
                                'IdentityType' => $this->isCpfCnpj($this->getIdentity()),
                                'Email' => $this->helper->substr($this->order->getCustomerEmail(), -255),
                                'Address' => $this->getBillingAddress(),
                                'DeliveryAddress' => $this->getShippingAddress(),
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
                    'Street' => $this->helper->substr($this->billingAddress->getStreetLine(1), 255, 0),
                    'Number' => $this->helper->substr($this->billingAddress->getStreetLine(2), 15, 0),
                    'Complement' => $this->helper->substr($this->getComplement(), 50, 0),
                    'ZipCode' => $this->helper->substr($this->billingAddress->getPostcode(), 9, 0),
                    'City' => $this->helper->substr($this->billingAddress->getCity(), 50, 0),
                    'Country' => $this->helper->substr($this->billingAddress->getCountryId(), 35, 0),
                ];
    }

    /**
     * @return array
     */
    public function getShippingAddress()
    {
        return [
                   'Street' => $this->helper->substr($this->shippingAddress->getStreetLine(1), 255, 0),
                   'Number' => $this->helper->substr($this->shippingAddress->getStreetLine(2), 15, 0),
                   'Complement' => $this->helper->substr($this->getComplement(), 50, 0),
                   'ZipCode' => $this->helper->substr($this->shippingAddress->getPostcode(), 9, 0),
                   'City' => $this->helper->substr($this->shippingAddress->getCity(), 50, 0),
                   'Country' => $this->helper->substr($this->shippingAddress->getCountryId(), 35, 0),
               ];
    }

    /**
     * prepare complement
     * @param \Magento\Sales\Model\Order\Address $address
     * @return string
     */
    public function getComplement()
    {
        $complement = array(
            $this->billingAddress->getStreetLine(3),
            $this->billingAddress->getStreetLine(4)
        );

        return implode(',', $complement);
    }

    /**
     * check if doc is cpf or cnpj
     * @param $doc
     * @return string
     */
    protected function isCpfCnpj($doc)
    {
        $doc = preg_replace('/[^0-9]/', '', $doc);
        if (strlen($doc) > 11) {
            return Customer::CNPJ;
        }

        return Customer::CPF;
    }

    /**
     * get identity of customer
     * @return mixed
     */
    public function getIdentity()
    {
        $attributeCode = $this->getPayment()
                              ->getConfigData('attribute_identity');

        $identity = $this->_customerSession
                         ->getCustomer()
                         ->getData($attributeCode);
        $identity = preg_replace('/[^0-9]/', '', $identity);
        return $identity;
    }

}