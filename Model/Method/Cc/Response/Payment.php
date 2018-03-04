<?php

namespace Az2009\Cielo\Model\Method\Cc\Response;

class Payment extends \Az2009\Cielo\Model\Method\Response
{

    const STATUS_AUTHORIZED = '1';

    const STATUS_CANCELED = '10';
    const STATUS_CANCELED_AFTER = '11';

    const STATUS_CANCELED_PARTIAL = '2';

    const STATUS_CAPTURED = '2';

    const STATUS_PENDING = '12';

    const STATUS_PAYMENT_REVIEW = '0';

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Authorize
     */
    protected $_authorize;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Capture
     */
    protected $_capture;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\General
     */
    protected $_pending;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Unauthorized
     */
    protected $_unauthorized;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Transaction\Cancel
     */
    protected $_cancel;


    public function __construct(
        \Az2009\Cielo\Model\Method\Cc\Transaction\Authorize $authorize,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Unauthorized $unauthorized,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Capture $capture,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Pending $pending,
        \Az2009\Cielo\Model\Method\Cc\Transaction\Cancel $cancel,
        array $data = []
    ) {
        $this->_unauthorized = $unauthorized;
        $this->_authorize = $authorize;
        $this->_capture = $capture;
        $this->_pending = $pending;
        $this->_cancel = $cancel;

        parent::__construct($data);
    }

    public function process()
    {
        parent::process();

        switch ($this->getStatus()) {
            case Payment::STATUS_AUTHORIZED:
                $this->_authorize
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->process();
            break;
            case Payment::STATUS_CAPTURED:
                $this->_capture
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->process();
            break;
            case Payment::STATUS_CANCELED_AFTER:
            case Payment::STATUS_CANCELED:
                $this->_cancel
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->process();
                break;
            case Payment::STATUS_PAYMENT_REVIEW:
            case Payment::STATUS_PENDING:
                $this->_pending
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->process();
            break;
            default:
                $this->_unauthorized
                     ->setPayment($this->getPayment())
                     ->setResponse($this->getResponse())
                     ->process();
            break;
        }
    }

    /**
     * get status payment
     * @return mixed
     * @throws \Exception
     */
    public function getStatus()
    {
        $body = $this->getBody();
        if (property_exists($body, 'Payment')) {
            $status = $body->Payment->Status;
            return $this->isStatusCanceled($status);
        } elseif (property_exists($body, 'Status')) {
            $status = $body->Status;
            return $this->isStatusCanceled($status);
        }

        throw new \Exception('invalid payment status');
    }

    /**
     * check status canceled when partial
     * @param $status
     * @return string
     */
    public function isStatusCanceled($status)
    {
        $payment = $this->getPayment();
        if ($payment->getActionCancel()
            && $status == Payment::STATUS_CANCELED_PARTIAL) {
            $status = self::STATUS_CANCELED;
        }

        return $status;
    }

}