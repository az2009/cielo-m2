<?php

namespace Az2009\Cielo\Model\Method\Cc;

class Cc extends \Az2009\Cielo\Model\Method\AbstractMethod
{

    protected $_code = 'az2009_cielo';

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canCancelInvoice = true;


    /**
     * @var string
     */
    protected $_formBlockType = \Az2009\Cielo\Block\Form\Cc::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \Az2009\Cielo\Block\Info\Cc::class;


    public function validate()
    {
        return true;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::refund($payment, $amount);
    }

    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::cancel($payment);
    }

    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::void($payment);
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $r = 'vb';
        $this->post($payment);
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return true;
    }
}