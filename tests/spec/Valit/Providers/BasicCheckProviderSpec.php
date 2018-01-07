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

use ArrayObject;
use PhpSpec\ObjectBehavior;

class BasicCheckProviderSpec extends ObjectBehavior
{
    function it_checks_isTruthy()
    {
        $this->checkIsTruthy(null)->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('truthy');
        $this->provides()->shouldHaveKey('isTruthy');

        $this->checkIsTruthy(true)->success()->shouldBe(true);
        $this->checkIsTruthy(1)->success()->shouldBe(true);
        $this->checkIsTruthy(2)->success()->shouldBe(true);
        $this->checkIsTruthy('1')->success()->shouldBe(true);
        $this->checkIsTruthy([0])->success()->shouldBe(true);
        $this->checkIsTruthy(0.1)->success()->shouldBe(true);
        $this->checkIsTruthy('00')->success()->shouldBe(true);
        $this->checkIsTruthy(curl_init())->success()->shouldBe(true);
        $this->checkIsTruthy((object) [])->success()->shouldBe(true);

        $this->checkIsTruthy(false)->success()->shouldBe(false);
        $this->checkIsTruthy(null)->success()->shouldBe(false);
        $this->checkIsTruthy([])->success()->shouldBe(false);
        $this->checkIsTruthy('')->success()->shouldBe(false);
        $this->checkIsTruthy('0')->success()->shouldBe(false);
        $this->checkIsTruthy(0)->success()->shouldBe(false);
        $this->checkIsTruthy(0.0)->success()->shouldBe(false);
    }

    function it_checks_isFalsy()
    {
        $this->checkIsFalsy(null)->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('falsy');
        $this->provides()->shouldHaveKey('isFalsy');

        $this->checkIsFalsy(false)->success()->shouldBe(true);
        $this->checkIsFalsy(null)->success()->shouldBe(true);
        $this->checkIsFalsy([])->success()->shouldBe(true);
        $this->checkIsFalsy('')->success()->shouldBe(true);
        $this->checkIsFalsy('0')->success()->shouldBe(true);
        $this->checkIsFalsy(0)->success()->shouldBe(true);
        $this->checkIsFalsy(0.0)->success()->shouldBe(true);

        $this->checkIsFalsy(true)->success()->shouldBe(false);
        $this->checkIsFalsy(1)->success()->shouldBe(false);
        $this->checkIsFalsy(2)->success()->shouldBe(false);
        $this->checkIsFalsy('1')->success()->shouldBe(false);
        $this->checkIsFalsy([0])->success()->shouldBe(false);
        $this->checkIsFalsy(0.1)->success()->shouldBe(false);
        $this->checkIsFalsy('00')->success()->shouldBe(false);
        $this->checkIsFalsy(curl_init())->success()->shouldBe(false);
        $this->checkIsFalsy((object) [])->success()->shouldBe(false);
    }

    function it_checks_isTrue()
    {
        $this->checkIsTruthy(null)->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('true');
        $this->provides()->shouldHaveKey('isTrue');

        $this->checkIsTrue(true)->success()->shouldBe(true);

        $this->checkIsTrue(0)->success()->shouldBe(false);
        $this->checkIsTrue(1)->success()->shouldBe(false);
        $this->checkIsTrue(2)->success()->shouldBe(false);
        $this->checkIsTrue('')->success()->shouldBe(false);
        $this->checkIsTrue([])->success()->shouldBe(false);
        $this->checkIsTrue('0')->success()->shouldBe(false);
        $this->checkIsTrue('1')->success()->shouldBe(false);
        $this->checkIsTrue(0.0)->success()->shouldBe(false);
        $this->checkIsTrue(0.1)->success()->shouldBe(false);
        $this->checkIsTrue([0])->success()->shouldBe(false);
        $this->checkIsTrue('00')->success()->shouldBe(false);
        $this->checkIsTrue(null)->success()->shouldBe(false);
        $this->checkIsTrue(false)->success()->shouldBe(false);
        $this->checkIsTrue((object) [])->success()->shouldBe(false);
        $this->checkIsTrue(curl_init())->success()->shouldBe(false);
    }

    function it_checks_isFalse()
    {
        $this->checkIsTruthy(null)->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('false');
        $this->provides()->shouldHaveKey('isFalse');

        $this->checkIsFalse(false)->success()->shouldBe(true);

        $this->checkIsFalse(0)->success()->shouldBe(false);
        $this->checkIsFalse(1)->success()->shouldBe(false);
        $this->checkIsFalse(2)->success()->shouldBe(false);
        $this->checkIsFalse('')->success()->shouldBe(false);
        $this->checkIsFalse([])->success()->shouldBe(false);
        $this->checkIsFalse('0')->success()->shouldBe(false);
        $this->checkIsFalse('1')->success()->shouldBe(false);
        $this->checkIsFalse(0.0)->success()->shouldBe(false);
        $this->checkIsFalse(0.1)->success()->shouldBe(false);
        $this->checkIsFalse([0])->success()->shouldBe(false);
        $this->checkIsFalse('00')->success()->shouldBe(false);
        $this->checkIsFalse(null)->success()->shouldBe(false);
        $this->checkIsFalse(true)->success()->shouldBe(false);
        $this->checkIsFalse((object) [])->success()->shouldBe(false);
        $this->checkIsFalse(curl_init())->success()->shouldBe(false);
    }

    function it_checks_isArray()
    {
        $this->checkArray([])->shouldHaveType('Valit\Result\AssertionResult');

        $this->provides()->shouldHaveKey('isArray');
        $this->provides()->shouldHaveKey('array');

        $this->checkArray([])->success()->shouldBe(true);
        $this->checkArray(['a', 'b'])->success()->shouldBe(true);
        $this->checkArray(['a' => 'b'])->success()->shouldBe(true);

        $this->checkArray(1)->success()->shouldBe(false);
        $this->checkArray(1.0)->success()->shouldBe(false);
        $this->checkArray(null)->success()->shouldBe(false);
        $this->checkArray('array')->success()->shouldBe(false);
        $this->checkArray(curl_init())->success()->shouldBe(false);
        $this->checkArray((object) [])->success()->shouldBe(false);
        $this->checkArray('ArrayObject')->success()->shouldBe(false);
        $this->checkArray(new ArrayObject([]))->success()->shouldBe(false);
    }
}
