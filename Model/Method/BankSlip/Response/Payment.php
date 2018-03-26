<?php

namespace Az2009\Cielo\Model\Method\BankSlip\Response;

class Payment extends \Az2009\Cielo\Model\Method\Cc\Response\Payment
{
    public function __construct(
        \Az2009\Cielo\Model\Method\BankSlip\Transaction\Authorize $authorize,
        \Az2009\Cielo\Model\Method\BankSlip\Transaction\Unauthorized $unauthorized,
        \Az2009\Cielo\Model\Method\BankSlip\Transaction\Capture $capture,
        \Az2009\Cielo\Model\Method\BankSlip\Transaction\Pending $pending,
        \Az2009\Cielo\Model\Method\BankSlip\Transaction\Cancel $cancel,
        array $data = []
    ) {
        parent::__construct($authorize, $unauthorized, $capture, $pending, $cancel, $data);
    }
}