<?php

namespace Az2009\Cielo\Model\Method\Cc\Validate;

class Validate extends \Az2009\Cielo\Model\Method\Validate
{

    /**
     * @var CreditCard
     */
    protected $_creditCard;

    /**
     * @var Payment
     */
    protected $_payment;

    /**
     * @var Customer
     */
    protected $_customer;


    public function __construct(
        CreditCard $creditCard,
        Payment $payment,
        Customer $customer
    ) {
        $this->_creditCard = $creditCard;
        $this->_customer = $customer;
        $this->_payment = $payment;
    }

    public function validate()
    {
        $this->_creditCard
             ->setPayment($this->getPayment())
             ->validate();

        $this->_customer
             ->setPayment($this->getPayment())
             ->validate();

        $this->_payment
             ->setPayment($this->getPayment())
             ->validate();
    }
}