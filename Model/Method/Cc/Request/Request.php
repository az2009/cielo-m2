<?php

namespace Az2009\Cielo\Model\Method\Cc\Request;

use Magento\Framework\Event\ManagerInterface;

class Request extends \Az2009\Cielo\Model\Method\Request
{

    public function __construct(
        Customer $customer,
        Payment $payment,
        ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct($customer, $payment, $eventManager, $data);
    }

    const PREFIX_DISPATCH = 'after_prepare_request_params_cielo_cc';

}