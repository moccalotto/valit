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

        $this->checksDateParsable('', false)->shouldHaveType('Moccalotto\Valit\Result');

        $this->checksDateParsable('1987-01-01', 'Y-m-d')->success()->shouldBe(true);
        $this->checksDateParsable('-2200-01-01', 'Y-m-d')->success()->shouldBe(true);
        $this->checksDateParsable('1987-01-01 23:55:55', 'Y-m-d H:i:s')->success()->shouldBe(true);

        $this->checksDateParsable('0000-00-00', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('1987-13-01', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('1987-01-32', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('21244-01-31', 'Y-m-d')->success()->shouldBe(false);
        $this->checksDateParsable('1987-01-01 25:55:55', 'Y-m-d H:i:s')->success()->shouldBe(false);
    }
}
