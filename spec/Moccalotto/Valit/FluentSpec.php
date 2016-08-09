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

use Moccalotto\Valit\Manager;
use PhpSpec\ObjectBehavior;

class FluentSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Moccalotto\Valit\Fluent');
    }

    public function it_can_execute_fluent_checks()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->isNumeric()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->success()->shouldBe(true);
        $this->isNegative()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->success()->shouldBe(false);
    }

    public function it_can_return_results()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->isNumeric()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->isNegative()->shouldHaveType('Moccalotto\Valit\Fluent');
        $this->results()->shouldHaveCount(2);
    }

    public function it_can_be_constructed_to_throw_exceptions_on_failures()
    {
        $this->beConstructedWith(Manager::instance(), 42, true);
        $this->shouldThrow('Moccalotto\Valit\ValidationException')
            ->during('isNegative', []);
    }

    public function it_can_be_modified_to_throw_exceptions_on_failures()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->isNegative();
        $this->shouldThrow('Moccalotto\Valit\ValidationException')
            ->during('orThrowException', []);
    }
}
