<?php

namespace Az2009\Cielo\Model\Method\Cc\Transaction;

class Cancel extends \Az2009\Cielo\Model\Method\Transaction
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->logger = $logger;
        parent::__construct($data);
    }

    public function process()
    {
        $bodyArray = $this->getBody(\Zend\Json\Json::TYPE_ARRAY);

        $this->logger->info(
            var_export($bodyArray, true)
        );

        throw new \Az2009\Cielo\Exception\Cc(__('Unauthorized Payment'));
    }

}