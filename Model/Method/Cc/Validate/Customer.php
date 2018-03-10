<?php

namespace Az2009\Cielo\Model\Method\Cc\Validate;

class Customer extends \Az2009\Cielo\Model\Method\Validate
{
    protected $_fieldsValidate = [
        'Name' => [
            'required' => true,
            'maxlength' => 255,
        ],
        'Identity' => [
            'required' => false,
            'maxlength' => 14,
        ],
        'IdentityType' => [
            'required' => false,
            'maxlength' => 255,
        ],
        'Email' => [
            'required' => false,
            'maxlength' => 255,
        ]
    ];

    public function validate()
    {
        $params = $this->getRequest();
        if (!isset($params['Customer'])) {
            throw new \Az2009\Cielo\Exception\CC(__('Customer info invalid'));
        }

        $creditCard = $params['Customer'];
        foreach ($creditCard as $k => $v) {
            $this->required($k,$v, __('Customer: '));
            $this->maxLength($k,$v, __('Customer: '));
        }
    }
}