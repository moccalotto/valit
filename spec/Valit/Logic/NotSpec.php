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

namespace spec\Valit\Logic;

use Valit\check;
use Valit\Manager;
use PhpSpec\ObjectBehavior;
use Valit\Result\AssertionResult;
use Valit\Result\AssertionResultBag;
use Valit\Exceptions\ValueRequiredException;

class NotSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), 'isInt');
        $this->shouldHaveType('Valit\Logic\Not');
    }

    function it_implements_Logic_interface()
    {
        $this->beConstructedWith(Manager::instance(), 'isInt');
        $this->shouldHaveType('Valit\Contracts\Logic');
    }

    function it_returns_AssertionResult()
    {
        $this->beConstructedWith(Manager::instance(), 'isInt');
        $this->execute(true, 'foobar')->shouldHaveType(AssertionResult::class);
    }

    function it_will_accept_a_failed_validation()
    {
        $this->beConstructedWith(Manager::instance(), 'isInt');
        $this->execute(true, 'not an integer')->success()->shouldBe(true);
    }

    function it_will_reject_a_successful_validation()
    {
        $this->beConstructedWith(Manager::instance(), 'isInt');
        $this->execute(true, 42)->success()->shouldBe(false);
    }
}
