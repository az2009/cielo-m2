<?php

namespace Az2009\Cielo\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_asset;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_order;

    /**
     * @var \Magento\Customer\Model\Session
     */
    public $_session;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Asset\Repository $asset
    ) {
        $this->_asset = $asset;
        $this->_order = $order;
        $this->_session = $session;
        parent::__construct($context);
    }

    public function getMerchantId()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/merchant_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getMerchantKey()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/merchant_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getMode()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getUriRequest()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/uri_request_production'
        );

        if ($this->getMode() == \Az2009\Cielo\Model\Source\Mode::MODE_STAGE) {
            $config = $this->scopeConfig->getValue(
                'payment/az2009_cielo/uri_request_stage'
            );
        }

        return (string)$config;
    }

    public function getUriQuery()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/uri_query_production'
        );

        if ($this->getMode() == \Az2009\Cielo\Model\Source\Mode::MODE_STAGE) {
            $config = $this->scopeConfig->getValue(
                'payment/az2009_cielo/uri_query_stage'
            );
        }

        return (string)$config;
    }

    public function getStatusPending()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/order_status_pending',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getStatusPay()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/order_status_pay',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $config;
    }

    public function getCardTypesAvailable()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/cctypes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $config = explode(',', $config);

        return $config;
    }

    public function getKeyRequest()
    {
        $key = urlencode(mt_rand(0, 999) .
               mt_rand(1000, 1999) .
               time() .
               $_SERVER['SERVER_ADDR']);

        return $key;
    }

    /**
     * remove placeholders of uri
     * @param $uri
     * @return mixed
     */
    public function sanitizeUri($uri)
    {
        $uri = str_replace('//', '/', $uri);
        $uri = str_replace(':/', '://', $uri);
        $uri = str_replace(['-capture', '-refund'], '', $uri);

        return $uri;
    }

    /**
     * get cards saved by customerId
     * @param $customerId int
     * @return array
     */
    public function getCardSavedByCustomer($customerId = null)
    {
        $tokens = [];

        if (is_null($customerId) && $this->_session->isLoggedIn()) {
            $customerId = $this->_session->getCustomerId();
        }

        if (empty($customerId)) {
            return $tokens;
        }

        $collection = $this->_order->create();
        $collection->addAttributeToSelect('entity_id');
        $collection->addAttributeToFilter(
            'customer_id',
            array(
                'eq' => $customerId
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
            $tokens[$order->getPayment()->getData('card_token')] = [
                'brand' => $order->getPayment()->getAdditionalInformation('cc_type'),
                'last_four' => $this->getCardLabel($order->getPayment()),
                'month_due' => $order->getPayment()->getAdditionalInformation('cc_exp_month'),
                'year_due' => $order->getPayment()->getAdditionalInformation('cc_exp_year'),
                'cardholder' => $order->getPayment()->getAdditionalInformation('cc_name'),
            ];
        }

        return $tokens;
    }

    public function getCardLabel(\Magento\Sales\Model\Order\Payment\Interceptor $payment)
    {
        $firstFour = substr($payment->getAdditionalInformation('cc_number') ?: $payment->getAdditionalInformation('cc_number_enc'),0, 4);
        $lastFour = substr($payment->getAdditionalInformation('cc_number') ?: $payment->getAdditionalInformation('cc_number_enc'), -4);

        return $firstFour. ' ****  **** '.$lastFour;
    }

    /**
     * sanitize string
     * @param $value
     * @param $maxlength
     * @param null $init
     * @return bool|string
     */
    public function prepareString($value, $maxlength, $init = null)
    {
        if (!is_null($init)) {
            return substr(trim($value), (int)$init, $maxlength);
        }

        return substr(trim($value), $maxlength);
    }

    public function canDebug()
    {
        $config = $this->scopeConfig->getValue(
            'payment/az2009_cielo/debug',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return (boolean)$config;
    }
}