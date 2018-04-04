<?php

namespace Az2009\Cielo\Block\Info;

class BankSlip extends \Magento\Payment\Block\Info
{
    /**
     * Prepare data related payment info
     *
     * @param \Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $info = $this->getInfo();

        $transaction = $info->getAdditionalInformation('payment_data');

        if (empty($transaction)) {
            return $this->_paymentSpecificInformation;
        }

        $transaction = \Zend\Json\Json::decode($transaction, true);

        if (!is_array($transaction)) {
            return $this->_paymentSpecificInformation;
        }

        if (isset($transaction['Url'])) {
            $data[(string)__('URL Bank Slip')] = $transaction['Url'];
        }

        if (isset($transaction['BarCodeNumber'])) {
            $data[(string)__('Bar Code Number')] = $transaction['BarCodeNumber'];
        }

        if (isset($transaction['Number'])) {
            $data[(string)__('Number')] = $transaction['Number'];
        }

        if (isset($transaction['DigitableLine'])) {
            $data[(string)__('Digitable Line')] = $transaction['DigitableLine'];
        }

        if (isset($transaction['ExpirationDate'])) {
            $data[(string)__('Expiration Date')] = $transaction['ExpirationDate'];
        }

        if (isset($transaction['Identification'])) {
            $data[(string)__('Identification')] = $transaction['Identification'];
        }

        if (isset($transaction['PaymentId'])) {
            $data[(string)__('Payment ID')] = $transaction['PaymentId'];
        }

        if (isset($transaction['Provider'])) {
            $data[(string)__('Provider')] = $transaction['Provider'];
        }

        $this->_paymentSpecificInformation = $transport->setData(array_merge($data, $transport->getData()));

        return $this->_paymentSpecificInformation;
    }

}
