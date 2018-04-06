<?php

namespace Az2009\Cielo\Block\Order;

class Info extends \Magento\Sales\Block\Order\Info
{
    public function isShowBankSlip()
    {
        $payment = $this->getPayment();

        if ($payment->getMethod()
            !== \Az2009\Cielo\Model\Method\BankSlip\BankSlip::CODE_PAYMENT
        ) {
            return false;
        }

        return true;
    }

    public function getLinkBankSlip()
    {
        $link = '';
        $payment = $this->getPayment();
        $data = $payment->getAdditionalInformation('payment_data');
        $data = \Zend\Json\Json::decode($data, true);

        if (empty($data) || !isset($data['Url'])) {
            return $link;
        }

        $link = $data['Url'];
        return $link;
    }

    public function getPayment()
    {
        $order = $this->getOrder();
        $payment = $order->getPayment();
        return $payment;
    }
}