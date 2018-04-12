<?php

namespace Az2009\Cielo\Model\Source;

class Provider
{
    /**
     * @return string[]
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Bradesco (Not Registered)'), 'value' => 'Bradesco'],
            ['label' => __('Banco do Brasil (Not Registered)'), 'value' => 'BancoDoBrasil'],
            ['label' => __('Bradesco (Registered)'), 'value' => 'Bradesco2'],
            ['label' => __('Banco do Brasil (Registered)'), 'value' => 'BancoDoBrasil2']
        ];
    }
}