<?php

namespace Az2009\Cielo\Model\Method\Cc\Response;

class Postback extends \Az2009\Cielo\Model\Method\Cc\Response\Payment
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    public function __construct(
        \Az2009\Cielo\Model\Method\Cc\Transaction\Authorize $authorize,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Unauthorized $unauthorized,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Capture $capture,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Pending $pending,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Cancel $cancel,
        \Magento\Sales\Model\Order $order,
        array $data = []
    ) {
        $this->_order = $order;

        parent::__construct($authorize, $unauthorized, $capture, $pending, $cancel, $data);
    }

    public function process()
    {
        if (!$this->getPayment()) {
            $this->_getPaymentInstance();
        }

        switch ($this->getStatus()) {
            case 0:
            case Payment::STATUS_AUTHORIZED:
                $this->_authorize
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->setPostback(true)
                    ->process();
            break;
            case Payment::STATUS_CAPTURED:
                $this->_capture
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->setPostback(true)
                     ->process();
            break;
            case Payment::STATUS_CANCELED_ABORTED:
            case Payment::STATUS_CANCELED_AFTER:
            case Payment::STATUS_CANCELED:
                $this->_cancel
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->setPostback(true)
                    ->process();
            break;
        }
    }

    /**
     * get payment instance of order
     * @return $this
     */
    protected function _getPaymentInstance()
    {
        $orderId = $this->_getMerchantOrderId();
        $this->_order = $this->_order->loadByIncrementId($orderId);
        $this->setOrder($this->_order);
        $this->setPayment($this->_order->getPayment());

        return $this;
    }

    /**
     * get order id of post
     * @return mixed
     * @throws \Exception
     */
    protected function _getMerchantOrderId()
    {
        $response = $this->getBody();
        if (!property_exists($response, 'MerchantOrderId')) {
            throw new \Exception(__('Proper MerchantOrderId not found'));
        }

        return $response->MerchantOrderId;
    }

}