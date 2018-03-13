<?php

namespace Az2009\Cielo\Model\Method\Cc;

class Cc extends \Az2009\Cielo\Model\Method\AbstractMethod
{

    /**
     * @var string
     */
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
    protected $_canVoid = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canReviewPayment = true;

    /**
     * @var bool
     */
    protected $_canCancelInvoice = true;

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
        Response\Payment $response,
        \Az2009\Cielo\Model\Method\Cc\Validate\Validate $validate,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Az2009\Cielo\Helper\Data $helper,
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
            $helper, $resource,
            $resourceCollection, $data
        );
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
        $this->setAmount($payment, $amount);
        if ($amount != $payment->getAmountAuthorized()) {
            $payment->setRefundPartial(true);
            $this->getClient()
                 ->setParameterGet('amount', $amount);
        }

        self::void($payment);
    }

    public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        $r = '';
        return self::capture($payment, $payment->getAmountAuthorized());
    }

    public function denyPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        $r = '';
        return self::void($payment);
    }

    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return self::void($payment);
    }

    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        $payment->setActionCancel(true);
        $this->setPath($payment->getLastTransId(), 'void')
             ->put()
             ->request();
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->setAmount($payment, $amount);
        $payment->setAdditionalInformation('can_capture', false);
        $this->setRunValidate(true);
        $this->post()->request();
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        //set value that are being captured
        $this->setAmount($payment, $amount);
        $this->setRunValidate(true);
        //check if operation have transaction authorize
        if ($payment->getAuthorizationTransaction()
            && !$payment->getOrder()->getTotalPaid()) {
            $this->setRunValidate(false);
            $this->setPath($payment->getLastTransId(), 'capture')
                 ->put();

            if ($amount != $payment->getAmountAuthorized()) {
                $payment->setCapturePartial(true);
                $this->getClient()
                     ->setParameterGet('amount', $amount);
            }

        } else {

            //check if transaction has value captured
            if ($payment->getOrder()->getTotalPaid() > 0) {
                throw new \Az2009\Cielo\Exception\Cc(
                    __('                
                        The request has already been captured. Cielo only supports one partial or full capture. 
                        A catch is already created for this request. 
                        Capture offline at the store and online at Cielo\'s backoffice.
                    ')
                );
            }

            $payment->setAdditionalInformation('can_capture', true);
            $this->post();

        }

        $this->request();
    }

    /**
     * set amount total to authorize and capture
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param $amount
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function setAmount(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($amount <= 0) {
            throw new \Az2009\Cielo\Exception\Cc(__('Invalid amount for capture.'));
        }

        $payment->setAmount($amount);
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return parent::isAvailable();
    }
}