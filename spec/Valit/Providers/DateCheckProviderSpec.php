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

use DateTime;
use DateTimeImmutable;
use PhpSpec\ObjectBehavior;

class DateCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\DateCheckProvider');
        $this->shouldHaveType('Valit\Contracts\CheckProvider');
    }

    function it_checks_parsableDate()
    {
        $this->provides()->shouldHaveKey('dateString');
        $this->provides()->shouldHaveKey('isDateString');
        $this->provides()->shouldHaveKey('parsableDate');
        $this->provides()->shouldHaveKey('isParsableDate');

        $this->checkDateParsable('1987-01-01', 'Y-m-d')->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkDateParsable(1, 'U')->success()->shouldBe(true); // tiemstamp
        $this->checkDateParsable('1987-01-01', 'Y-m-d')->success()->shouldBe(true);
        $this->checkDateParsable('-2200-01-01', 'Y-m-d')->success()->shouldBe(true);
        $this->checkDateParsable('536457600.089000', 'U.u')->success()->shouldBe(true);
        $this->checkDateParsable('1987-01-01 23:55:55', 'Y-m-d H:i:s')->success()->shouldBe(true);
        $this->checkDateParsable('1987-01-01 23:55:55.000001', 'Y-m-d H:i:s.u')->success()->shouldBe(true);

        $this->checkDateParsable('0000-00-00', 'Y-m-d')->success()->shouldBe(false);
        $this->checkDateParsable('1987-13-01', 'Y-m-d')->success()->shouldBe(false);
        $this->checkDateParsable('1987-01-32', 'Y-m-d')->success()->shouldBe(false);
        $this->checkDateParsable('21244-01-31', 'Y-m-d')->success()->shouldBe(false);
        $this->checkDateParsable('1987-01-01 25:55:55', 'Y-m-d H:i:s')->success()->shouldBe(false);

        $this->checkDateParsable(1.0, null)->success()->shouldBe(false);
        $this->checkDateParsable(null, null)->success()->shouldBe(false);
        $this->checkDateParsable('foo', null)->success()->shouldBe(false);
        $this->checkDateParsable(curl_init(), null)->success()->shouldBe(false);
        $this->checkDateParsable((object) [], null)->success()->shouldBe(false);
    }

    function it_checks_dateAfter()
    {
        $this->provides()->shouldHaveKey('dateAfter');
        $this->provides()->shouldHaveKey('laterThan');
        $this->provides()->shouldHaveKey('isDateAfter');
        $this->provides()->shouldHaveKey('isLaterThan');
        $this->provides()->shouldHaveKey('occursAfter');

        $this->shouldThrow('InvalidArgumentException')->during('checkDateAfter', ['fooDate', 'barDate']);

        $this->checkDateAfter('1987-01-01', new DateTime('1987-01-01'))->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkDateAfter('1987-01-02', new DateTime('1987-01-01'))->success()->shouldBe(true);
        $this->checkDateAfter('1987-01-01 00:00:01', new DateTime('1987-01-01 00:00:00'))->success()->shouldBe(true);
        $this->checkDateAfter('1987-01-01 00:00:00.000001', new DateTime('1987-01-01 00:00:00.000000'))->success()->shouldBe(true);
        $this->checkDateAfter(new DateTime('1987-01-01 00:00:00.000001'), new DateTime('1987-01-01 00:00:00.000000'))->success()->shouldBe(true);

        $this->checkDateAfter('1987-01-01', new DateTime('1987-01-01'))->success()->shouldBe(false);
        $this->checkDateAfter('1987-01-01', new DateTime('1987-01-02'))->success()->shouldBe(false);
    }

    function it_checks_dateBefore()
    {
        $this->provides()->shouldHaveKey('dateBefore');
        $this->provides()->shouldHaveKey('earlierThan');
        $this->provides()->shouldHaveKey('isDateBefore');
        $this->provides()->shouldHaveKey('isEarlierThan');
        $this->provides()->shouldHaveKey('occursBefore');

        $this->shouldThrow('InvalidArgumentException')->during('checkDateBefore', ['fooDate', 'barDate']);

        $this->checkDateBefore('1987-01-01', new DateTime('1987-01-01'))->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkDateBefore('1987-01-01', new DateTime('1987-01-02'))->success()->shouldBe(true);
        $this->checkDateBefore('1987-01-01 00:00:00', new DateTime('1987-01-01 00:00:01'))->success()->shouldBe(true);
        $this->checkDateBefore('1987-01-01 00:00:00.000000', new DateTime('1987-01-01 00:00:00.000001'))->success()->shouldBe(true);
        $this->checkDateBefore(new DateTime('1987-01-01 00:00:00.000000'), new DateTime('1987-01-01 00:00:00.000001'))->success()->shouldBe(true);

        $this->checkDateBefore('1987-01-01', new DateTime('1987-01-01'))->success()->shouldBe(false);
        $this->checkDateBefore('1987-01-02', new DateTime('1987-01-01'))->success()->shouldBe(false);
    }

    function it_checks_inThePast()
    {
        $this->provides()->shouldHaveKey('dateInThePast');
        $this->provides()->shouldHaveKey('isDateInThePast');

        // Override the current time to make testing easier.
        $now = new DateTimeImmutable('2000-01-01 00:00:00');
        $this->overrideNow($now);

        $this->checkInThePast($now)->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkInThePast('1999-12-31 23:59:59')->success()->shouldBe(true);
        $this->checkInThePast($now->modify('-1 seconds'))->success()->shouldBe(true);
        $this->checkInThePast(new DateTime('1999-12-31 23:59:59'))->success()->shouldBe(true);
        $this->checkInThePast(new DateTimeImmutable('1999-12-31 23:59:59'))->success()->shouldBe(true);

        $this->checkInThePast($now)->success()->shouldBe(false);
        $this->checkInThePast($now->modify('+1 seconds'))->success()->shouldBe(false);
    }

    function it_checks_inTheFuture()
    {
        $this->provides()->shouldHaveKey('dateInTheFuture');
        $this->provides()->shouldHaveKey('isDateInTheFuture');

        // Override the current time to make testing easier.
        $now = new DateTimeImmutable('2000-01-01 00:00:00');
        $this->overrideNow($now);

        $this->checkInTheFuture($now)->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkInTheFuture($now->modify('+1 seconds'))->success()->shouldBe(true);
        $this->checkInTheFuture('2000-01-01 00:00:00.00001')->success()->shouldBe(true);
        $this->checkInTheFuture(new DateTime('2000-01-01 00:00:00.00001'))->success()->shouldBe(true);
        $this->checkInTheFuture(new DateTimeImmutable('2000-01-01 00:00:00.00001'))->success()->shouldBe(true);

        $this->checkInTheFuture($now)->success()->shouldBe(false);
        $this->checkInTheFuture('1999-12-31 23:59:59')->success()->shouldBe(false);
        $this->checkInTheFuture($now->modify('-1 seconds'))->success()->shouldBe(false);
    }

    function it_checks_atMidnight()
    {
        $this->provides()->shouldHaveKey('dateTimeAtMidnight');
        $this->provides()->shouldHaveKey('isDateTimeAtMidnight');
        $this->provides()->shouldHaveKey('dateOnly');
        $this->provides()->shouldHaveKey('isDateOnly');

        $this->checkAtMidnight(null)->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkAtMidnight('1987-01-01')->success()->shouldBe(true);
        $this->checkAtMidnight('1987-01-01 00:00:00')->success()->shouldBe(true);
        $this->checkAtMidnight(new DateTime('1987-01-01'))->success()->shouldBe(true);
        $this->checkAtMidnight(new DateTimeImmutable('1987-01-01'))->success()->shouldBe(true);

        $this->checkAtMidnight('foo')->success()->shouldBe(false);
        $this->checkAtMidnight('1987-01-01 00:00:01')->success()->shouldBe(false);
        $this->checkAtMidnight('1987-01-01 00:00:00.000001')->success()->shouldBe(false);
    }
}
