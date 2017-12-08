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
use PhpSpec\ObjectBehavior;
use Valit\CustomCallbackChecker;
use Valit\Contracts\CustomChecker;
use Valit\Result\SingleAssertionResult;

class CustomCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\CustomCheckProvider');
        $this->shouldHaveType('Valit\Contracts\CheckProvider');
    }

    function it_checks_passesCallback()
    {
        $this->provides()->shouldHaveKey('passesCallback');

        $this->checkPassesCallback('value', 'message', 'is_bool')->shouldHaveType('Valit\Result\SingleAssertionResult');
        $this->checkPassesCallback(true, '{value} is a bool', 'is_bool')->message()->shouldBe('{value} is a bool');

        $this->checkPassesCallback(true, '{value} is a bool', 'is_bool')->success()->shouldBe(true);
        $this->checkPassesCallback(false, '{value} is a bool', 'is_bool')->success()->shouldBe(true);

        $this->checkPassesCallback(null, '{value} is a bool', function ($value) {
            return is_null($value);
        })->success()->shouldBe(true);

        $this->checkPassesCallback('value', 'message', 'is_bool')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkPassesCallback', [
            'some variable',
            curl_init(), // not a valid string
            'is_string'
        ]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPassesCallback', [
            'some variable',
            'this is a valid message',
            23  // not a valid callable
        ]);
    }

    function it_checks_passesChecker(CustomChecker $mockChecker, SingleAssertionResult $mockResult)
    {
        $this->provides()->shouldHaveKey('passesCustom');
        $this->provides()->shouldHaveKey('passesChecker');

        $mockChecker->check(null)->willReturn($mockResult);
        $this->checkPassesChecker(null, $mockChecker)->shouldBe($mockResult);

        $strChecker = new CustomCallbackChecker('message', 'is_string');
        $intChecker = new CustomCallbackChecker('message', 'is_int');

        $this->checkPassesChecker('foo', $strChecker)->shouldHaveType('Valit\Result\SingleAssertionResult');
        $this->checkPassesChecker('foo', $strChecker)->success()->shouldBe(true);
        $this->checkPassesChecker(true, $strChecker)->success()->shouldBe(false);

        $this->checkPassesChecker(999, $intChecker)->success()->shouldBe(true);
        $this->checkPassesChecker('foo', $intChecker)->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkPassesChecker', [
            'foo',
            'not instance of CustomChecker'
        ]);

        $this->shouldThrow('InvalidArgumentException')->during('checkPassesChecker', [
            'foo',
            new \StdClass()
        ]);
    }
}
