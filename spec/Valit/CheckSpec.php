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

class CheckSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Check');
    }

    function it_creates_single_value_validator()
    {
        $this->that(42)->shouldHaveType('Valit\Validators\ValueValidator');
    }

    function it_creates_container_validator()
    {
        $this->container([1,2,3])->shouldHaveType('Valit\Validators\ContainerValidator');
    }
}
