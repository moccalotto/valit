<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace spec\Moccalotto\Valit\Providers;

use ArrayObject;
use PhpSpec\ObjectBehavior;

class BasicCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\BasicCheckProvider');
    }

    public function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    public function it_checks_identical_to()
    {
        $this->checkIdenticalTo(null, null)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isIdenticalTo');
        $this->provides()->shouldHaveKey('identicalTo');
        $this->provides()->shouldHaveKey('isSameAs');
        $this->provides()->shouldHaveKey('sameAs');

        $obj_1a = (object) ['foo' => 'bar'];
        $obj_1b = clone $obj_1a;

        $this->checkIdenticalTo(1, 1)->success()->shouldBe(true);
        $this->checkIdenticalTo(1.23, 1.23)->success()->shouldBe(true);
        $this->checkIdenticalTo('', '')->success()->shouldBe(true);
        $this->checkIdenticalTo(true, true)->success()->shouldBe(true);
        $this->checkIdenticalTo(false, false)->success()->shouldBe(true);
        $this->checkIdenticalTo([], [])->success()->shouldBe(true);
        $this->checkIdenticalTo($obj_1a, $obj_1a)->success()->shouldBe(true);
        $this->checkIdenticalTo(['a' => 'b'], ['a' => 'b'])->success()->shouldBe(true);

        $this->checkIdenticalTo(1, '1')->success()->shouldBe(false);
        $this->checkIdenticalTo(1, true)->success()->shouldBe(false);
        $this->checkIdenticalTo(0, null)->success()->shouldBe(false);
        $this->checkIdenticalTo(0, false)->success()->shouldBe(false);
        $this->checkIdenticalTo([], false)->success()->shouldBe(false);
        $this->checkIdenticalTo($obj_1a, $obj_1b)->success()->shouldBe(false);
        $this->checkIdenticalTo(['a', 'b'], ['b', 'a'])->success()->shouldBe(false);
        $this->checkIdenticalTo(curl_init(), curl_init())->success()->shouldBe(false);
    }

    public function it_checks_equals()
    {
        $this->checkEquals(null, null)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('is');
        $this->provides()->shouldHaveKey('equals');

        $obj_1a = (object) ['foo' => 'bar'];
        $obj_1b = clone $obj_1a;

        $this->checkEquals(1, 1)->success()->shouldBe(true);
        $this->checkEquals('', '')->success()->shouldBe(true);
        $this->checkEquals(1, '1')->success()->shouldBe(true);
        $this->checkEquals([], [])->success()->shouldBe(true);
        $this->checkEquals(0, null)->success()->shouldBe(true);
        $this->checkEquals(1, true)->success()->shouldBe(true);
        $this->checkEquals(0, false)->success()->shouldBe(true);
        $this->checkEquals([], false)->success()->shouldBe(true);
        $this->checkEquals(1.23, 1.23)->success()->shouldBe(true);
        $this->checkEquals(true, true)->success()->shouldBe(true);
        $this->checkEquals(false, false)->success()->shouldBe(true);
        $this->checkEquals($obj_1a, $obj_1a)->success()->shouldBe(true);
        $this->checkEquals($obj_1a, $obj_1b)->success()->shouldBe(true);
        $this->checkEquals(['a' => 'b'], ['a' => 'b'])->success()->shouldBe(true);

        $this->checkEquals(['a', 'b'], ['b', 'a'])->success()->shouldBe(false);
        $this->checkEquals(curl_init(), curl_init())->success()->shouldBe(false);
    }

    public function it_checks_isTruthy()
    {
        $this->checkIsTruthy(null)->shouldHaveType('Moccalotto\Valit\Result');

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

    public function it_checks_isFalsy()
    {
        $this->checkIsFalsy(null)->shouldHaveType('Moccalotto\Valit\Result');

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

    public function it_checks_isArray()
    {
        $this->checkArray([])->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isArray');

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
