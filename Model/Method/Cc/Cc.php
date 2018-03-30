<?php

namespace Az2009\Cielo\Model\Method\Cc;

class Cc extends \Az2009\Cielo\Model\Method\AbstractMethod
{

    const CODE_PAYMENT = 'az2009_cielo';
    /**
     * @var string
     */
    protected $_code = self::CODE_PAYMENT;

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
    protected $_canVoid = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canReviewPayment = false;

    /**
     * @var bool
     */
    protected $_canFetchTransactionInfo = false;

    /**
     * @var bool
     */
    protected $_canCancelInvoice = true;

    /**
     * @var \Az2009\Cielo\Block\Form\Cc
     */
    protected $_infoBlockType = \Az2009\Cielo\Block\Info\Cc::class;

    /**
     * @var \Az2009\Cielo\Block\Form\Cc
     */
    protected $_formBlockType = \Az2009\Cielo\Block\Form\Cc::class;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Postback
     */
    protected $_postback = \Az2009\Cielo\Model\Method\Cc\Postback::class;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        Request\Request $request,
        Response\Payment $response,
        \Az2009\Cielo\Model\Method\Cc\Validate\Validate $validate,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Az2009\Cielo\Helper\Data $helper,
        \Az2009\Cielo\Model\Method\Cc\Postback $update,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context, $registry,
            $extensionFactory, $customAttributeFactory,
            $paymentData, $scopeConfig,
            $logger, $request,
            $response, $validate, $httpClientFactory,
            $helper, $update, $resource,
            $resourceCollection, $data
        );
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::authorize($payment, $amount);
        $payment->setAdditionalInformation('cc_cid', '');
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
        $payment->setAdditionalInformation('cc_cid', '');
    }

}