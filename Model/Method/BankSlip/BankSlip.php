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
    protected $_canCaptureOnce = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canFetchTransactionInfo = true;

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
    protected $_canReviewPayment = true;

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

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

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
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context, $registry, $extensionFactory,
            $customAttributeFactory, $paymentData, $scopeConfig, $logger,
            $request, $response, $validate, $httpClientFactory, $helper,
            $resource, $resourceCollection, $data
        );

        $this->messageManager = $messageManager;
    }

    public function denyPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        $msg = (string)__(
            'Payment Denied Manual/Offline. '.
            'Manually cancel the payment on the payment gateway. '.
            'This cancellation is only effective in the store. Transaction ID: %1',
            $payment->getLastTransId()
        );

        $this->messageManager->addNoticeMessage($msg);

        $payment->getOrder()
                ->addStatusHistoryComment($msg);

        return true;
    }

    public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        $msg = (string)__(
            'Payment Accept/Authorize Manual/Offline. '.
            'Manually capture the payment at the payment gateway and generate'.
            'the offline invoice at the store. '.
            'This authorization is only being made out in the store. '.
            'Transaction ID: %1',
            $payment->getLastTransId()
        );

        $this->messageManager->addNoticeMessage($msg);

        $payment->getOrder()
                ->addStatusHistoryComment($msg);

        return true;
    }

    public function fetchTransactionInfo(\Magento\Payment\Model\InfoInterface $payment, $transactionId)
    {
        return [];
    }
}