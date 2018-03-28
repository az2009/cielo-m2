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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Az2009\Cielo\Helper\Data $helper
    ) {
        $this->_session = $session;
        $this->logger = $logger;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(403);
        $msg = '';
        if ($this->_isValid()
            && $this->_changeType == \Az2009\Cielo\Helper\Data::CHANGE_TYPE
        ) {
            try {

                $postback = $this->helper->getPostbackByTransId($this->_paymentId);
                if (!($postback instanceof \Az2009\Cielo\Model\Method\AbstractMethod)) {
                    throw new \Exception((string)__('Order Not Found to PaymentId %1', $this->_paymentId));
                }

                $postback->setPaymentId($this->_paymentId)
                         ->process();

                $response->setHttpResponseCode(200);

            } catch(\Exception $e) {
                echo $e->getMessage();
                $code = mt_rand(2, 9999);
                $msg = __('CodeError: %1', $code);
                $this->logger->error(__("\n \n \n PostbackError: \n Code: %1 \n Message: %2", $code, $e->getMessage()));
                $response->setHttpResponseCode(500);
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