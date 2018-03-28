<?php

namespace Az2009\Cielo\Model\Source;

class State
{

    protected $_statesBrazilian = [
        'AC'=>'Acre',
        'AL'=>'Alagoas',
        'AP'=>'Amapá',
        'AM'=>'Amazonas',
        'BA'=>'Bahia',
        'CE'=>'Ceará',
        'DF'=>'Distrito Federal',
        'ES'=>'Espírito Santo',
        'GO'=>'Goiás',
        'MA'=>'Maranhão',
        'MT'=>'Mato Grosso',
        'MS'=>'Mato Grosso do Sul',
        'MG'=>'Minas Gerais',
        'PA'=>'Pará',
        'PB'=>'Paraíba',
        'PR'=>'Paraná',
        'PE'=>'Pernambuco',
        'PI'=>'Piauí',
        'RJ'=>'Rio de Janeiro',
        'RN'=>'Rio Grande do Norte',
        'RS'=>'Rio Grande do Sul',
        'RO'=>'Rondônia',
        'RR'=>'Roraima',
        'SC'=>'Santa Catarina',
        'SP'=>'São Paulo',
        'SE'=>'Sergipe',
        'TO'=>'Tocantins'
    ];

    /**
     * get code state
     *
     * @param $state
     * @return false|int|mixed|string
     */
    public function getState($state)
    {
        $state = strtolower($state);
        $this->_statesBrazilian = array_map('strtolower', $this->_statesBrazilian);
        $code = array_search($state, $this->_statesBrazilian);
        if(isset($this->_statesBrazilian[$state]) && empty($code)) {
            $code = $state;
        }

        return strtoupper($code);
    }
}