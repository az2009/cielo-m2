<?php

namespace Az2009\Cielo\Model\Method\Dc\Request;

class Payment extends \Az2009\Cielo\Model\Method\Cc\Request\Payment
{

    const TYPE = 'DebitCard';

    public function __construct(
        \Az2009\Cielo\Model\Source\Cctype $cctype,
        \Az2009\Cielo\Helper\Dc $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($cctype, $helper, $data);
    }

    public function getRequest()
    {
        $this->order = $this->getOrder();
        $payment = $this->order
                        ->getPayment()
                        ->getMethodInstance();

        $info = $payment->getInfoInstance();

        $this->setInfo($info);
        $this->setPayment($payment);

        return $this->setData(
            [
                'Payment' => [
                    'Type' => Payment::TYPE,
                    'Amount' => $info->getAmount(),
                    'Authenticate' => true,
                    'ReturnUrl' => $this->getReturnUrl(),
                    'DebitCard' => $this->getDebitCard(),
                ]
            ]
        )->toArray();
    }

    public function getDebitCard()
    {
        $debitCard =  $this->getCreditCardNew();
        if (isset($debitCard['SaveCard'])) {
            unset($debitCard['SaveCard']);
        }

        return $debitCard;
    }

    public function getReturnUrl()
    {
        return $this->helper->getReturnUrl();
    }

}