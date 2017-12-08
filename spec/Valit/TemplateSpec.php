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

namespace spec\Valit;

use Valit\Manager;
use PhpSpec\ObjectBehavior;
use Valit\Validators\SingleValueValidator;

class TemplateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Template');

        $this->assertions->shouldHaveType('Valit\Assertion\AssertionBag');
        $this->assertions->shouldHaveCount(0);
    }

    function it_can_add_assertions()
    {
        $this->addAssertion('greaterThanOrEqual', [18])->shouldHaveType('Valit\Template');

        $this->assertions->shouldHaveCount(1);

        $this->assertions->all()[0]->shouldHaveType('Valit\Assertion\Assertion');
        $this->assertions->all()[0]->name->shouldBe('greaterThanOrEqual');
        $this->assertions->all()[0]->args->shouldBe([18]);
    }

    function it_can_add_assertions_dynamically()
    {
        $this->greaterThanOrEqual(18)->shouldHaveType('Valit\Template');

        $this->assertions->all()[0]->shouldHaveType('Valit\Assertion\Assertion');
        $this->assertions->all()[0]->name->shouldBe('greaterThanOrEqual');
        $this->assertions->all()[0]->args->shouldBe([18]);
    }

    function it_can_execute_assertions_on_a_fluent_instance(SingleValueValidator $validator)
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

        $validator->shouldHaveType('Valit\Validators\SingleValueValidator');

        $validator->success()->shouldBe(false);

        $this->whereValueIs(18)->errors()->shouldHaveCount(0);
        $this->whereValueIs(100)->errors()->shouldHaveCount(1);
        $this->whereValueIs(NAN)->errors()->shouldHaveCount(2);
    }
}
