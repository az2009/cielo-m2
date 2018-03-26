<?php

namespace Az2009\Cielo\Model\Method\BankSlip\Validate;

class Validate extends \Az2009\Cielo\Model\Method\Validate
{

    /**
     * @var Payment
     */
    protected $_payment;

    /**
     * @var Customer
     */
    protected $_customer;

    /**
     * @var Address
     */
    protected $_address;

    public function __construct(
        Payment $payment,
        Customer $customer,
        Address $address
    ) {
        $this->_customer = $customer;
        $this->_payment = $payment;
        $this->_address = $address;
    }

    public function validate()
    {

        $this->_customer
             ->setPayment($this->getPayment())
             ->validate();

        $this->_address
             ->setPayment($this->getPayment())
             ->validate();

        $this->_payment
             ->setPayment($this->getPayment())
             ->validate();
    }
}