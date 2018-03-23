<?php

namespace Az2009\Cielo\Model\Source;

use Magento\Payment\Model\Source\Cctype as PaymentCctype;

class Cctype extends PaymentCctype
{
    protected $_brands = [
        'VI' => 'Visa',
        'MC' => 'Master',
        'AE' => 'Amex',
        'DI' => 'Discover',
        'JCB' => 'JCB',
        'DN' => 'Diners',
        'ELO' => 'Elo',
        'HIC' => 'Hipercard',
    ];

    /**
     * @return string[]
     */
    public function getAllowedTypes()
    {
        return array_keys($this->_brands);
    }

    /**
     * get card in format to send cielo
     * @return array
     */
    public function getBrandFormatCielo($brand)
    {
        if (isset($this->_brands[$brand])) {
            return $this->_brands[$brand];
        }

        return false;
    }
}