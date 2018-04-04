<?php

namespace Az2009\Cielo\Model\Method\Dc\Validate;

class Payment extends \Az2009\Cielo\Model\Method\Cc\Validate\Payment
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
        'Authenticate' => [
            'required' => true,
        ],
        'ReturnUrl' => [
            'required' => true,
        ],
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