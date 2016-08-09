<?php

namespace spec\Moccalotto\Valit;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Moccalotto\Valit\Manager;

class FluentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Moccalotto\Valit\Fluent');
    }

    function it_can_execute_fluent_checks()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->isNumeric()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->success()->shouldBe(true);
        $this->isNegative()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->success()->shouldBe(false);
    }

    function it_can_return_results()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->isNumeric()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->isNegative()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->results()->shouldHaveCount(2);
    }

    function it_can_be_constructed_to_throw_exceptions_on_failures()
    {
        $this->beConstructedWith(Manager::instance(), 42, true);
        $this->shouldThrow('Moccalotto\Valit\ValidationException')
            ->during('isNegative', []);
    }

    function it_can_be_modified_to_throw_exceptions_on_failures()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->isNegative();
        $this->shouldThrow('Moccalotto\Valit\ValidationException')
            ->during('orThrowException', []);
    }
}
