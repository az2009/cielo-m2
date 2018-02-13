<?php

namespace Az2009\Cielo\Model\Method;

use \Magento\Framework\DataObject;

abstract class AbstractMethod extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * @var DataObject
     */
    protected $request;

    /**
     * @var DataObject
     */
    protected $response;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    /**
     * @var string
     */
    protected $keyRequest;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        DataObject $request,
        DataObject $response,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->helper = $helper;
        $this->response = $response;
        $this->request = $request;
        $this->httpClientFactory = $httpClientFactory->create();
    }

    /**
     * @return DataObject
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return DataObject
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Magento\Framework\HTTP\ZendClient
     */
    public function getClient()
    {
        $this->keyRequest = $this->helper->getKeyRequest();

        return $this->httpClientFactory
                    ->setHeaders(
                        array(
                            'Content-Type' => 'application/json',
                            'MerchantId' => $this->helper->getMerchantId(),
                            'MerchantKey' => $this->helper->getMerchantKey(),
                            'RequestId' => $this->keyRequest,
                        )
                    );
    }

    public function post(\Magento\Framework\DataObject $data)
    {
        $this->getClient()->setParameterPost($this->getParams($data));

        return $this;
    }

    public function get(\Magento\Framework\DataObject $data)
    {
        $this->getClient()->setParameterGet($this->getParams($data));

        return $this;
    }

    public function getParams(\Magento\Framework\DataObject $payment)
    {
        return $this->getRequest()
                    ->setPayment($payment)
                    ->buildRequest();
    }

    public function request()
    {
        try {
            $request = $this->getClient()->request();
        } catch(\Exception $e) {
            $this->_logger->error($e->getMessage());
        }

        return $request;
    }

}