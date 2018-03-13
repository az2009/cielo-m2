<?php

namespace Az2009\Cielo\Model;

use Magento\Framework\View\Asset\Source;

class CieloConfigProvider
    extends \Magento\Payment\Model\CcConfigProvider
        implements \Magento\Checkout\Model\ConfigProviderInterface
{

    /**
     * @var array
     */
    protected $icons = [];

    /**
     * @var \Az2009\Cielo\Helper\Installment
     */
    protected $installment;

    public function __construct(
        CcConfig $ccConfig,
        Source $assetSource,
        \Az2009\Cielo\Helper\Data $helper,
        \Az2009\Cielo\Helper\Installment $installment
    ) {
        $this->helper = $helper;
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
        $this->installment = $installment;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                'az2009_cielo' => [
                    'icons' => $this->getIcons(),
                    'availableTypes' => $this->getCcAvailableTypes(),
                    'cards' => $this->helper->getCardSavedByCustomer(),
                    'installments' => $this->installment->getInstallmentsAvailable(),
                    'is_logged_in' => $this->helper->_session->isLoggedIn()
                ]
            ]
        ];
    }

    public function getCcAvailableTypes()
    {
        $types = $this->helper->getCardTypesAvailable();
        return $types;
    }

    /**
     * get icons for available payment methods
     * @return array
     */
    public function getIcons()
    {
        if (!empty($this->icons)) {
            return $this->icons;
        }

        $types = $this->ccConfig->getCcAvailableTypes();
        foreach (array_keys($types) as $code) {
            if (!array_key_exists($code, $this->icons)) {
                $asset = $this->ccConfig->createAsset('Az2009_Cielo::images/cc/' . strtolower($code) . '.png');
                $placeholder = $this->assetSource->findSource($asset);
                if ($placeholder) {
                    list($width, $height) = getimagesize($asset->getSourceFile());
                    $this->icons[$code] = [
                        'url' => $asset->getUrl(),
                        'width' => $width,
                        'height' => $height
                    ];
                }
            }
        }

        return $this->icons;
    }
}