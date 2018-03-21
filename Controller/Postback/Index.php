<?php

namespace Az2009\Cielo\Controller\Postback;

use Magento\Framework\App\Action\Context;

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


    public function __construct(
        Context $context,
        \Az2009\Cielo\Model\Method\Cc\Postback $postback,
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order
    ) {
        $this->_postback = $postback;
        $this->_session = $session;
        $this->_order = $order;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->getResponse();

        if ($this->_isValid()) {

            switch ($this->_changeType) {
                case 1:
                    $this->_postback
                         ->setPaymentId($this->_paymentId)
                         ->process();
                break;
            }

           $response->setHttpResponseCode(200);
           $response->clearBody();
           $response->sendHeaders();
        }
    }

    protected function _isValid()
    {
        $request = $this->getRequest();

         if(!$request->isPost()
            || !($this->_paymentId = $request->getParam('PaymentId'))
            || !($this->_changeType = $request->getParam('ChangeType'))
        ) {
            return false;
        }

        return true;
    }
}