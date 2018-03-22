<?php

namespace Az2009\Cielo\Controller\Postback;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var string
     */
    protected $_paymentId;

    /**
     * @var string
     */
    protected $_changeType;

    /**
     * @var \Az2009\Cielo\Model\Method\Cc\Postback
     */
    protected $_postback;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Az2009\Cielo\Model\Method\Cc\Postback $postback,
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_postback = $postback;
        $this->_session = $session;
        $this->_order = $order;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(403);
        $msg = '';
        if ($this->_isValid()) {
            switch ($this->_changeType) {
                case 1:
                    try {
                        $this->_postback
                             ->setPaymentId($this->_paymentId)
                             ->process();
                        $response->setHttpResponseCode(200);
                    }catch (\Exception $e) {
                        $code = mt_rand(2, 9999);
                        $msg = __('CodeError: %1', $code);
                        $this->logger->error(__("\n \n \n PostbackError: \n Code: %1 \n Message: %2", $code, $e->getMessage()));
                        $response->setHttpResponseCode(500);
                    }
                break;
            }

            $response->clearBody();
            $response->setBody($msg);
            $response->sendHeaders();
        }
    }

    protected function _isValid()
    {
        $request = $this->getRequest();

         if(/*!$request->isPost()
            ||*/ !($this->_paymentId = $request->getParam('PaymentId'))
            || !($this->_changeType = $request->getParam('ChangeType'))
        ) {
            return false;
        }

        return true;
    }
}