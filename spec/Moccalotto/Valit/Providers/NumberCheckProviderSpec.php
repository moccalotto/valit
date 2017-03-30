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

use PhpSpec\ObjectBehavior;

class NumberCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\NumberCheckProvider');
    }

    public function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    public function it_checks_numeric()
    {
        $this->checkNumeric(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('numeric');
        $this->provides()->shouldHaveKey('isNumeric');

        $this->checkNumeric(0)->success()->shouldBe(true);
        $this->checkNumeric(1)->success()->shouldBe(true);
        $this->checkNumeric(3.14)->success()->shouldBe(true);
        $this->checkNumeric('0')->success()->shouldBe(true);
        $this->checkNumeric('1')->success()->shouldBe(true);
        $this->checkNumeric('3.14')->success()->shouldBe(true);
        $this->checkNumeric('-3.14')->success()->shouldBe(true);
        $this->checkNumeric('-0')->success()->shouldBe(true);
        $this->checkNumeric(INF)->success()->shouldBe(true);
        $this->checkNumeric(NAN)->success()->shouldBe(true);

        $this->checkNumeric('')->success()->shouldBe(false);
        $this->checkNumeric('aff')->success()->shouldBe(false);
        $this->checkNumeric('22e')->success()->shouldBe(false);
        $this->checkNumeric('0x22e')->success()->shouldBe(false);
        $this->checkNumeric('1.3.4')->success()->shouldBe(false);
        $this->checkNumeric('NaN')->success()->shouldBe(false);
        $this->checkNumeric([])->success()->shouldBe(false);
        $this->checkNumeric((object) [])->success()->shouldBe(false);
        $this->checkNumeric(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_real()
    {
        $this->checkRealNumber(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isRealNumber');
        $this->provides()->shouldHaveKey('realNumber');

        $this->checkRealNumber(0)->success()->shouldBe(true);
        $this->checkRealNumber(1)->success()->shouldBe(true);
        $this->checkRealNumber(3.14)->success()->shouldBe(true);
        $this->checkRealNumber('0')->success()->shouldBe(true);
        $this->checkRealNumber('1')->success()->shouldBe(true);
        $this->checkRealNumber('3.14')->success()->shouldBe(true);
        $this->checkRealNumber('-3.14')->success()->shouldBe(true);
        $this->checkRealNumber('-0')->success()->shouldBe(true);
        $this->checkRealNumber(PHP_INT_MAX)->success()->shouldBe(true);
        $this->checkRealNumber((float) PHP_INT_MAX)->success()->shouldBe(true);

        $this->checkRealNumber(INF)->success()->shouldBe(false);
        $this->checkRealNumber(NAN)->success()->shouldBe(false);
        $this->checkRealNumber('')->success()->shouldBe(false);
        $this->checkRealNumber('aff')->success()->shouldBe(false);
        $this->checkRealNumber('22e')->success()->shouldBe(false);
        $this->checkRealNumber('0x22e')->success()->shouldBe(false);
        $this->checkRealNumber('1.3.4')->success()->shouldBe(false);
        $this->checkRealNumber('NaN')->success()->shouldBe(false);

        $this->checkRealNumber([])->success()->shouldBe(false);
        $this->checkRealNumber((object) [])->success()->shouldBe(false);
        $this->checkRealNumber(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_natural()
    {
        $this->checkNaturalNumber(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isRealNumber');
        $this->provides()->shouldHaveKey('realNumber');

        $this->checkNaturalNumber(0)->success()->shouldBe(true);
        $this->checkNaturalNumber(1)->success()->shouldBe(true);
        $this->checkNaturalNumber('0')->success()->shouldBe(true);
        $this->checkNaturalNumber('1')->success()->shouldBe(true);
        $this->checkNaturalNumber('-0')->success()->shouldBe(true);
        $this->checkNaturalNumber('-10')->success()->shouldBe(true);
        $this->checkNaturalNumber(PHP_INT_MAX)->success()->shouldBe(true);
        $this->checkNaturalNumber((float) PHP_INT_MAX)->success()->shouldBe(true);

        $this->checkNaturalNumber(3.14)->success()->shouldBe(false);
        $this->checkNaturalNumber('3.14')->success()->shouldBe(false);
        $this->checkNaturalNumber('-3.14')->success()->shouldBe(false);
        $this->checkNaturalNumber(INF)->success()->shouldBe(false);
        $this->checkNaturalNumber(NAN)->success()->shouldBe(false);
        $this->checkNaturalNumber('')->success()->shouldBe(false);
        $this->checkNaturalNumber('aff')->success()->shouldBe(false);
        $this->checkNaturalNumber('22e')->success()->shouldBe(false);
        $this->checkNaturalNumber('0x22e')->success()->shouldBe(false);
        $this->checkNaturalNumber('1.3.4')->success()->shouldBe(false);
        $this->checkNaturalNumber('NaN')->success()->shouldBe(false);

        $this->checkNaturalNumber([])->success()->shouldBe(false);
        $this->checkNaturalNumber((object) [])->success()->shouldBe(false);
        $this->checkNaturalNumber(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_gt()
    {
        $this->checkGreaterThan(0, 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('gt');
        $this->provides()->shouldHaveKey('greaterThan');
        $this->provides()->shouldHaveKey('isGreaterThan');

        $this->checkGreaterThan(1, 0)->success()->shouldBe(true);
        $this->checkGreaterThan(0, -1)->success()->shouldBe(true);
        $this->checkGreaterThan(-1, -3)->success()->shouldBe(true);
        $this->checkGreaterThan(1, 0.999999)->success()->shouldBe(true);

        $this->checkGreaterThan(0, 0)->success()->shouldBe(false);
        $this->checkGreaterThan(0, -0)->success()->shouldBe(false);
        $this->checkGreaterThan(0, 1)->success()->shouldBe(false);
    }

    public function it_checks_lt()
    {
        $this->checkLessThan(0, 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('lt');
        $this->provides()->shouldHaveKey('lessThan');
        $this->provides()->shouldHaveKey('lowerThan');
        $this->provides()->shouldHaveKey('isLessThan');
        $this->provides()->shouldHaveKey('isLowerThan');

        $this->checkLessThan(1, 0)->success()->shouldBe(false);
        $this->checkLessThan(0, -1)->success()->shouldBe(false);
        $this->checkLessThan(-1, -3)->success()->shouldBe(false);
        $this->checkLessThan(0, 0)->success()->shouldBe(false);
        $this->checkLessThan(0, -0)->success()->shouldBe(false);

        $this->checkLessThan(0, 1)->success()->shouldBe(true);
        $this->checkLessThan(-0, 1)->success()->shouldBe(true);
        $this->checkLessThan(0.999999, 1.0)->success()->shouldBe(true);
    }

    public function it_checks_gte()
    {
        $this->checkGreaterThanOrEqual(0, 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('gte');
        $this->provides()->shouldHaveKey('greaterThanOrEqual');
        $this->provides()->shouldHaveKey('isGreaterThanOrEqual');

        $this->checkGreaterThanOrEqual(0, 0)->success()->shouldBe(true);
        $this->checkGreaterThanOrEqual(1, 0)->success()->shouldBe(true);
        $this->checkGreaterThanOrEqual(0, -1)->success()->shouldBe(true);
        $this->checkGreaterThanOrEqual(-1, -3)->success()->shouldBe(true);
        $this->checkGreaterThanOrEqual(0, -0)->success()->shouldBe(true);
        $this->checkGreaterThanOrEqual(0.999999, 0.999999)->success()->shouldBe(true);

        $this->checkGreaterThanOrEqual(0, 1)->success()->shouldBe(false);
        $this->checkGreaterThanOrEqual(-0, 1)->success()->shouldBe(false);
    }

    public function it_checks_lte()
    {
        $this->checkLessThanOrEqual(0, 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('lte');
        $this->provides()->shouldHaveKey('lessThanOrEqual');
        $this->provides()->shouldHaveKey('lowerThanOrEqual');
        $this->provides()->shouldHaveKey('isLessThanOrEqual');
        $this->provides()->shouldHaveKey('isLowerThanOrEqual');

        $this->checkLessThanOrEqual(0, 0)->success()->shouldBe(true);
        $this->checkLessThanOrEqual(0, -0)->success()->shouldBe(true);
        $this->checkLessThanOrEqual(0, 1)->success()->shouldBe(true);
        $this->checkLessThanOrEqual(-0, 1)->success()->shouldBe(true);
        $this->checkLessThanOrEqual(0.999999, 0.999999)->success()->shouldBe(true);

        $this->checkLessThanOrEqual(1, 0)->success()->shouldBe(false);
        $this->checkLessThanOrEqual(0, -1)->success()->shouldBe(false);
        $this->checkLessThanOrEqual(-1, -3)->success()->shouldBe(false);
    }

    public function it_checks_floatEqual()
    {
        $this->checkFloatEqual(0, 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('closeTo');
        $this->provides()->shouldHaveKey('isCloseTo');
        $this->provides()->shouldHaveKey('floatEquals');
        $this->provides()->shouldHaveKey('isFloatEqualTo');

        $this->checkFloatEqual(0, 0, 0)->success()->shouldBe(true);
        $this->checkFloatEqual(1.0, 0.999999)->success()->shouldBe(true);
        $this->checkFloatEqual(1.0, 0.9991, 0.001)->success()->shouldBe(true);
        $this->checkFloatEqual(1.0, 0.9, 0.1)->success()->shouldBe(true);

        $this->checkFloatEqual(1.0, 0.9999)->success()->shouldBe(false);
        $this->checkFloatEqual(1.0, 0.9, 0.01)->success()->shouldBe(false);
        $this->checkFloatEqual(-1.0, -0.9, 0.01)->success()->shouldBe(false);

        $this->checkFloatEqual(INF, INF, 0)->success()->shouldBe(false);
        $this->checkFloatEqual('test', 0, 0.1)->success()->shouldBe(false);
        $this->checkFloatEqual(null, 0, 0.1)->success()->shouldBe(false);
        $this->checkFloatEqual([], 0, 0.1)->success()->shouldBe(false);
        $this->checkFloatEqual((object) [], 0, 0.1)->success()->shouldBe(false);
        $this->checkFloatEqual(curl_init(), 0, 0.1)->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkFloatEqual', [0, 'a', 0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkFloatEqual', [0, 0, 'a']);
        $this->shouldThrow('InvalidArgumentException')->during('checkFloatEqual', [0, null, 0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkFloatEqual', [0, null, null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkFloatEqual', [0, 0, NAN]);
        $this->shouldThrow('InvalidArgumentException')->during('checkFloatEqual', [0, 0, INF]);
    }

    public function it_checks_odd()
    {
        $this->checkOdd(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isOdd');
        $this->provides()->shouldHaveKey('odd');

        $this->checkOdd(-2)->success()->shouldBe(false);
        $this->checkOdd(-1)->success()->shouldBe(true);
        $this->checkOdd(0)->success()->shouldBe(false);
        $this->checkOdd(1)->success()->shouldBe(true);
        $this->checkOdd(2)->success()->shouldBe(false);

        $this->checkOdd(NAN)->success()->shouldBe(false);
        $this->checkOdd('test')->success()->shouldBe(false);
        $this->checkOdd(null)->success()->shouldBe(false);
        $this->checkOdd([])->success()->shouldBe(false);
        $this->checkOdd((object) [])->success()->shouldBe(false);
        $this->checkOdd(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_even()
    {
        $this->checkEven(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isEven');
        $this->provides()->shouldHaveKey('even');

        $this->checkEven(-2)->success()->shouldBe(true);
        $this->checkEven(-1)->success()->shouldBe(false);
        $this->checkEven(0)->success()->shouldBe(true);
        $this->checkEven(1)->success()->shouldBe(false);
        $this->checkEven(2)->success()->shouldBe(true);

        $this->checkEven(NAN)->success()->shouldBe(false);
        $this->checkEven('test')->success()->shouldBe(false);
        $this->checkEven(null)->success()->shouldBe(false);
        $this->checkEven([])->success()->shouldBe(false);
        $this->checkEven((object) [])->success()->shouldBe(false);
        $this->checkEven(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_positive()
    {
        $this->checkPositive(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isPositive');
        $this->provides()->shouldHaveKey('positive');

        $this->checkPositive(INF)->success()->shouldBe(true);
        $this->checkPositive(-INF)->success()->shouldBe(false);
        $this->checkPositive(-2)->success()->shouldBe(false);
        $this->checkPositive(-1)->success()->shouldBe(false);
        $this->checkPositive(0)->success()->shouldBe(false);
        $this->checkPositive(1)->success()->shouldBe(true);
        $this->checkPositive(2)->success()->shouldBe(true);

        $this->checkPositive(NAN)->success()->shouldBe(false);
        $this->checkPositive('test')->success()->shouldBe(false);
        $this->checkPositive(null)->success()->shouldBe(false);
        $this->checkPositive([])->success()->shouldBe(false);
        $this->checkPositive((object) [])->success()->shouldBe(false);
        $this->checkPositive(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_negative()
    {
        $this->checkNegative(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isNegative');
        $this->provides()->shouldHaveKey('negative');

        $this->checkNegative(INF)->success()->shouldBe(false);
        $this->checkNegative(-INF)->success()->shouldBe(true);
        $this->checkNegative(-2)->success()->shouldBe(true);
        $this->checkNegative(-1)->success()->shouldBe(true);
        $this->checkNegative(0)->success()->shouldBe(false);
        $this->checkNegative(1)->success()->shouldBe(false);
        $this->checkNegative(2)->success()->shouldBe(false);

        $this->checkNegative(NAN)->success()->shouldBe(false);
        $this->checkNegative('test')->success()->shouldBe(false);
        $this->checkNegative(null)->success()->shouldBe(false);
        $this->checkNegative([])->success()->shouldBe(false);
        $this->checkNegative((object) [])->success()->shouldBe(false);
        $this->checkNegative(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_primeRelativeTo()
    {
        $this->checkPrimeRelativeTo(0, 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('coprimeTo');
        $this->provides()->shouldHaveKey('isCoprimeTo');
        $this->provides()->shouldHaveKey('relativePrime');
        $this->provides()->shouldHaveKey('isRelativePrime');
        $this->provides()->shouldHaveKey('primeRelativeTo');
        $this->provides()->shouldHaveKey('isPrimeRelativeTo');

        $this->checkPrimeRelativeTo(1, 1)->success()->shouldBe(true);
        $this->checkPrimeRelativeTo('1', '1')->success()->shouldBe(true);
        $this->checkPrimeRelativeTo('1', 0)->success()->shouldBe(true);
        $this->checkPrimeRelativeTo(13, 2)->success()->shouldBe(true);

        $this->checkPrimeRelativeTo(0, 0)->success()->shouldBe(false);
        $this->checkPrimeRelativeTo(2, 4)->success()->shouldBe(false);
        $this->checkPrimeRelativeTo(100, 0)->success()->shouldBe(false);
        $this->checkPrimeRelativeTo(13, 26)->success()->shouldBe(false);
        $this->checkPrimeRelativeTo(13.5, 27)->success()->shouldBe(false); // value is not natural number

        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, INF]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, NAN]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, 45.5]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkPrimeRelativeTo', [1, curl_init()]);
    }

    public function it_checks_divisibleBy()
    {
        $this->checkDivisibleBy(1, 1)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isDivisibleBy');
        $this->provides()->shouldHaveKey('divisibleBy');
        $this->provides()->shouldHaveKey('dividesBy');

        $this->checkDivisibleBy(10, 1)->success()->shouldBe(true);
        $this->checkDivisibleBy(10, 2)->success()->shouldBe(true);
        $this->checkDivisibleBy(10, 5)->success()->shouldBe(true);
        $this->checkDivisibleBy(1, 0.5)->success()->shouldBe(true);
        $this->checkDivisibleBy(-40, 20)->success()->shouldBe(true);
        $this->checkDivisibleBy(15, 7.5)->success()->shouldBe(true);
        $this->checkDivisibleBy(PHP_INT_MAX, PHP_INT_MAX)->success()->shouldBe(true);
        $this->checkDivisibleBy(PHP_INT_MAX, PHP_INT_MAX / 2)->success()->shouldBe(true);

        $this->checkDivisibleBy(5, 2)->success()->shouldBe(false);
        $this->checkDivisibleBy(5, 3)->success()->shouldBe(false);
        $this->checkDivisibleBy(5, 4)->success()->shouldBe(false);
        $this->checkDivisibleBy(10, 3)->success()->shouldBe(false);
        $this->checkDivisibleBy(10, 4)->success()->shouldBe(false);
        $this->checkDivisibleBy(10, 6)->success()->shouldBe(false);
        $this->checkDivisibleBy(INF, 1)->success()->shouldBe(false);
        $this->checkDivisibleBy(NAN, 1)->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkDivisibleBy', [1, 0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDivisibleBy', [1, NAN]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDivisibleBy', [1, INF]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDivisibleBy', [1, []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDivisibleBy', [1, (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDivisibleBy', [1, curl_init()]);
    }
}
