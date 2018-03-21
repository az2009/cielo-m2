<?php

namespace Az2009\Cielo\Model\Method;

use Magento\Framework\Exception\LocalizedException;

class Response extends \Magento\Framework\DataObject
{

    /**
     * @var array
     */
    protected $_requestStatusAllowed = [201, 200];

    public function process()
    {
        $this->hasError();
    }

    /**
     * check if request occured an error
     * @return $this
     * @throws LocalizedException
     * @throws \Exception
     */
    public function hasError()
    {
        if ($message = $this->getRequestError()) {
            throw new \Exception(__($message));
        }

        if (!in_array($this->getResponse()->getStatus(),
            $this->_requestStatusAllowed)) {
            throw new \Exception(__($this->getMessage()));
        }

        return $this;
    }

    /**
     * get headers of request
     * @return \Zend\Http\Headers
     */
    public function getHeaders()
    {
        return $this->getResponse()->getHeaders();
    }

    /**
     * get message of request
     * @return string
     */
    public function getMessage()
    {
        return $this->getResponse()->getMessage();
    }

    /**
     * get body of request
     * @return mixed|string
     */
    public function getBody($type = \Zend\Json\Json::TYPE_OBJECT)
    {
        $body = $this->getResponse()->getBody();
        $body = \Zend\Json\Json::decode($body, $type);

        return $body;
    }

    /**
     * @return \Zend_Http_Response
     * @throws Exception
     */
    public function getResponse()
    {
        $response = $this->getData('response');
        if (!($response instanceof \Zend_Http_Response)) {
            throw new \Exception(__('invalid response'));
        }

        return $response;
    }

}