<?php

namespace Az2009\Cielo\Model\Method\Cc\Validate;

class CreditCard extends \Az2009\Cielo\Model\Method\Validate
{

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Az2009\Cielo\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($data);
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

        // American Express
        'Amex' => '/^3[47][0-9]{13}$/',

        // Discover
        'Discover' => '/^(6011((0|9|[2-4])[0-9]{11,14}|(74|7[7-9]|8[6-9])[0-9]{10,13})|6(4[4-9][0-9]{13,16}|' .
        '5[0-9]{14,17}))/',

        // Diners
        'Diners' => '/^(3(0[0-5]|095|6|[8-9]))\d*$/',

        // JCB
        'JCB' => '/^35(2[8-9][0-9]{12,15}|[3-8][0-9]{13,16})/',

        // Aura
        'Aura' => '/^(5078\d{2})(\d{2})(\d{11})$/',

        // ELO
        'Elo' => '/^(?:401178|401179|431274|438935|451416|457393|457631|457632|504175|627780|636297|636368|655000|655001|651652|651653|651654|650485|650486|650487|650488|506699|5067[0-6][0-9]|50677[0-8]|509\d{3})\d{10}$/',

        // Hipercard
        'Hipercard' => '/^606282|3841\d{2}/',

    ];

    /**
     * check if the request is by card saved token
     * @return bool
     */
    public function isToken()
    {
        $payment = $this->getPayment()->getInfoInstance();
        $ccToken = $payment->getAdditionalInformation('cc_token');
        if ($ccToken == \Az2009\Cielo\Helper\Data::CARD_TOKEN || empty($ccToken)) {
            return false;
        }

        return true;
    }

    /**
     * get fields to validate when is request by card saved token
     */
    public function getFieldsWhenToken()
    {
        $this->_fieldsValidate = [
            'CardToken' => [
                'required' => true,
                'maxlength' => 36,
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
    }

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
        if (!isset($params['Payment']['CreditCard'])) {
            throw new \Az2009\Cielo\Exception\Cc(__('Invalid Credit Card Info '));
        }

        $creditCard = $params['Payment']['CreditCard'];
        foreach ($creditCard as $k => $v) {
            $this->required($k,$v, __('Credit Card: '));
            $this->maxLength($k,$v, __('Credit Card: '));
        }

        $this->isValidCreditCard($creditCard);
        $this->isValidDueDate($creditCard);
        $this->isValidToken($creditCard);
    }

    /**
     * validate credit card token
     * @param array $creditCard
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function isValidToken(Array $creditCard)
    {
        if ($this->isToken()
            && !array_key_exists($creditCard['CardToken'], $this->helper->getCardSavedByCustomer())
        ) {
            throw new \Az2009\Cielo\Exception\Cc(__('Invalid Token Credit Card'));
        }
    }

    /**
     * validate credit card due date
     * @param array $creditCard
     */
    public function isValidDueDate(Array $creditCard)
    {
        if (!$this->isToken()) {

            $date = $creditCard['ExpirationDate'];
            $date = explode('/', $date);

            if ($date[0] < date('m') && $date[1] <= date('Y')
                || $date[1] < date('Y')
            ) {
                throw new \Az2009\Cielo\Exception\Cc(__('Invalid Due Date of Credit Card'));
            }
        }
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
            throw new \Az2009\Cielo\Exception\Cc(__('Invalid Credit Card'));
        }
    }
}