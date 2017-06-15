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

namespace spec\Moccalotto\Valit;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CustomCallbackCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('message', 'is_string');
        $this->shouldHaveType('Moccalotto\Valit\CustomChecker');
        $this->shouldHaveType('Moccalotto\Valit\CustomCallbackChecker');
    }

    function it_executes_callbacks()
    {
        $this->beConstructedWith('message', 'is_string');

        $this->check('testString')->shouldHaveType('Moccalotto\Valit\Result');
        $this->check(false)->success()->shouldBe(false);
        $this->check('testString')->success()->shouldBe(true);
    }
}
