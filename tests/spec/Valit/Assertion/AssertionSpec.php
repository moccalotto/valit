<?php

namespace spec\Valit\Assertion;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssertionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('name', ['args']);
        $this->shouldHaveType('Valit\Assertion\Assertion');
    }

    function it_has_acccessible_name_attribute()
    {
        $this->beConstructedWith('foo', ['bar']);

        $this->name->shouldBe('foo');
    }

    function it_has_acccessible_args_attribute()
    {
        $this->beConstructedWith('foo', ['bar']);

        $this->args->shouldBe(['bar']);
    }
}
