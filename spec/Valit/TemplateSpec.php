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
use Valit\Contracts\FluentCheckInterface;

class TemplateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Template');

        $this->checks->shouldHaveCount(0);
    }

    function it_can_add_checks()
    {
        $this->addCheck('greaterThanOrEqual', [18])->shouldHaveType('Valit\Template');

        $this->checks->shouldHaveCount(1);

        $this->checks[0]->shouldHaveType('Valit\Container\Filter');
        $this->checks[0]->name->shouldBe('greaterThanOrEqual');
        $this->checks[0]->args->shouldBe([18]);
    }

    function it_can_add_checks_dynamically()
    {
        $this->greaterThanOrEqual(18)->shouldHaveType('Valit\Template');

        $this->checks[0]->shouldHaveType('Valit\Container\Filter');
        $this->checks[0]->name->shouldBe('greaterThanOrEqual');
        $this->checks[0]->args->shouldBe([18]);
    }

    function it_can_execute_checks_on_a_fluent_instance(FluentCheckInterface $fluent)
    {
        $fluent->executeCheck('greaterThanOrEqual', [18])
            ->shouldBeCalled()
            ->willReturn($fluent->getWrappedObject());

        $fluent->executeCheck('isLowerThan', [100])
            ->shouldBeCalled()
            ->willReturn($fluent->getWrappedObject());

        $this->greaterThanOrEqual(18);
        $this->isLowerThan(100);
        $this->executeOnFluent($fluent);
    }

    function it_can_execute_checks_on_a_self_created_fluent_interface()
    {
        $fluent = $this->greaterThanOrEqual(18)
            ->isLowerThan(100)
            ->whereValueIs(0);

        $fluent->shouldHaveType('Valit\Fluent');

        $fluent->success()->shouldBe(false);

        $this->whereValueIs(18)->errors()->shouldHaveCount(0);
        $this->whereValueIs(100)->errors()->shouldHaveCount(1);
        $this->whereValueIs(NAN)->errors()->shouldHaveCount(2);
    }
}
