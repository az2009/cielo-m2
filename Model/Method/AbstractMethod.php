<?php

namespace Az2009\Cielo\Model\Method;

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
     * @var array
     */
    protected $_path = [];

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var string
     */
    protected $_uri;

    /**
     * @var Validate
     */
    protected $validate;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\DataObject $request,
        \Magento\Framework\DataObject $response,
        Validate $validate,
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

        $this->validate = $validate;
        $this->helper = $helper;
        $this->response = $response;
        $this->request = $request;
        $this->httpClientFactory = $httpClientFactory->create();
        $this->_uri = $this->helper->getUriRequest();
    }

    protected function _validate()
    {
        $this->validate
            ->setPayment($this)
            ->validate();
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
            ->setUri($this->getUri())
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
     * get uri to request
     * @return mixed|string
     */
    public function getUri()
    {
        $uri = $this->_uri . '/' . $this->getPath();
        $uri = $this->helper->sanitizeUri($uri);
        return $uri;
    }

    /**
     * send request of type PUT
     * @return $this
     */
    public function put()
    {
        $params = $this->getParams();
        $this->getClient()
            ->setRawData($params)
            ->setMethod($this->httpClientFactory::PUT);

        return $this;
    }

    /**
     * send request of type POST
     * @return $this
     */
    public function post()
    {
        $params = $this->getParams();
        $this->getClient()
            ->setMethod($this->httpClientFactory::POST)
            ->setRawData($params);

        return $this;
    }

    /**
     * send request of type GET
     * @return $this
     */
    public function get()
    {
        $params = $this->getParams();
        $this->getClient()
            ->setMethod($this->httpClientFactory::GET)
            ->setRawData($params);

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $path = '';
        foreach ($this->_path as $k => $v) {
            if (!empty($v)) {
                $path .= $k .'/'.$v;
            } else {
                $path .= $k;
            }
        }

        return $path;
    }

    /**
     * @param $k
     * @param $v
     */
    public function setPath($k, $v)
    {
        $this->_path[$k] = $v;

        return $this;
    }

    /**
     * get params of request
     * @param DataObject $payment
     * @return mixed
     */
    public function getParams()
    {
        $request = $this->getRequest()
            ->setPaymentData($this->getInfoInstance())
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

            if ($this->getRunValidate()) {
                $this->_validate();
            }

            $this->_eventManager->dispatch(
                'before_send_request_cielo',
                ['client' => $this->getClient()]
            );

            $response = $this->getClient()->request();

            $this->getResponse()
                 ->setResponse($response);

            $this->_eventManager->dispatch(
                'after_send_request_cielo',
                ['client' => $this->getClient()]
            );

            $this->_processResponse();

        } catch(\Az2009\Cielo\Exception\Cc $e) {
            throw $e;
        } catch(\Exception $e) {
            $message = 'Occurred an error during payment process. Try Again.';
            $this->isCatchException($response, $message);
            $this->_logger->error($e->getMessage());
            throw new \Exception(__($message));
        }

        return $this;
    }

    /**
     * check if exception is message to the user
     * @param \Zend_Http_Response $response
     * @param $message
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function isCatchException(\Zend_Http_Response $response, $message)
    {
        if ($response->getStatus() == \Zend\Http\Response::STATUS_CODE_400
            && isset(\Zend\Json\Decoder::decode($response->getBody())[0])
            && property_exists(\Zend\Json\Decoder::decode($response->getBody())[0], 'Message')
            && $message = (string)\Zend\Json\Decoder::decode($response->getBody())[0]->Message
        ) {
            throw new \Az2009\Cielo\Exception\Cc(__($message));
        }
    }

    /**
     * process response
     * @param $response
     */
    protected function _processResponse()
    {
        $this->getResponse()
            ->setPayment($this->getInfoInstance())
            ->process();
    }

}