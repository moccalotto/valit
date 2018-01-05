<?php

namespace spec\Valit\Assertion;

use PhpSpec\ObjectBehavior;
use Valit\Validators\ValueValidator;

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */
class AssertionBagSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Assertion\AssertionBag');
    }

    function it_is_countable()
    {
        $this->shouldHaveCount(0);
        $this->assertions->shouldHaveCount(0);
    }

    function it_contains_assertions(\Valit\Assertion\Assertion $assertion)
    {
        $this->addAssertion($assertion);
        $this->shouldHaveCount(1);
        $this->assertions[0]->shouldBe($assertion);
    }

    function it_has_flags()
    {
        $this->flags->shouldHaveCount(0);

        $this->hasFlag('foo')->shouldBe(false);

        $this->setFlag('foo')->shouldBe($this);

        $this->flags->shouldHaveCount(1);

        $this->hasFlag('foo')->shouldBe(true);
    }

    function it_can_unset_flags()
    {
        $this->setFlag('foo');
        $this->setFlag('foo', false);
        $this->hasFlag('foo')->shouldBe(false);
    }

    function it_can_add_assertions_dynamically()
    {
        $this->greaterThanOrEqual(18)->shouldHaveType('Valit\Assertion\AssertionBag');

        $this->all()[0]->shouldHaveType('Valit\Assertion\Assertion');
        $this->all()[0]->name->shouldBe('greaterThanOrEqual');
        $this->all()[0]->args->shouldBe([18]);
    }

    function it_can_execute_assertions_on_a_fluent_instance(ValueValidator $validator)
    {
        $validator->executeCheck('greaterThanOrEqual', [18])
            ->shouldBeCalled()
            ->willReturn($validator->getWrappedObject());

        $validator->executeCheck('isLowerThan', [100])
            ->shouldBeCalled()
            ->willReturn($validator->getWrappedObject());

        $this->greaterThanOrEqual(18);
        $this->isLowerThan(100);
        $this->applyToValidator($validator);
    }

    function it_can_execute_assertions_on_a_self_created_fluent_interface()
    {
        $validator = $this->greaterThanOrEqual(18)
            ->isLowerThan(100)
            ->whereValueIs(0);

        $validator->shouldHaveType('Valit\Validators\ValueValidator');

        $validator->success()->shouldBe(false);

        $this->whereValueIs(18)->errors()->shouldHaveCount(0);
        $this->whereValueIs(100)->errors()->shouldHaveCount(1);
        $this->whereValueIs(NAN)->errors()->shouldHaveCount(2);
    }
}
