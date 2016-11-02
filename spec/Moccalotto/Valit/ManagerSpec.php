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
        $this->beConstructedWith([]);
        $this->shouldHaveType('Moccalotto\Valit\Manager');
        $this->shouldHaveType('Moccalotto\Valit\Contracts\CheckManager');
    }

    public function it_loads_core_providers()
    {
        $this->beConstructedThrough('create');

        $this->hasCheck('isNumeric')->shouldBe(true);
        $this->hasCheck('decimalString')->shouldBe(true);
    }

    public function it_can_execute_checks()
    {
        $this->beConstructedThrough('create');

        $this->executeCheck('isNumeric', 42, [])
            ->shouldHaveType('Moccalotto\Valit\Result');

        $this->executeCheck('isNumeric', 42, [])->success()->shouldBe(true);
    }

    public function it_has_default_global_instance()
    {
        $this->beConstructedThrough('instance');

        $this->shouldHaveType('Moccalotto\Valit\Contracts\CheckManager');

        $this->instance()->shouldBe($this->getWrappedObject());
    }

    /**
     * Must be last because of use of setAsGlobal()
     */
    public function it_can_override_global()
    {
        $this->beConstructedThrough('create');

        $this->instance()->shouldNotBe($this->getWrappedObject());

        $this->setAsGlobal();

        $this->instance()->shouldBe($this->getWrappedObject());
    }

}
