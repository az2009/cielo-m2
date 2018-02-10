<?php

namespace Az2009\Cielo\Model\Method;

class Cc extends AbstractPaymentMethod
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


}