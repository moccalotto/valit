<?php

namespace Moccalotto\Valit\Test;

class ContainerTestClass
{
    public $public = 'propertyAlreadyExists';
    protected $protected = 'propertyAlreadyExists';

    public function validationData()
    {
        return [
            'foo' => __FUNCTION__,
            'bar' => __FUNCTION__,
            'baz' => [
                'thing1' => __FUNCTION__,
                'thing2' => __FUNCTION__,
            ],
            'priority' => __FUNCTION__,
            'public' => __FUNCTION__,
            'protected' => __FUNCTION__,
        ];
    }

    public function __debugInfo()
    {
        return [
            'foo' => __FUNCTION__,
            'bar' => __FUNCTION__,
            'baz' => [
                'thing1' => __FUNCTION__,
                'thing2' => __FUNCTION__,
            ],
            'priority' => __FUNCTION__,
            'public' => __FUNCTION__,
            'protected' => __FUNCTION__,
        ];
    }

    public function jsonSerialize()
    {
        return [
            'foo' => __FUNCTION__,
            'bar' => __FUNCTION__,
            'baz' => [
                'thing1' => __FUNCTION__,
                'thing2' => __FUNCTION__,
            ],
            'priority' => __FUNCTION__,
            'public' => __FUNCTION__,
            'protected' => __FUNCTION__,
        ];
    }
}
