<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Valit\Result;

use PhpSpec\ObjectBehavior;

class AssertionResultSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->shouldHaveType('Valit\Result\AssertionResult');
    }

    function it_has_a_success_status()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->success()->shouldBe(true);
    }

    function it_has_a_message()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->message()->shouldBe('Foo');
    }

    function it_has_a_context()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->context()->shouldBe(['foo' => 'bar']);
    }
}
