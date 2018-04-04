<?php

namespace Az2009\Cielo\Model\Method\Dc\Validate;

class Validate extends \Az2009\Cielo\Model\Method\Cc\Validate\Validate
{

    /**
     * @var DebitCard
     */
    protected $_debitCard;

    /**
     * @var Payment
     */
    protected $_payment;

    /**
     * @var Customer
     */
    protected $_customer;

    public function __construct(
        DebitCard $debitCard,
        Payment $payment,
        Customer $customer
    ) {
        $this->_debitCard = $debitCard;
        $this->_customer = $customer;
        $this->_payment = $payment;
    }

    public function validate()
    {
        $this->_debitCard
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