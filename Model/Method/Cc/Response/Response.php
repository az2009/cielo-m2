<?php

namespace Az2009\Cielo\Model\Method\Cc\Response;

use Braintree\Exception;
use Magento\Framework\Exception\LocalizedException;

class Response extends \Magento\Framework\DataObject
{
    protected $_statusCodeAllowed = [1,2];

    public function processResponse()
    {
        $payment = $this->getPayment();
        $response = $this->prepareResponse();
        $payment->setAdditionalInformation(
            'body_response',
            $response->getBody()
        );
    }

    public function prepareResponse()
    {
        /** @var \Zend\Http\Response $response */
        $response = $this->getResponseOfRequest();
        $this->setResponse($response);

        $body = json_decode($response->getBody());
        $this->setBody($body);

        $this->hasError();

        return $response;
    }

    public function hasError()
    {
        $message = __('Occurred an Error');

        if ($message = $this->getRequestError() || !is_object($this->getBody())) {
            throw new \Exception(__($message));
        }

        if (!in_array($this->getBody()->Payment->Status, $this->_statusCodeAllowed)
            && isset($this->getBody()->Payment->ReturnMessage)) {
            $message = $this->getBody()->Payment->ReturnMessage;
            throw new LocalizedException(__($message));
        }

        return $this;
    }

}