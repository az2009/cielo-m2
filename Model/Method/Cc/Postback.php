<?php

namespace Az2009\Cielo\Model\Method\Cc;

class Postback extends \Az2009\Cielo\Model\Method\AbstractMethod
{

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\DataObject $request,
        Response\Postback $response,
        Validate\Validate $validate,
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
        $this->_uri = $this->helper->getUriQueryStage();
    }

    public function process()
    {
        $paymentId = $this->getPaymentId();
        $this->setPath($paymentId, '');
        $this->request();
    }

    /**
     * process response
     * @param $response
     */
    protected function _processResponse($response)
    {
        $this->getResponse()
             ->setResponse($response)
             ->process();
    }

    /**
     * validate paymentId
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentId()
    {
        $paymentId = $this->getData('payment_id');

        if (empty($paymentId)) {
            throw new \Exception(__('payment_id empty to send the postback'));
        }

        return $paymentId;
    }
}