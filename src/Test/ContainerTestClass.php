<?php

namespace Valit\Test;

use ArrayIterator;
use IteratorAggregate;

class ContainerTestClass implements IteratorAggregate
{
    public $public = 'propertyAlreadyExists';
    protected $protected = 'propertyAlreadyExists';

    public function getIterator()
    {
        return new ArrayIterator([
            'foo' => 'iterator',
            'bar' => 'iterator',
            'baz' => [
                'thing1' => 'iterator',
                'thing2' => 'iterator',
            ],
            'priority' => 'iterator',
            'public' => 'iterator',
            'protected' => 'iterator',
        ]);
    }

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
