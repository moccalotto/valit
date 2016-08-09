<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace spec\Moccalotto\Valit;

use PhpSpec\ObjectBehavior;

class ManagerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Manager');
    }

    public function it_loads_core_providers()
    {
        $this->beConstructedThrough('instance');

        $this->hasCheck('isNumeric')->shouldBe(true);
        $this->hasCheck('decimalString')->shouldBe(true);
    }

    public function it_can_execute_checks()
    {
        $this->beConstructedThrough('instance');

        $this->executeCheck('isNumeric', 42, [])
            ->shouldHaveType('Moccalotto\Valit\Result');

        $this->executeCheck('isNumeric', 42, [])->success()->shouldBe(true);
    }
}
