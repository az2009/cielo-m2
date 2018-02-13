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
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    public function getRequest()
    {
        $this->order = $this->getOrder();
        return $this->setData(
                        [
                           'Payment' => [
                               'Type' => Payment::TYPE,
                               'Amount' => $this->order->getGrandTotal(),
                               'ServiceTaxAmount' => 0,
                               'Installments' => $this->order->getPayment()->getInstallments(),
                               'Interest' => Payment::INTEREST,
                               'Capture' => $this->order->getPayment()->hasCapture(),
                               'Authenticate' => false,
                               'SoftDescriptor' => $this->order
                                                        ->getPayment()
                                                        ->getMethodInstance()
                                                        ->getConfigData('billing_description', $this->order->getStoreId()),
                               'CreditCard' => $this->getCreditCard(),
                           ]
                        ]
                      )->toArray();
    }

    /**
     * @return array
     */
    public function getCreditCard()
    {
        return [
            'CardNumber' => $this->order->getPayment()->getCcNumber(),
            'Holder' => $this->order->getPayment()->getCcOwner(),
            'ExpirationDate' => $this->getExpDate(),
            'SecurityCode' => $this->order->getPayment()->getCcSecureVerify(),
            'SaveCard' => Payment::SAVE_CARD,
            'Brand' => $this->order->getPayment()->getCcType()
        ];
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        $date = [
            $this->order->getPayment()->getCcExpMonth(),
            $this->order->getPayment()->getCcExpYear()
        ];

        return implode('/', $date);
    }
}