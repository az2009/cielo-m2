<?php

namespace Az2009\Cielo\Model;

use Magento\Framework\View\Asset\Source;

class CieloConfigProvider
    extends \Magento\Payment\Model\CcConfigProvider
        implements \Magento\Checkout\Model\ConfigProviderInterface
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_order;

    public function __construct(
        CcConfig $ccConfig,
        Source $assetSource,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order
    ) {
        $this->helper = $helper;
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
        $this->_session = $session;
        $this->_order = $order;
    }

    /**
     * @var array
     */
    private $icons = [];

    public function getConfig()
    {
        return [
            'payment' => [
                'az2009_cielo' => [
                    'icons' => $this->getIcons(),
                    'availableTypes' => $this->getCcAvailableTypes(),
                    'cards' => $this->getCardsSave()
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
     * Get icons for available payment methods
     *
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

    public function isValidDoc()
    {

    }

    public function getCardsSave()
    {
        $tokens = [];
        if ($this->_session->isLoggedIn()) {
            $collection = $this->_order->create();
            $collection->addAttributeToSelect('entity_id');
            $collection->addAttributeToFilter(
                'customer_id',
                array(
                    'eq' => $this->_session->getCustomerId()
                )
            );
            $collection->getSelect()
                       ->join(
                           array('sop' => $collection->getTable('sales_order_payment')),
                           'main_table.entity_id = sop.parent_id AND sop.card_token IS NOT NULL',
                           []
                       )
                       ->group('sop.card_token');


           foreach ($collection as $order) {
                $tokens[$order->getPayment()->getData('card_token')] = $order->getPayment()->getAdditionalInformation('cc_type') .
                                '-' . substr($order->getPayment()->getAdditionalInformation('cc_number_enc'), -4);
            }
        }

        return $tokens;
    }

}