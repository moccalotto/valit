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

namespace spec\Valit\Validators;

use Valit\Manager;
use PhpSpec\ObjectBehavior;

class ValueValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Valit\Validators\ValueValidator');
    }

    function it_can_execute_fluent_checks()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Valit\Validators\ValueValidator');
        $this->isNumeric()->shouldHaveType('Valit\Validators\ValueValidator');
        $this->success()->shouldBe(true);
        $this->isNegative()->shouldHaveType('Valit\Validators\ValueValidator');
        $this->success()->shouldBe(false);
    }

    function it_can_return_results()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->shouldHaveType('Valit\Validators\ValueValidator');
        $this->isNumeric()->shouldHaveType('Valit\Validators\ValueValidator');
        $this->isNegative()->shouldHaveType('Valit\Validators\ValueValidator');
        $this->successes->shouldBe(1);
        $this->failures->shouldBe(1);
        $this->results()->shouldHaveCount(2);
    }

    function it_can_be_constructed_to_throw_exceptions_on_failures()
    {
        $this->beConstructedWith(Manager::instance(), 42, true);
        $this->shouldThrow('Valit\Exceptions\InvalidValueException')
            ->during('isNegative', []);
    }

    function it_can_be_modified_to_throw_exceptions_on_failures()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->isNegative();
        $this->shouldThrow('Valit\Exceptions\InvalidValueException')
            ->during('orThrowException', []);
    }

    function it_can_return_the_initial_value()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->value()->shouldBe(42);
        $this->isNegative()->value()->shouldBe(42);
    }

    function it_can_return_the_initial_value_with_a_fallback()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $this->valueOr('foo')->shouldBe(42);
        $this->isNegative()->valueOr('foo')->shouldBe('foo');
    }

    function it_can_return_all_errors()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $errors = $this->isNumeric()->isNegative()->errors();

        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_can_return_rendered_error_messages()
    {
        $this->beConstructedWith(Manager::instance(), 42, false);
        $errorMessages = $this->isNumeric()->isNegative()->errorMessages();

        $errorMessages->shouldBeArray();
        $errorMessages->shouldHaveCount(1);

        $errorMessages[0]->shouldContain('less than 0');
    }
}
