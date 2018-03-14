<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Unauthorized extends \Az2009\Cielo\Model\Method\Transaction
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;


    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        $this->logger = $logger;
        parent::__construct($session, $data);
    }

    public function process()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);

        $message = __('Unauthorized Payment Action');

        if (isset($bodyArray['Payment']['ReturnMessage'])) {
            $message = $bodyArray['Payment']['ReturnMessage'];
        }

        throw new \Az2009\Cielo\Exception\Cc(
            __('The acquirer\'s response: %1', $message)
        );
    }

}