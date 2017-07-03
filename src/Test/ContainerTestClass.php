<?php

namespace Moccalotto\Valit\Test;

class ContainerTestClass
{
    public $publicExists = 'propertyAlreadyExists';
    protected $privateExists = 'propertyAlreadyExists';

    public function validationData()
    {
        return [
            'validationData_foo' => __FUNCTION__,
            'validationData_bar' => __FUNCTION__,
            'validationData_baz' => [
                'thing1' => __FUNCTION__,
                'thing2' => __FUNCTION__,
            ],
            'priority' => __FUNCTION__,
            'publicExists' => __FUNCTION__,
            'privateExists' => __FUNCTION__,
        ];
    }

    public function __debugInfo()
    {
        return [
            '__debugInfo_foo' => __FUNCTION__,
            '__debugInfo_bar' => __FUNCTION__,
            '__debugInfo_baz' => [
                'thing1' => __FUNCTION__,
                'thing2' => __FUNCTION__,
            ],
            'priority' => __FUNCTION__,
            'publicExists' => __FUNCTION__,
            'privateExists' => __FUNCTION__,
        ];
    }

    public function jsonSerialize()
    {
        return [
            'jsonSerialize_foo' => __FUNCTION__,
            'jsonSerialize_bar' => __FUNCTION__,
            'jsonSerialize_baz' => [
                'thing1' => __FUNCTION__,
                'thing2' => __FUNCTION__,
            ],
            'priority' => __FUNCTION__,
            'publicExists' => __FUNCTION__,
            'privateExists' => __FUNCTION__,
        ];
    }
}
