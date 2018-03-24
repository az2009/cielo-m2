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
            ['label' => __('Bradesco'), 'value' => 'Bradesco2'],
            ['label' => __('Banco do Brasil'), 'value' => 'BancoDoBrasil2']
        ];
    }
}