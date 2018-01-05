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

use DateTime;
use Valit\Util\Date;
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

        $this->checkDateParsable('1987-01-01')->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkDateParsable('1987-01-01')->success()->shouldBe(true);
        $this->checkDateParsable('3 days ago')->success()->shouldBe(true);
        $this->checkDateParsable(1, 'U')->success()->shouldBe(true); // tiemstamp
        $this->checkDateParsable(1.0, null)->success()->shouldBe(true); // float timestamp
        $this->checkDateParsable(-1.0, null)->success()->shouldBe(true); // float timestamp
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
        $this->checkDateAfter('1987-01-02', '1987-01-01')->success()->shouldBe(true);
        $this->checkDateAfter('now', 'yesterday')->success()->shouldBe(true);
        $this->checkDateAfter('1987-01-01 00:00:01', new DateTime('1987-01-01 00:00:00'))->success()->shouldBe(true);
        $this->checkDateAfter('1987-01-01 00:00:01', '1987-01-01 00:00:00')->success()->shouldBe(true);
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
        $this->checkDateBefore('1987-01-01', '1987-01-02')->success()->shouldBe(true);
        $this->checkDateBefore('now', 'tomorrow')->success()->shouldBe(true);
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
        Date::mockCurrentTime($now);

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
        Date::mockCurrentTime($now);

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

    function it_checks_atNoon()
    {
        $this->provides()->shouldHaveKey('isDateTimeAtNoon');
        $this->provides()->shouldHaveKey('dateTimeAtNoon');

        $this->checkAtNoon(null)->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkAtNoon('12:00:00')->success()->shouldBe(true);
        $this->checkAtNoon('1987-01-01 12:00:00')->success()->shouldBe(true);

        $this->checkAtNoon('1987-01-01')->success()->shouldBe(false);
        $this->checkAtNoon(new DateTime('1987-01-01'))->success()->shouldBe(false);
        $this->checkAtNoon(new DateTimeImmutable('1987-01-01'))->success()->shouldBe(false);
        $this->checkAtNoon('foo')->success()->shouldBe(false);
        $this->checkAtNoon('1987-01-01 00:00:01')->success()->shouldBe(false);
        $this->checkAtNoon('1987-01-01 00:00:00.000001')->success()->shouldBe(false);
    }

    function it_checks_sameDateAs()
    {
        $this->provides()->shouldHaveKey('sameDayAs');
        $this->provides()->shouldHaveKey('sameDateAs');
        $this->provides()->shouldHaveKey('isSameDayAs');
        $this->provides()->shouldHaveKey('isSameDateAs');

        $this->checkSameDateAs(null, new DateTime())->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkSameDateAs(new DateTime(), new DateTime())->success()->shouldBe(true);
        $this->checkSameDateAs(new DateTime(), new DateTimeImmutable())->success()->shouldBe(true);
        $this->checkSameDateAs('1987-01-01 12:00:00', new DateTime('1987-01-01 11:00:00'))->success()->shouldBe(true);
        $this->checkSameDateAs('now', 'now')->success()->shouldBe(true);
        $this->checkSameDateAs('now', 'noon')->success()->shouldBe(true);
        $this->checkSameDateAs('now', 'midnight')->success()->shouldBe(true);
        $this->checkSameDateAs('midnight', 'noon')->success()->shouldBe(true);

        $this->checkSameDateAs('now', '1 day ago')->success()->shouldBe(false);
        $this->checkSameDateAs(null, 'now')->success()->shouldBe(false);
        $this->checkSameDateAs(false, 'now')->success()->shouldBe(false);
        $this->checkSameDateAs(true, 'now')->success()->shouldBe(false);
        $this->checkSameDateAs(curl_init(), 'now')->success()->shouldBe(false);
    }

    function it_checks_sameDayOfWeek()
    {
        $this->provides()->shouldHaveKey('sameDayOfWeek');
        $this->provides()->shouldHaveKey('isSameDayOfWeek');
        $this->provides()->shouldHaveKey('isDayOfWeek');
        $this->provides()->shouldHaveKey('dayOfWeek');

        $this->checkSameDayOfWeek(null, new DateTime())->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkSameDayOfWeek(new DateTime(), new DateTime())->success()->shouldBe(true);
        $this->checkSameDayOfWeek(new DateTime(), new DateTimeImmutable())->success()->shouldBe(true);
        $this->checkSameDayOfWeek('1987-01-01 12:00:00', new DateTime('1987-01-01 11:00:00'))->success()->shouldBe(true);
        $this->checkSameDayOfWeek('1987-01-01 12:00:00', new DateTime('1987-01-08 11:00:00'))->success()->shouldBe(true);
        $this->checkSameDayOfWeek('1987-01-01 12:00:00', '1987-01-15 00:00:00')->success()->shouldBe(true);
        $this->checkSameDayOfWeek('wednesday', new DateTime('last wednesday'))->success()->shouldBe(true);
        $this->checkSameDayOfWeek('now', 'now')->success()->shouldBe(true);
        $this->checkSameDayOfWeek('now', 'noon')->success()->shouldBe(true);
        $this->checkSameDayOfWeek('now', 'midnight')->success()->shouldBe(true);
        $this->checkSameDayOfWeek('midnight', 'noon')->success()->shouldBe(true);
        $this->checkSameDayOfWeek('now', '7 day ago')->success()->shouldBe(true);

        $this->checkSameDayOfWeek('now', '1 day ago')->success()->shouldBe(false);
        $this->checkSameDayOfWeek(null, 'now')->success()->shouldBe(false);
        $this->checkSameDayOfWeek(false, 'now')->success()->shouldBe(false);
        $this->checkSameDayOfWeek(true, 'now')->success()->shouldBe(false);
        $this->checkSameDayOfWeek(curl_init(), 'now')->success()->shouldBe(false);
    }

    function it_checks_dayOfMonth()
    {
        $this->provides()->shouldHaveKey('dayOfMonth');
        $this->provides()->shouldHaveKey('isDayOfMonth');

        $this->checkDayOfMonth(null, 1)->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkDayOfMonth(new DateTime(), date('j'))->success()->shouldBe(true);
        $this->checkDayOfMonth('1987-01-01', 1)->success()->shouldBe(true);
        $this->checkDayOfMonth('1987-01-01', '1')->success()->shouldBe(true);
        $this->checkDayOfMonth('1987-02-01', 1)->success()->shouldBe(true);
        $this->checkDayOfMonth('1988-03-01', 1)->success()->shouldBe(true);
        $this->checkDayOfMonth('1988-03-31', 31)->success()->shouldBe(true);

        $this->checkDayOfMonth('1987-02-31', 31)->success()->shouldBe(false); // not 31 days if feb.
        $this->checkDayOfMonth('1987-01-01', 31)->success()->shouldBe(false); // not 31 days if feb.
        $this->checkDayOfMonth(null, 1)->success()->shouldBe(false); // not 31 days if feb.
        $this->checkDayOfMonth(new \stdClass(), 1)->success()->shouldBe(false); // not 31 days if feb.
        $this->checkDayOfMonth([], 1)->success()->shouldBe(false); // not 31 days if feb.
        $this->checkDayOfMonth(curl_init(), 1)->success()->shouldBe(false); // not 31 days if feb.

        $this->shouldThrow('InvalidArgumentException')->during('checkDayOfMonth', ['now', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDayOfMonth', ['now', 0]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDayOfMonth', ['now', 32]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDayOfMonth', ['now', -5]);
        $this->shouldThrow('InvalidArgumentException')->during('checkDayOfMonth', ['now', 'non-int']);
    }

    function it_checks_birthday()
    {
        $this->provides()->shouldHaveKey('isBirthdayEquivalentOf');
        $this->provides()->shouldHaveKey('birthdatEquivalentOf');
        $this->provides()->shouldHaveKey('sameDayAndMonth');
        $this->provides()->shouldHaveKey('isSameDayAndMonth');

        $this->checkBirthday(new DateTime(), new DateTime())->shouldHaveType('Valit\Result\AssertionResult');

        $this->checkBirthday(new DateTime(), new DateTime())->success()->shouldBe(true);
        $this->checkBirthday('now', new DateTime())->success()->shouldBe(true);
        $this->checkBirthday('now', 'now')->success()->shouldBe(true);
        $this->checkBirthday('now', '1 year ago')->success()->shouldBe(true);
        $this->checkBirthday('now', '10 years ago')->success()->shouldBe(true);
        $this->checkBirthday('1987-01-01', '2018-01-01')->success()->shouldBe(true);

        $this->checkBirthday('1987-01-01', '1987-01-02')->success()->shouldBe(false);
        $this->checkBirthday('1987-01-01', '1987-02-01')->success()->shouldBe(false);
        $this->checkBirthday('foobar', '2018-01-02')->success()->shouldBe(false);
        $this->checkBirthday(curl_init(), '2018-01-02')->success()->shouldBe(false);

        $this->checkBirthday(new DateTime(), new DateTime('yesterday'))->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkBirthday', ['now', 'non-date']);
        $this->shouldThrow('InvalidArgumentException')->during('checkBirthday', ['now', null]);
    }
}
