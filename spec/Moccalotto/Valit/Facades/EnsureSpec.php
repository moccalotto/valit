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

namespace spec\Moccalotto\Valit\Facades;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EnsureSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Facades\Ensure');
    }

    function it_creates_fluent()
    {
        $this->that(42)->shouldHaveType('Moccalotto\Valit\Fluent');
    }

    function it_creates_container_validator()
    {
        $this->container([1,2,3])->shouldHaveType('Moccalotto\Valit\ContainerValidator');
    }
}
