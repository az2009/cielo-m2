<?php

namespace Az2009\Cielo\Model\Method\Dc\Validate;

class DebitCard extends \Az2009\Cielo\Model\Method\Cc\Validate\CreditCard
{

    /**
     * @var \Az2009\Cielo\Helper\Dc
     */
    protected $helper;

    public function __construct(
        \Az2009\Cielo\Helper\Dc $helper,
        array $data = []
    ) {
        parent::__construct($helper, $data);
        $this->helper = $helper;
    }

    /**
     * rules field
     * @var array
     */
    protected $_fieldsValidate = [
        'CardNumber' => [
            'required' => true,
            'maxlength' => 19,
        ],
        'Holder' => [
            'required' => true,
            'maxlength' => 25,
        ],
        'ExpirationDate' => [
            'required' => true,
            'maxlength' => 7,
        ],
        'SecurityCode' => [
            'required' => true,
            'maxlength' => 4,
        ],
        'Brand' => [
            'required' => true,
            'maxlength' => 10,
        ]
    ];

    /**
     * regex of credit card
     * @var array
     */
    protected $_ccTypeRegExpList = [
        // Visa
        'Visa' => '/^4[0-9]{12}([0-9]{3})?$/',

        // Master Card
        'Master' => '/^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/',
    ];

    /**
     * validate fields
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function validate()
    {
        if ($this->isToken()) {
            $this->getFieldsWhenToken();
        }

        $params = $this->getRequest();
        if (!isset($params['Payment']['DebitCard'])) {
            throw new \Az2009\Cielo\Exception\Cc(__('Invalid Debit Card Info'));
        }

        $creditCard = $params['Payment']['DebitCard'];
        foreach ($creditCard as $k => $v) {
            $this->required($k,$v, __('Debit Card: '));
            $this->maxLength($k,$v, __('Debit Card: '));
        }

        $this->isValidCreditCard($creditCard);
        $this->isValidDueDate($creditCard);
    }

    /**
     * validate credit card number
     * @param array $creditCard
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function isValidCreditCard(Array $creditCard)
    {
        if (!$this->isToken()
            && !preg_match(
                $this->_ccTypeRegExpList[$creditCard['Brand']], $creditCard['CardNumber']
            )
        ) {
            throw new \Az2009\Cielo\Exception\Cc(__('Invalid Debit Card'));
        }
    }
}