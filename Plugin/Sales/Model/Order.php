<?php

namespace Az2009\Cielo\Plugin\Sales\Model;

class Order
{

    /**
     * @var \Az2009\Cielo\Helper\Data
     */
    protected $helper;

    public function __construct(\Az2009\Cielo\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Model\Order $subject
     * @param $label
     * @return \Magento\Framework\Phrase|string
     */
    public function afterGetStatusLabel(
        \Magento\Sales\Model\Order $subject,
        $label
    )
    {
        if (in_array(
                $subject->getPayment()->getMethod(),
                $this->helper->getCodesPayment()
        ) && $subject->isPaymentReview()) {
            return $subject->getConfig()->getStateLabelByStateAndStatus(
                $subject->getState(),
                $subject->getStatus()
            );
        }

        return $label;
    }
}