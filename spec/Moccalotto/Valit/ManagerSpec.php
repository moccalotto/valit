<?php

namespace spec\Moccalotto\Valit;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Manager');
    }

    function it_loads_core_providers()
    {
        $this->beConstructedThrough('instance');

        $this->hasCheck('isNumeric')->shouldBe(true);
        $this->hasCheck('decimalString')->shouldBe(true);
    }

    function it_can_execute_checks()
    {
        $this->beConstructedThrough('instance');

        $this->executeCheck('isNumeric', 42, [])
            ->shouldHaveType('Moccalotto\Valit\Result');

        $this->executeCheck('isNumeric', 42, [])->success()->shouldBe(true);
    }
}
