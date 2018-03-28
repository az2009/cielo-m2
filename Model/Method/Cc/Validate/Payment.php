<?php

namespace Az2009\Cielo\Model\Method\Cc\Validate;

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
        'Installments' => [
            'required' => true,
            'maxlength' => 2,
        ]
    ];

    public function validate()
    {
        $params = $this->getRequest();
        if (!isset($params['Payment'])) {
            throw new \Az2009\Cielo\Exception\Cc(__('Payment info invalid'));
        }

        $creditCard = $params['Payment'];
        foreach ($creditCard as $k => $v) {
            $this->required($k,$v, __('Payment: '));
            $this->maxLength($k,$v, __('Payment: '));
        }
    }
}