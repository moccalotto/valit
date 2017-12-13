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

namespace spec\Valit\Providers;

use Prophecy\Argument;
use Valit\Contracts\Logic;
use PhpSpec\ObjectBehavior;
use Valit\Result\AssertionResult;

class LogicCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\LogicCheckProvider');
    }

    function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    function it_checks_logic(Logic $logic)
    {
        $this->provides()->shouldHaveKey('isSuccessfulLogic');
        $this->provides()->shouldHaveKey('passesLogic');
        $this->provides()->shouldHaveKey('logic');

        $logic->execute(true, 5)
            ->shouldBeCalled()
            ->willReturn(new AssertionResult(true, 'this is just a test', []));

        $this->checkLogic(5, $logic, true)->success()->shouldBe(true);

        $logic->execute(true, 5)
            ->shouldBeCalled()
            ->willReturn(new AssertionResult(false, 'this is another test', []));

        $this->checkLogic(5, $logic, true)->success()->shouldBe(false);

        $logic->execute(false, null)
            ->shouldBeCalled()
            ->willReturn(new AssertionResult(true, 'this is a third test', []));

        $this->checkLogic(5, $logic, false)->success()->shouldBe(true);
    }
}
