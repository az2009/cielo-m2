<?php

namespace Az2009\Cielo\Model\Method\BankSlip\Validate;

class Payment extends \Az2009\Cielo\Model\Method\Validate
{
    protected $_fieldsValidate = [
        'Type' => [
            'required' => true,
            'maxlength' => 100,
        ],
        'Amount' => [
            'required' => true,
            'maxlength' => 15,
        ],
        'Provider' => [
            'required' => true,
            'maxlength' => 15,
        ],
        'ExpirationDate' => [
            'required' => false
        ],
        'Assignor' => [
            'required' => false,
            'maxlength' => 200,
        ],
        'Demonstrative' => [
            'required' => false,
            'maxlength' => 450,
        ],
        'Address' => [
            'required' => false,
            'maxlength' => 255,
        ],
        'BoletoNumber' => [
            'required' => false
        ],
        'Identification' => [
            'required' => true,
            'maxlength' => 14,
        ],
        'Instructions' => [
            'required' => false,
            'maxlength' => 450,
        ]
    ];

    public function validate()
    {
        $params = $this->getRequest();
        if (!isset($params['Payment'])) {
            throw new \Az2009\Cielo\Exception\Cc(__('Payment info invalid'));
        }

        $payment = $params['Payment'];
        foreach ($payment as $k => $v) {
            $this->required($k,$v, __('Payment: '));
            $this->maxLength($k,$v, __('Payment: '));
        }
    }
}