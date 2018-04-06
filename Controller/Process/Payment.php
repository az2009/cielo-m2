<?php

namespace Az2009\Cielo\Controller\Process;

class Payment extends \Az2009\Cielo\Controller\Postback\Index
{
    public function execute()
    {
        $url = '/';
        if ($this->_isValid()) {
            try {

                $postback = $this->helper->getPostbackByTransId($this->_paymentId);
                if (!($postback instanceof \Az2009\Cielo\Model\Method\AbstractMethod)) {
                    throw new \Exception((string)__('Order Not Found to PaymentId %1', $this->_paymentId));
                }

                $postback->setPaymentId($this->_paymentId)
                         ->process();

                if ($this->registry->registry('payment_captured')) {
                    return $this->_redirect('checkout/onepage/success');
                }

            } catch(\Az2009\Cielo\Exception\Cc $e) {
                $this->messageManager->addError($e->getMessage());
            } catch(\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError(__('Occurred an error during payment process. Contact the store'));
            }

            $url = 'checkout/onepage/failure';

        }

        return $this->_redirect($url);
    }

    protected function _isValid()
    {
        $request = $this->getRequest();

        if(/*!$request->isPost()
            ||*/ !($this->_paymentId = $request->getParam('PaymentId'))
        ) {
            return false;
        }

        return true;
    }

}