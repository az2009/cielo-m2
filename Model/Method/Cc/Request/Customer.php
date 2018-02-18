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


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
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
                                'Name' => $this->billingAddress->getFirstname(),
                                'Identity' => $this->getIdentity(),
                                'IdentityType' => $this->isCpfCnpj($this->getIdentity()),
                                'Email' => $this->order->getCustomerEmail(),
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
                    'Street' => $this->billingAddress->getStreetLine(1),
                    'Number' => $this->billingAddress->getStreetLine(2),
                    'Complement' => $this->getComplement(),
                    'ZipCode' => $this->billingAddress->getPostcode(),
                    'City' => $this->billingAddress->getCity(),
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
                   'Country' => $this->shippingAddress->getCountryId(),
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

        return $identity;
    }

}