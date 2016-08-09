<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace spec\Moccalotto\Valit\Facades;

use PhpSpec\ObjectBehavior;

class CheckSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Facades\Check');
    }

    public function it_creates_fluent()
    {
        $this->that(42)->shouldHaveType('Moccalotto\Valit\Fluent');
    }
}
