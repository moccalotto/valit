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

use PhpSpec\ObjectBehavior;

class ManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith([]);
        $this->shouldHaveType('Valit\Manager');
        $this->shouldHaveType('Valit\Contracts\CheckManager');
    }

    function it_loads_core_providers()
    {
        $this->beConstructedThrough('create');

        $this->hasCheck('isNumeric')->shouldBe(true);
        $this->hasCheck('decimalString')->shouldBe(true);
    }

    function it_can_execute_checks()
    {
        $this->beConstructedThrough('create');

        $this->executeCheck('isNumeric', 42, [])
            ->shouldHaveType('Valit\Result\AssertionResult');

        $this->executeCheck('isNumeric', 42, [])->success()->shouldBe(true);

        $this->shouldThrow('UnexpectedValueException')->duringExecuteCheck(
            'someCheckThatDoesntExist',
            42,
            []
        );
    }

    function it_has_default_global_instance()
    {
        $this->beConstructedThrough('instance');

        $this->shouldHaveType('Valit\Contracts\CheckManager');

        $this->instance()->shouldBe($this->getWrappedObject());
    }

    /**
     * Must be last because of use of setGlobal().
     */
    function it_can_override_global()
    {
        $this->beConstructedThrough('create');

        $this->instance()->shouldNotBe($this->getWrappedObject());

        $this->setGlobal();

        $this->instance()->shouldBe($this->getWrappedObject());
    }
}
