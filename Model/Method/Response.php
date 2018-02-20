<?php

namespace Az2009\Cielo\Model\Method;

use Magento\Framework\Exception\LocalizedException;

class Response extends \Magento\Framework\DataObject
{
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
     * get body of request
     * @return mixed|string
     */
    public function getBody()
    {
        $body = $this->getResponse()->getBody();
        $body = \Zend\Json\Json::decode($body);

        return $body;
    }

    /**
     * @return \Zend\Http\Response
     * @throws Exception
     */
    public function getResponse()
    {
        $response = $this->getData('response');
        if (!($response instanceof \Zend\Http\Response)) {
            throw new \Exception('invalid response');
        }

        return $response;
    }

}