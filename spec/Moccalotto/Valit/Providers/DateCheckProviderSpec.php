<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Moccalotto\Valit\Providers;

use DateTime;
use PhpSpec\ObjectBehavior;

class DateCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\DateCheckProvider');
        $this->shouldHaveType('Moccalotto\Valit\Contracts\CheckProvider');
    }

    public function it_checks_parsableDate()
    {
        $this->provides()->shouldHaveKey('dateString');
        $this->provides()->shouldHaveKey('isDateString');
        $this->provides()->shouldHaveKey('parsableDate');
        $this->provides()->shouldHaveKey('isParsableDate');

        $this->checksDateParsable('1987-01-01', 'Y-m-d')->shouldHaveType('Moccalotto\Valit\Result');

        $this->checksDateParsable('1987-01-01', 'Y-m-d')->success()->shouldBe(true);

        $this->checksDateParsable('-2200-01-01', 'Y-m-d')->success()->shouldBe(true);
        $this->checksDateParsable('536457600.089000', 'U.u')->success()->shouldBe(true);
        $this->checksDateParsable('1987-01-01 23:55:55', 'Y-m-d H:i:s')->success()->shouldBe(true);
        $this->checksDateParsable('1987-01-01 23:55:55.000001', 'Y-m-d H:i:s.u')->success()->shouldBe(true);

        $this->checksDateParsable('0000-00-00', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('1987-13-01', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('1987-01-32', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('21244-01-31', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('1987-01-01 25:55:55', 'Y-m-d H:i:s')->success()->shouldBe(false);
    }

    public function it_checks_dateAfter()
    {
        $this->provides()->shouldHaveKey('dateAfter');
        $this->provides()->shouldHaveKey('laterThan');
        $this->provides()->shouldHaveKey('isDateAfter');
        $this->provides()->shouldHaveKey('isLaterThan');
        $this->provides()->shouldHaveKey('occursAfter');

        $this->shouldThrow('InvalidArgumentException')->during('checkDateAfter', ['fooDate', 'barDate']);

        $this->checkDateAfter('1987-01-01', new DateTime('1987-01-01'))->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkDateAfter('1987-01-02', new DateTime('1987-01-01'))->success()->shouldBe(true);
        $this->checkDateAfter('1987-01-01 00:00:01', new DateTime('1987-01-01 00:00:00'))->success()->shouldBe(true);
        $this->checkDateAfter('1987-01-01 00:00:00.000001', new DateTime('1987-01-01 00:00:00.000000'))->success()->shouldBe(true);

        $this->checkDateAfter('1987-01-01', new DateTime('1987-01-01'))->success()->shouldBe(false);
        $this->checkDateAfter('1987-01-01', new DateTime('1987-01-02'))->success()->shouldBe(false);
    }
}
