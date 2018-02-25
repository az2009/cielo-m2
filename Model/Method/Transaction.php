<?php

namespace Az2009\Cielo\Model\Method;

class Transaction extends \Az2009\Cielo\Model\Method\Response
{
    protected $_transactionData = array();


    /**
     * set body response to transaction of payment
     * @param array $data
     */
    public function prepareBodyTransaction(Array $data, $key = '')
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $this->prepareBodyTransaction($v, $k);
            } else {
                if (is_bool($v)) {
                    if ($v) {
                        $v = 'true';
                    } else {
                        $v = 'false';
                    }
                }

                $this->_transactionData[$key."_".$k] = $v;
            }
        }
    }

    /**
     * get instance payment
     * @return \Magento\Sales\Model\Order\Payment
     * @throws \Az2009\Cielo\Exception\Cc
     */
    public function getPayment()
    {
        $payment = $this->getData('payment');
        if (!($payment instanceof \Magento\Payment\Model\InfoInterface)) {
            throw new \Az2009\Cielo\Exception\Cc(
                __('Occurred an error during payment process. Try Again.')
            );
        }

        return $payment;
    }

    /**
     * @return array
     */
    public function getTransactionData()
    {
        return $this->_transactionData;
    }

}