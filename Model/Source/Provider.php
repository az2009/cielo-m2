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
            ['label' => __('Bradesco (Não Registrado)'), 'value' => 'Bradesco'],
            ['label' => __('Banco do Brasil (Não Registrado)'), 'value' => 'BancoDoBrasil'],
            ['label' => __('Bradesco (Registrado)'), 'value' => 'Bradesco2'],
            ['label' => __('Banco do Brasil (Registrado)'), 'value' => 'BancoDoBrasil2']
        ];
    }
}