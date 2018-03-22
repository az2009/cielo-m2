<?php

namespace Az2009\Cielo\Model\Source;

class CustomerAttribute
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    public function __construct(\Magento\Customer\Model\Customer $customer)
    {
        $this->_customer = $customer;
    }

    public function toOptionArray()
    {
        $options = array();
        $attributes = $this->_customer->getAttributes();
        $options[] = ['label' => __('Select a Attribute'), 'value' => ''];
        foreach ($attributes as $attr) {
            $options[] = [
                'label' => $attr->getAttributeCode(),
                'value' => $attr->getAttributeCode(),
            ];
        }

        return $options;
    }
}