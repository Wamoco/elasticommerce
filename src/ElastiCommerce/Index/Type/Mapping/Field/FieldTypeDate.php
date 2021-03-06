<?php

namespace SmartDevs\ElastiCommerce\Index\Type\Mapping\Field;

use SmartDevs\ElastiCommerce\Implementor\Index\Type\Mapping\Field\FieldTypeImplementor;

final class FieldTypeDate extends FieldTypeBase implements FieldTypeImplementor
{
    protected $validTypes = [
        'date',
    ];

    /**
     * valid parameters for generating mapping schema
     *
     * @var string[]
     */
    protected $supportedParameters = [
        'format'
    ];

    /**
     * valid attributes
     *
     * @var string[]
     */
    protected $validAttributes = ['type', 'store', 'index'];
}