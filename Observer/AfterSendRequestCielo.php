<?php
namespace Az2009\Cielo\Observer;

class AfterSendRequestCielo implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Az2009\Cielo\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observe)
    {
        if (!$this->helper->canDebug()) {
            return $this;
        }

        $client = $observe->getEvent()->getClient();

        if ($client instanceof \Magento\Framework\HTTP\ZendClient) {
            $lastRequest = $client->getLastRequest();
            $lastResponse = $client->getLastResponse();
            $requestId = $client->getHeader('RequestId');
            $debug = "\n\n\n Request - {$requestId}: \n {$lastRequest}\n".
                     "\n\n\n Response - {$requestId}: \n {$lastResponse} \n";

            $this->helper->getLogger()->info($debug);
        }
    }
}