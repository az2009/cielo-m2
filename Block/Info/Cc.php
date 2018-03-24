<?php

namespace Az2009\Cielo\Block\Info;

class Cc extends \Magento\Payment\Block\Info\Cc
{

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    public $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Payment\Model\Config $paymentConfig, array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $paymentConfig, $data);
    }

    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getCcTypeName()
    {
        $types = $this->_paymentConfig->getCcTypes();
        $ccType = $this->getInfo()->getAdditionalInformation('cc_type');
        if (isset($types[$ccType])) {
            return $types[$ccType];
        }
        return empty($ccType) ? __('N/A') : $ccType;
    }

    /**
     * Whether current payment method has credit card expiration info
     *
     * @return bool
     */
    public function hasCcExpDate()
    {
        return (int)$this->getInfo()->getAdditionalInformation('cc_exp_month')
               || (int)$this->getInfo()->getAdditionalInformation('cc_exp_year');
    }

    /**
     * Retrieve CC expiration month
     *
     * @return string
     */
    public function getCcExpMonth()
    {
        $month = $this->getInfo()->getAdditionalInformation('cc_exp_month');
        return $month;
    }

    /**
     * Retrieve CC expiration month
     *
     * @return string
     */
    public function getCcExpYear()
    {
        $year = $this->getInfo()->getAdditionalInformation('cc_exp_year');
        return $year;
    }

    /**
     * Retrieve CC expiration date
     *
     * @return \DateTime
     */
    public function getCcExpDate()
    {
        $date = new \DateTime('now', new \DateTimeZone($this->_localeDate->getConfigTimezone()));
        $date->setDate(
            $this->getInfo()->getAdditionalInformation('cc_exp_year'),
            $this->getInfo()->getAdditionalInformation('cc_exp_month'
            ) + 1, 0
        );
        return $date;
    }

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

        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $month = $this->getCcExpMonth();
        $year = $this->getCcExpYear();

        if ($ccType = $this->getCcTypeName()) {
            $data[(string)__('Credit Card Type')] = $ccType;
        }

        if ($last4 = $this->getCcLast4()) {
            $data[(string)__('Credit Card Number')] = sprintf('xxxx-%s', $last4);
        }

        if ($month && $year) {
            $data[(string)__('Due Date')] = $this->_formatCardDate($year, $month);
        }

        $data[(string)__('Transaction Type')] = $this->getIsAuthorizeOrCapture() ?: __('N/A');
        $data[(string)__('Transaction ID')] = $this->helper->sanitizeUri($this->getInfo()->getLastTransId()) ?: __('N/A');

        return $transport->setData(array_merge($data, $transport->getData()));
    }

    public function getIsAuthorizeOrCapture()
    {
        $info = $this->getInfo();
        $type = __('Authorize');

        if ($info->getAdditionalInformation('can_capture')) {
            $type = __('Capture');
        }

        return $type;
    }

    public function getCcLast4()
    {
        return substr($this->getInfo()->getAdditionalInformation('cc_number'), -4);
    }

}