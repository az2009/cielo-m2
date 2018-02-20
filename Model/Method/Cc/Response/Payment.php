<?php

namespace Az2009\Cielo\Model\Method\Cc\Response;

class Payment extends \Az2009\Cielo\Model\Method\Response
{
    protected $_statusCodeAllowed = [1,2];

    public function process()
    {
        parent::process();
    }

    /**
     * checks if request errors occurred
     * @return $this
     * @throws LocalizedException
     * @throws \Exception
     */
    public function hasError()
    {
        parent::hasError();

        if (property_exists($this->getBody(), 'Payment')
            && !in_array($this->getBody()->Payment->Status, $this->_statusCodeAllowed)) {

            $message = $this->getBody()->Payment->ReturnMessage;
            throw new \Az2009\Cielo\Exception\Cc(__($message));
        }
    }

}