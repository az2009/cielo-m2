<?php

namespace Az2009\Cielo\Model\Method\Cc;

use Magento\Framework\DataObject;

class Cc extends \Az2009\Cielo\Model\Method\AbstractMethod
{

    protected $_code = 'az2009_cielo';

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canCancelInvoice = true;


    /**
     * @var string
     */
    protected $_formBlockType = \Az2009\Cielo\Block\Form\Cc::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \Az2009\Cielo\Block\Info\Cc::class;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        Request\Request $request,
        Response\Response $response,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig,
            $logger, $request, $response, $httpClientFactory, $helper, $resource, $resourceCollection, $data);
    }

    public function validate()
    {
        return true;
    }

    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        $info = $this->getInfoInstance();
        $info->setAdditionalInformation($data->getAdditionalData());
        return $this;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::refund($payment, $amount);
    }

    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::cancel($payment);
    }

    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::void($payment);
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $payment->setAdditionalInformation('can_capture', false);
        $this->post($payment)
             ->request();

    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $payment->setAdditionalInformation('can_capture', true);
        $this->post($payment)
             ->request()
             ->getResponse()
             ->setPayment($payment)
             ->processResponse();

    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return true;
    }
}