<?php

namespace Az2009\Cielo\Model\Method;

use \Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

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
     * get instance client
     * @return \Magento\Framework\HTTP\ZendClient
     */
    public function getClient()
    {
        $this->keyRequest = $this->helper->getKeyRequest();

        return $this->httpClientFactory
                    ->setUri($this->helper->getRequestUriStage())
                    ->setHeaders(
                        [
                            'Content-Type' => 'application/json',
                            'MerchantId' => $this->helper->getMerchantId(),
                            'MerchantKey' => $this->helper->getMerchantKey(),
                            'RequestId' => $this->keyRequest,
                        ]
                    );
    }

    /**
     * send request of type POST
     * @param DataObject $data
     * @return $this
     */
    public function post(\Magento\Framework\DataObject $data)
    {
        $params = $this->getParams($data);
        $this->getClient()
             ->setMethod($this->httpClientFactory::POST)
             ->setRawData($params);

        return $this;
    }

    /**
     * send request of type GET
     * @param DataObject $data
     * @return $this
     */
    public function get(\Magento\Framework\DataObject $data)
    {
        $params = $this->getParams($data);
        $this->getClient()
             ->setMethod($this->httpClientFactory::GET)
             ->setRawData($params);

        return $this;
    }

    /**
     * get params of request
     * @param DataObject $payment
     * @return mixed
     */
    public function getParams(\Magento\Framework\DataObject $payment)
    {
        $request = $this->getRequest()
                        ->setPaymentData($payment)
                        ->buildRequest();

        return $request;
    }

    /**
     * execute the request
     * @throws \Exception
     */
    public function request()
    {
        try {

            $this->_eventManager->dispatch(
                'before_send_request_cielo',
                ['client' => $this->getClient()]
            );

            $response = $this->getClient()->request();
            $this->getResponse()->setResponseOfRequest($response);

            $this->_eventManager->dispatch(
                'after_send_request_cielo',
                ['response' => $this->getResponse()]
            );

        } catch(\Exception $e) {
            $this->_logger->error($e->getMessage());
            $this->getResponse()
                 ->setRequestError(__('Not possible process payment'));
        }

        return $this;
    }

}