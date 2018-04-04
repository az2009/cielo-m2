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

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Az2009\Cielo\Helper\Dc
     */
    protected $helperdc;

    public function __construct(
        CcConfig $ccConfig,
        Source $assetSource,
        \Az2009\Cielo\Helper\Data $helper,
        \Az2009\Cielo\Helper\Dc $helperdc,
        \Az2009\Cielo\Helper\Installment $installment,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Framework\App\State $state
    ) {
        $this->helper = $helper;
        $this->helperdc = $helperdc;
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
        $this->installment = $installment;
        $this->sessionQuote = $sessionQuote;
        $this->state = $state;
    }

    public function getConfig()
    {
        $payment = $this->sessionQuote->getQuote()->getPayment();
        $config = [
            'payment' => [
                'az2009_cielo' => [
                    'icons' => $this->getIcons(),
                    'availableTypes' => $this->getCcAvailableTypes(),
                    'availableTypesDc' => $this->getCcAvailableTypesDc(),
                    'cards' => $this->helper->getCardSavedByCustomer(),
                    'installments' => $this->getInstallmentsAvailable(),
                    'is_logged_in' => $this->helper->_session->isLoggedIn(),
                    'month' => $this->getExpMonth(),
                    'year' => $this->getExpYear(),
                ]
            ]
        ];

        if ($this->state->getAreaCode()
            == \Magento\Framework\App\Area::AREA_ADMINHTML
        ) {
            $config['payment']['az2009_cielo']['info_credit_card'] = [
                'method' => $payment->getMethod(),
                'cc_number' => $this->sessionQuote->getCcNumber(),
                'cc_name' => $this->sessionQuote->getCcName(),
                'cc_type' => $this->sessionQuote->getCcType(),
                'cc_exp_month' => $this->sessionQuote->getCcExpMonth(),
                'cc_exp_year' => $this->sessionQuote->getCcExpYear(),
                'cc_cid' => $this->sessionQuote->getCcCid()
            ];
        }

        return $config;
    }

    public function getCcAvailableTypes()
    {
        $types = $this->helper->getCardTypesAvailable();
        return $types;
    }

    public function getCcAvailableTypesDc()
    {
        $types = $this->helperdc->getCardTypesAvailable();
        return $types;
    }

    public function getExpMonth()
    {
        $month = [];
        for ($x=1; $x <= 12; $x++) {
            $value = $x <= 9 ? "0{$x}" : $x;
            $month[$value] = $value ;
        }

        return $month;
    }

    public function getExpYear()
    {
        $year = [];
        $yearCurrent = date('Y');
        $yearFuture = $yearCurrent + 5;
        for ($x = $yearCurrent; $x <= $yearFuture; $x++) {
            $year[$x] = $x;
        }

        return $year;
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

    /**
     * get flag image of card
     * @param $code
     * @return \stdClass
     */
    public function getIconByCode($code)
    {
        $asset = $this->ccConfig->createAsset('Az2009_Cielo::images/cc/' . strtolower($code) . '.png');
        $placeholder = $this->assetSource->findSource($asset);
        $std = new \stdClass();

        $std->url = '';
        $std->width = '';
        $std->height = '';

        if ($placeholder) {
            list($width, $height) = getimagesize($asset->getSourceFile());
            $std->url = $asset->getUrl();
            $std->width = $width;
            $std->height = $height;
        }

        return $std;
    }

    public function getInstallmentsAvailable()
    {
        return $this->installment->getInstallmentsAvailable();
    }
}