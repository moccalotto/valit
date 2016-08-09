<?php

namespace spec\Moccalotto\Valit\Facades;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CheckSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Facades\Check');
    }

    function it_creates_fluent()
    {
        $this->that(42)->shouldHaveType('Moccalotto\Valit\Fluent');
    }
}
