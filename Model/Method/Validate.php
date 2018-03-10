<?php

namespace Az2009\Cielo\Model\Method;

use Magento\Framework\DataObject;

abstract class Validate extends DataObject
{

    protected $_fieldsValidate = [];

    /**
     * get instance payment
     * @return \Magento\Sales\Model\Order\Payment
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function getPayment()
    {
        $payment = $this->getData('payment');
        if (!($payment instanceof \Az2009\Cielo\Model\Method\AbstractMethod)) {
            throw new \Az2009\Cielo\Exception\Cc(
                __('Occurred an error during payment process. Try Again.')
            );
        }

        return $payment;
    }

    /**
     * get request before send
     * @return mixed
     */
    public function getRequest()
    {
        $params = $this->getPayment()->getParams();
        $params = json_decode($params, true);
        return $params;
    }

    /**
     * validate fields required
     * @param $key
     * @param $value
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function required($key, $value, $prefix = '')
    {
        if (isset($this->_fieldsValidate[$key])
            && $this->_fieldsValidate[$key]['required'] === true
            && empty($value)
        ) {
            throw new \Az2009\Cielo\Exception\Cc(__('%1 Field %2 required', $prefix, $key));
        }
    }

    /**
     * validate length of the fields
     * @param $key
     * @param $value
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function maxLength($key, $value, $prefix = '')
    {
        if (isset($this->_fieldsValidate[$key]['maxlength'])
            && strlen($value) > $this->_fieldsValidate[$key]['maxlength']
        ) {
            throw new \Az2009\Cielo\Exception\Cc(
                __(
                    '%1 Field %2 exceed limit of %3 characters',
                    $prefix,
                    $key,
                    $this->_fieldsValidate[$key]['maxlength']
                )
            );
        }
    }

    /**
     * Todo validation
     */
    public abstract function validate();

}