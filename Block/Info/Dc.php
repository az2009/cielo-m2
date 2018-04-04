<?php

namespace Az2009\Cielo\Block\Info;

class Dc extends Cc
{
    protected $_paymentSpecificInformation;
    /**
     * Prepare credit card related payment info
     *
     * @param \Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */

    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        $transport = \Magento\Payment\Block\Info::_prepareSpecificInformation($transport);

        $data = [];
        $month = $this->getCcExpMonth();
        $year = $this->getCcExpYear();

        if ($ccType = $this->getCcTypeName()) {
            $data[(string)__('Debit Card Type')] = $ccType;
        }

        if ($last4 = $this->getCcLast4()) {
            $data[(string)__('Debit Card Number')] = sprintf('xxxx-%s', $last4);
        }

        if ($month && $year) {
            $data[(string)__('Due Date')] = $this->_formatCardDate($year, $month);
        }

        if ($this->_appState->getAreaCode()
            == \Magento\Framework\App\Area::AREA_ADMINHTML
        ) {
            $data[(string)__('Transaction ID')] = $this->helper->sanitizeUri($this->getInfo()->getLastTransId()) ?: __('N/A');
        }

        $this->_paymentSpecificInformation = $transport->setData(array_merge($data, $transport->getData()));

        return $this->_paymentSpecificInformation;
    }
}