<?php

namespace Az2009\Cielo\Model\Method\BankSlip\Validate;

class Address extends \Az2009\Cielo\Model\Method\Validate
{
    protected $_fieldsValidate = [
        'Street' => [
            'required' => false,
            'maxlength' => 70,
        ],
        'Number' => [
            'required' => true,
            'maxlength' => 10,
        ],
        'Complement' => [
            'required' => false,
            'maxlength' => 50,
        ],
        'ZipCode' => [
            'required' => true,
            'maxlength' => 9,
        ],
        'City' => [
            'required' => true,
            'maxlength' => 18,
        ],
        'State' => [
            'required' => true,
            'maxlength' => 2,
        ],
        'Country' => [
            'required' => true,
            'maxlength' => 35,
        ]
    ];

    public function validate()
    {
        $params = $this->getRequest();
        if (!isset($params['Customer']['Address'])) {
            throw new \Az2009\Cielo\Exception\CC(__('Billing Address info invalid'));
        }

        $creditCard = $params['Customer']['Address'];
        foreach ($creditCard as $k => $v) {
            $this->required($k,$v, __('Billing Address: '));
            $this->maxLength($k,$v, __('Billing Address: '));
        }
    }
}