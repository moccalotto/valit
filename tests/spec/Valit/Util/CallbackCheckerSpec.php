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

namespace spec\Valit\Util;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('message', 'is_string');
        $this->shouldHaveType('Valit\Contracts\CustomChecker');
        $this->shouldHaveType('Valit\Util\CallbackChecker');
    }

    function it_executes_callbacks()
    {
        $this->beConstructedWith('message', 'is_string');

        $this->check('testString')->shouldHaveType('Valit\Result\AssertionResult');
        $this->check(false)->success()->shouldBe(false);
        $this->check('testString')->success()->shouldBe(true);
    }
}
