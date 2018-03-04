<?php

namespace Az2009\Cielo\Model\Method\Cc;

use Magento\Framework\DataObject;

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
        DataObject $request,
        \Az2009\Cielo\Model\Method\Cc\Response\Postback $response,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Az2009\Cielo\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $request, $response, $httpClientFactory, $helper, $resource, $resourceCollection, $data);
    }

    public function process()
    {
        $paymentId = $this->getPaymentId();
        $this->setPath($paymentId, '');
        self::request();
    }

    /**
     * get uri to request
     * @return mixed|string
     */
    public function getUri()
    {
        $uri = (string)$this->helper->getUriQueryStage() .'/'. $this->getPath();
        $uri = str_replace('//', '/', $uri);
        $uri = str_replace(':/', '://', $uri);
        return $uri;
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

            $this->getResponse()
                 ->setResponse($response)
                 ->process();

            $this->_eventManager->dispatch(
                'after_send_request_cielo',
                ['response' => $this->getResponse()]
            );

        } catch(\Az2009\Cielo\Exception\Cc $e) {
            throw $e;
        } catch(\Exception $e) {
            $this->_logger->error($e->getMessage());
            throw new \Exception(__('Occurred an error during payment process. Try Again.'));
        }

        return $this;
    }

    /**
     * validate paymentId
     *
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentId()
    {
        $paymentId = $this->getData('payment_id');

        if (empty($paymentId)) {
            throw new \Exception('payment_id empty to send the postback');
        }

        return $paymentId;
    }
}