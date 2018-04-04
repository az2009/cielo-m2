<?php

namespace Az2009\Cielo\Model\Method\Dc\Validate;

class Customer extends \Az2009\Cielo\Model\Method\Cc\Validate\Customer
{
    protected $_fieldsValidate = [
        'Name' => [
            'required' => true,
            'maxlength' => 255,
        ]
    ];

    public function validate()
    {
        $params = $this->getRequest();
        if (!isset($params['Customer'])) {
            throw new \Az2009\Cielo\Exception\Cc(__('Customer info invalid'));
        }

        $customer = $params['Customer'];
        foreach ($customer as $k => $v) {
            $this->required($k,$v, __('Customer: '));
            $this->maxLength($k,$v, __('Customer: '));
        }
    }
}