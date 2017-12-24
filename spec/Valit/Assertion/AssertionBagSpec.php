<?php

namespace spec\Valit\Assertion;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
        $this->add($assertion);
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
}
