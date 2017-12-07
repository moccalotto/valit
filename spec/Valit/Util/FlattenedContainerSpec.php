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

namespace spec\Valit\Util;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FlattenedContainerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith([]);

        $this->shouldHaveType('Valit\Util\FlattenedContainer');
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
            ['o' => $o = new \Valit\Test\ContainerTestClass()]
        );

        $this->container['o']->shouldHaveType('Valit\Test\ContainerTestClass');

        $this->find('o')->shouldBe([
            'o' => $o,
        ]);

        $this->find('o/public')->shouldBe([
            'o/public' => 'propertyAlreadyExists',
        ]);

        $this->find('o/foo')->shouldBe([
            'o/foo' => 'validationData',
        ]);
        $this->find('o/bar')->shouldBe([
            'o/bar' => 'validationData',
        ]);
        $this->find('o/baz')->shouldBe([
            'o/baz' => [
                'thing1' => 'validationData',
                'thing2' => 'validationData',
            ]
        ]);


        $this->find('o/validationData/foo')->shouldBe([
            'o/validationData/foo' => 'validationData',
        ]);
        $this->find('o/validationData/bar')->shouldBe([
            'o/validationData/bar' => 'validationData',
        ]);
        $this->find('o/validationData/baz')->shouldBe([
            'o/validationData/baz' => [
                'thing1' => 'validationData',
                'thing2' => 'validationData',
            ]
        ]);
        $this->find('o/validationData/public')->shouldBe([
            'o/validationData/public' => 'validationData',
        ]);
        $this->find('o/validationData/protected')->shouldBe([
            'o/validationData/protected' => 'validationData',
        ]);



        $this->find('o/debugData/foo')->shouldBe([
            'o/debugData/foo' => '__debugInfo',
        ]);
        $this->find('o/debugData/bar')->shouldBe([
            'o/debugData/bar' => '__debugInfo',
        ]);
        $this->find('o/debugData/baz')->shouldBe([
            'o/debugData/baz' => [
                'thing1' => '__debugInfo',
                'thing2' => '__debugInfo',
            ]
        ]);
        $this->find('o/debugData/public')->shouldBe([
            'o/debugData/public' => '__debugInfo',
        ]);
        $this->find('o/debugData/protected')->shouldBe([
            'o/debugData/protected' => '__debugInfo',
        ]);


        $this->find('o/jsonData/foo')->shouldBe([
            'o/jsonData/foo' => 'jsonSerialize',
        ]);
        $this->find('o/jsonData/bar')->shouldBe([
            'o/jsonData/bar' => 'jsonSerialize',
        ]);
        $this->find('o/jsonData/baz')->shouldBe([
            'o/jsonData/baz' => [
                'thing1' => 'jsonSerialize',
                'thing2' => 'jsonSerialize',
            ]
        ]);
        $this->find('o/jsonData/public')->shouldBe([
            'o/jsonData/public' => 'jsonSerialize',
        ]);
        $this->find('o/jsonData/protected')->shouldBe([
            'o/jsonData/protected' => 'jsonSerialize',
        ]);


        $this->find('o/iteratorData/foo')->shouldBe([
            'o/iteratorData/foo' => 'iterator',
        ]);
        $this->find('o/iteratorData/bar')->shouldBe([
            'o/iteratorData/bar' => 'iterator',
        ]);
        $this->find('o/iteratorData/baz')->shouldBe([
            'o/iteratorData/baz' => [
                'thing1' => 'iterator',
                'thing2' => 'iterator',
            ]
        ]);
        $this->find('o/iteratorData/public')->shouldBe([
            'o/iteratorData/public' => 'iterator',
        ]);
        $this->find('o/iteratorData/protected')->shouldBe([
            'o/iteratorData/protected' => 'iterator',
        ]);
    }
}
