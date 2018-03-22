<?php

namespace Az2009\Cielo\Model\Source;

class Mode
{
    const MODE_STAGE = 'stage';

    const MODE_PRODUCTION = 'production';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Stage'), 'value' => self::MODE_STAGE],
            ['label' => __('Production'), 'value' => self::MODE_PRODUCTION]
        ];
    }
}