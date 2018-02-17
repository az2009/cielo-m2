<?php

namespace Az2009\Cielo\Model;

class CieloConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    public function getConfig()
    {
        return [
            'payment' => [
                'az2009_cielo' => [
                    'teste' => 'teste123'
                ]
            ]
        ];
    }

    public function isValidDoc()
    {

    }

    public function getImgURLBrandCard()
    {

    }

    public function isAvailable()
    {

    }

    public function isValid()
    {

    }
}