<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Az2009\Cielo\Model\Method\Cc\Request;

class Payment extends \Magento\Framework\DataObject
{

    const TYPE = 'CreditCard';
    const INTEREST = 'ByMerchant';
    const SAVE_CARD = 'true';

    /**
     * @var \Az2009\Cielo\Model\Source\Cctype
     */
    protected $_cctype;

    /**
     * @var \Magento\Sales\Model\Order\Address
     */
    protected $billingAddress;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    public function __construct(
        \Az2009\Cielo\Model\Source\Cctype $cctype,
        \Az2009\Cielo\Helper\Data $helper,
        array $data = []
    ) {
        $this->_cctype = $cctype;
        $this->helper = $helper;
        parent::__construct($data);
    }

    public function getRequest()
    {
        $this->order = $this->getOrder();
        $this->billingAddress = $this->order->getBillingAddress();
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
                               'ServiceTaxAmount' => 0,
                               'Installments' => $this->getInstallments(),
                               'Interest' => Payment::INTEREST,
                               'Currency' => $this->order->getStoreCurrencyCode(),
                               'Country' => $this->helper->prepareString($this->helper->getCountryCodeAlpha3($this->billingAddress->getCountryId()), 35, 0),
                               'Capture' => $info->getAdditionalInformation('can_capture'),
                               'Authenticate' => false,
                               'SoftDescriptor' => $this->helper->prepareString($this->getSoftDescriptor(), 13, 0),
                               'CreditCard' => $this->getCreditCard(),
                           ]
                        ]
                      )->toArray();


    }

    /**
     * @return bool|string
     */
    public function getSoftDescriptor()
    {
        $desc = $this->getPayment()
                     ->getConfigData(
                         'billing_description',
                         $this->order->getStoreId()
                     );

        return $desc;
    }

    /**
     * prepare installments
     * @return int
     */
    public function getInstallments()
    {
        $installments = $this->getInfo()->getAdditionalInformation('cc_installments');
        $installments = intval($installments);
        if ($installments <= 0) {
            $installments = 1;
        }

        return $installments;
    }

    public function getCreditCard()
    {
        $token = $this->getInfo()->getAdditionalInformation('cc_token');

        if (empty($token) || $token == \Az2009\Cielo\Helper\Data::CARD_TOKEN) {
            return $this->getCreditCardNew();
        }

        return $this->getCreditCardSaved();
    }

    public function isSaveCard()
    {
        if (!$this->helper->_session->isLoggedIn()) {
            return false;
        }

        $isSave = $this->getPayment()->getConfigData('can_save_cc', $this->order->getStoreId());
        return (boolean)$isSave;
    }

    /**
     * mock data credit card new
     * @return array
     */
    public function getCreditCardNew()
    {
        return [
            'CardNumber' => $this->getInfo()->getAdditionalInformation('cc_number'),
            'Holder' => $this->getInfo()->getAdditionalInformation('cc_name'),
            'ExpirationDate' => $this->getExpDate(),
            'SecurityCode' => $this->getInfo()->getAdditionalInformation('cc_cid'),
            'SaveCard' => $this->isSaveCard(),
            'Brand' => $this->_cctype->getBrandFormatCielo($this->getInfo()->getAdditionalInformation('cc_type'))
        ];
    }

    /**
     * mock data credit card saved
     * @return array
     */
    public function getCreditCardSaved()
    {
        return [
            'CardToken' => $this->getInfo()->getAdditionalInformation('cc_token'),
            'SaveCard' => false,
            'SecurityCode' => $this->getInfo()->getAdditionalInformation('cc_cid'),
            'Brand' => $this->_cctype->getBrandFormatCielo($this->getInfo()->getAdditionalInformation('cc_type'))
        ];
    }

    /**
     * mock data date due of card
     * @return string
     */
    public function getExpDate()
    {
        $date = [
            $this->getInfo()->getAdditionalInformation('cc_exp_month'),
            $this->getInfo()->getAdditionalInformation('cc_exp_year')
        ];

        return implode('/', $date);
    }

}