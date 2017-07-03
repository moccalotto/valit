<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Moccalotto\Valit\Util;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FlattenedContainerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith([]);

        $this->shouldHaveType('Moccalotto\Valit\Util\FlattenedContainer');
    }

    function it_can_find_data()
    {
        $this->beConstructedWith([
            'a' => [
                'b' => [
                    'c' => 'd'
                ]
            ],
            'x' => '2',
            'y' => 4,
        ]);

        $this->find('a')->shouldBe([
            'a' => [
                'b' => [
                    'c' => 'd'
                ]
            ],
        ]);
        $this->find('a/b')->shouldBe([
            'a/b' => [
                'c' => 'd'
            ]
        ]);
        $this->find('a/b/c')->shouldBe(['a/b/c' => 'd']);
        $this->find('x')->shouldBe(['x' => '2']);
        $this->find('y')->shouldBe(['y' => 4]);
    }

    function it_expands_objects()
    {
        $this->beConstructedWith(
            ['object' => new \Moccalotto\Valit\Test\ContainerTestClass()]
        );

        $container = $this->container;

        $container['object']->validationData_foo->shouldBe('validationData');
        $container['object']->validationData_baz->shouldBeArray();
        $container['object']->validationData_baz['thing1']->shouldBe('validationData');
        $container['object']->validationData_baz['thing2']->shouldBe('validationData');

        $container['object']->__debugInfo_foo->shouldBe('__debugInfo');
        $container['object']->__debugInfo_baz->shouldBeArray();
        $container['object']->__debugInfo_baz['thing1']->shouldBe('__debugInfo');
        $container['object']->__debugInfo_baz['thing2']->shouldBe('__debugInfo');

        $container['object']->jsonSerialize_foo->shouldBe('jsonSerialize');
        $container['object']->jsonSerialize_baz->shouldBeArray();
        $container['object']->jsonSerialize_baz['thing1']->shouldBe('jsonSerialize');
        $container['object']->jsonSerialize_baz['thing2']->shouldBe('jsonSerialize');

        $container['object']->priority->shouldBe('validationData');
        $container['object']->publicExists->shouldBe('propertyAlreadyExists');
    }
}
