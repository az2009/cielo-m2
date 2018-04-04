<?php

namespace Az2009\Cielo\Model\Method\Dc\Response;

class Payment extends \Az2009\Cielo\Model\Method\Cc\Response\Payment
{
    public function __construct(
        \Az2009\Cielo\Model\Method\Dc\Transaction\Authorize $authorize,
        \Az2009\Cielo\Model\Method\Dc\Transaction\Unauthorized $unauthorized,
        \Az2009\Cielo\Model\Method\Dc\Transaction\Capture $capture,
        \Az2009\Cielo\Model\Method\Dc\Transaction\Pending $pending,
        \Az2009\Cielo\Model\Method\Dc\Transaction\Cancel $cancel,
        array $data = []
    ) {
        parent::__construct($authorize, $unauthorized, $capture, $pending, $cancel, $data);
    }

    public function process()
    {
        if (!$this->getPayment()) {
            $this->_getPaymentInstance();
        }

        switch ($this->getStatus()) {
            case Payment::STATUS_AUTHORIZED:
            case Payment::STATUS_CAPTURED:
                $this->_capture
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->setPostback(true)
                    ->process();
            break;
            case Payment::STATUS_PAYMENT_REVIEW:
            case Payment::STATUS_PENDING:
                $this->_pending
                    ->setPayment($this->getPayment())
                    ->setResponse($this->getResponse())
                    ->process();
            break;
            case Payment::STATUS_CANCELED_DENIED:
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
}