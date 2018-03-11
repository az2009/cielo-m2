<?php

namespace Az2009\Cielo\Block\Adminhtml\Form\Field;

class Installment
    extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected function _prepareToRender()
    {
        $this->addColumn('installment_boundary', [
            'label' => __('Amount (incl.)'),
            'style' => 'width:100px',
        ]);

        $this->addColumn('installment_frequency', [
            'label' => __('Maximum Number of Installments'),
            'style' => 'width:100px',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Installment Boundary');
    }
}
