<?php

namespace Az2009\Cielo\Model\Method\BankSlip;

class BankSlip extends \Az2009\Cielo\Model\Method\AbstractMethod
{

    const CODE_PAYMENT = 'az2009_cielo_bank_slip';

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
    protected $_canCaptureOnce = false;

    /**
     * @var bool
     */
    protected $_canCapture = false;

    /**
     * @var bool
     */
    protected $_canFetchTransactionInfo = false;

    /**
     * @var bool
     */
    protected $_canRefund = false;

    /**
     * @var bool
     */
    protected $_canCapturePartial = false;

    /**
     * @var bool
     */
    protected $_canVoid = false;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = false;

    /**
     * @var bool
     */
    protected $_canReviewPayment = false;

    /**
     * @var bool
     */
    protected $_canCancelInvoice = false;

    /**
     * @var \Az2009\Cielo\Block\Form\BankSlip
     */
    protected $_infoBlockType = \Az2009\Cielo\Block\Info\BankSlip::class;

    /**
     * @var \Az2009\Cielo\Block\Form\BankSlip
     */
    protected $_formBlockType = \Az2009\Cielo\Block\Form\BankSlip::class;

    /**
     * @var \Az2009\Cielo\Model\Method\BankSlip\Postback
     */
    protected $_postback = \Az2009\Cielo\Model\Method\BankSlip\Postback::class;

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
        Validate\Validate $validate,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Az2009\Cielo\Helper\BankSlip $helper,
        \Az2009\Cielo\Model\Method\BankSlip\Postback $update,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context, $registry, $extensionFactory,
            $customAttributeFactory, $paymentData, $scopeConfig, $logger,
            $request, $response, $validate, $httpClientFactory, $helper, $update,
            $resource, $resourceCollection, $data
        );

    }


}