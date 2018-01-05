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

namespace spec\Valit\Providers;

use Valit\Logic;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Valit\Result\AssertionResult;
use Valit\Contracts\Logic as LogicContract;

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

    function it_checks_logic(LogicContract $logic)
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

    function it_checks_oneOf()
    {
        $scenarios = [
            'isString & containsString("a")',
            'isString & containsString("b")',
            'isString & containsString("c")',
        ];

        $this->provides()->shouldHaveKey('passesOneOf');
        $this->provides()->shouldHaveKey('logicOneOf');

        $this->checkPassesOneOf(42, [])->shouldHaveType(AssertionResult::class);

        $this->checkPassesOneOf('', $scenarios)->success()->shouldBe(false);
        $this->checkPassesOneOf('a', $scenarios)->success()->shouldBe(true);
        $this->checkPassesOneOf('b', $scenarios)->success()->shouldBe(true);
        $this->checkPassesOneOf('c', $scenarios)->success()->shouldBe(true);
        $this->checkPassesOneOf('ba', $scenarios)->success()->shouldBe(false);
        $this->checkPassesOneOf('ca', $scenarios)->success()->shouldBe(false);
        $this->checkPassesOneOf('bc', $scenarios)->success()->shouldBe(false);
        $this->checkPassesOneOf('abc', $scenarios)->success()->shouldBe(false);
    }

    function it_checks_allOf()
    {
        $scenarios = [
            'isString & containsString("a")',
            'isString & containsString("b")',
            'isString & containsString("c")',
        ];

        $this->provides()->shouldHaveKey('passesAllOf');
        $this->provides()->shouldHaveKey('logicAllOf');

        $this->checkPassesAllOf(42, [])->shouldHaveType(AssertionResult::class);

        $this->checkPassesAllOf('', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('a', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('b', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('c', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('ba', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('ca', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('bc', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAllOf('abc', $scenarios)->success()->shouldBe(true);
    }

    function it_checks_anyOf()
    {
        $scenarios = [
            'isString & containsString("a")',
            'isString & containsString("b")',
            'isString & containsString("c")',
        ];

        $this->provides()->shouldHaveKey('passesAnyOf');
        $this->provides()->shouldHaveKey('logicAnyOf');

        $this->checkPassesAnyOf(42, [])->shouldHaveType(AssertionResult::class);

        $this->checkPassesAnyOf('', $scenarios)->success()->shouldBe(false);
        $this->checkPassesAnyOf('a', $scenarios)->success()->shouldBe(true);
        $this->checkPassesAnyOf('b', $scenarios)->success()->shouldBe(true);
        $this->checkPassesAnyOf('c', $scenarios)->success()->shouldBe(true);
        $this->checkPassesAnyOf('ba', $scenarios)->success()->shouldBe(true);
        $this->checkPassesAnyOf('ca', $scenarios)->success()->shouldBe(true);
        $this->checkPassesAnyOf('bc', $scenarios)->success()->shouldBe(true);
        $this->checkPassesAnyOf('abc', $scenarios)->success()->shouldBe(true);
    }

    function it_checks_noneOf()
    {
        $scenarios = [
            'isString & containsString("a")',
            'isString & containsString("b")',
            'isString & containsString("c")',
        ];

        $this->provides()->shouldHaveKey('passesNoneOf');
        $this->provides()->shouldHaveKey('logicNoneOf');
        $this->provides()->shouldHaveKey('failsAllOf');

        $this->checkPassesNoneOf(42, [])->shouldHaveType(AssertionResult::class);

        $this->checkPassesNoneOf('', $scenarios)->success()->shouldBe(true);
        $this->checkPassesNoneOf('a', $scenarios)->success()->shouldBe(false);
        $this->checkPassesNoneOf('b', $scenarios)->success()->shouldBe(false);
        $this->checkPassesNoneOf('c', $scenarios)->success()->shouldBe(false);
        $this->checkPassesNoneOf('ba', $scenarios)->success()->shouldBe(false);
        $this->checkPassesNoneOf('ca', $scenarios)->success()->shouldBe(false);
        $this->checkPassesNoneOf('bc', $scenarios)->success()->shouldBe(false);
        $this->checkPassesNoneOf('abc', $scenarios)->success()->shouldBe(false);
    }

    function it_checks_not()
    {
        $scenario = ['isInt', 'isDivisibleBy(2)'];

        $this->provides()->shouldHaveKey('not');
        $this->provides()->shouldHaveKey('doesNotPass');
        $this->provides()->shouldHaveKey('fails');
        $this->provides()->shouldHaveKey('invert');

        $this->checkDoesNotPass(42, 'isInt & isDivisibleBy(2)')->shouldHaveType(AssertionResult::class);

        $this->checkDoesNotPass('', $scenario)->success()->shouldBe(true);
        $this->checkDoesNotPass(1, $scenario)->success()->shouldBe(true);
    }
}
