<?php

namespace spec\Valit\Assertion;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssertionBagFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('');
        $this->shouldHaveType('Valit\Assertion\AssertionBagFactory');
    }

    function it_has_public_assertions_attribute()
    {
        $this->beConstructedWith('');

        $this->assertions->shouldHaveType('Valit\Assertion\AssertionBag');
        $this->assertions->shouldHaveCount(0);
    }

    function it_can_parse_simple_expression_strings()
    {
        $this->beConstructedWith('greaterThan(0)');

        $this->assertions->shouldHaveCount(1);
        $this->assertions->all()[0]->shouldHaveType('Valit\Assertion\Assertion');
        $this->assertions->all()[0]->name->shouldBe('greaterThan');
        $this->assertions->all()[0]->args->shouldBe([0]);
    }

    function it_can_parse_complex_expression_strings()
    {
        $this->beConstructedWith('greaterThan(0) & lowerThan(100)');

        $this->assertions->shouldHaveCount(2);
        $this->assertions->all()[0]->name->shouldBe('greaterThan');
        $this->assertions->all()[0]->args->shouldBe([0]);
        $this->assertions->all()[1]->name->shouldBe('lowerThan');
        $this->assertions->all()[1]->args->shouldBe([100]);
    }

    function it_can_parse_arrays_with_expression_strings()
    {
        $this->beConstructedWith(['greaterThan(0)', 'lowerThan(100)']);

        $this->assertions->shouldHaveCount(2);
        $this->assertions->all()[0]->name->shouldBe('greaterThan');
        $this->assertions->all()[0]->args->shouldBe([0]);
        $this->assertions->all()[1]->name->shouldBe('lowerThan');
        $this->assertions->all()[1]->args->shouldBe([100]);
    }

    function it_can_parse_arrays_with_parameters_defined()
    {
        $this->beConstructedWith([
            'containsString' => ['foo'],
            ['containsString', 'bar'],
            ['containsString', ' '],
            'stringLongerThan' => [6],
            'stringShorterThan' => 255, // array brackets not needed.
        ]);

        $this->assertions->shouldHaveCount(5);

        $this->assertions->all()[0]->name->shouldBe('containsString');
        $this->assertions->all()[0]->args->shouldBe(['foo']);

        $this->assertions->all()[1]->name->shouldBe('containsString');
        $this->assertions->all()[1]->args->shouldBe(['bar']);

        $this->assertions->all()[2]->name->shouldBe('containsString');
        $this->assertions->all()[2]->args->shouldBe([' ']);

        $this->assertions->all()[3]->name->shouldBe('stringLongerThan');
        $this->assertions->all()[3]->args->shouldBe([6]);

        $this->assertions->all()[4]->name->shouldBe('stringShorterThan');
        $this->assertions->all()[4]->args->shouldBe([255]);
    }

    function it_parses_required_keyword()
    {
        $this->beConstructedWith('required & lowerThan(255)');

        $this->assertions->shouldHaveCount(1);
        $this->assertions->all()[0]->name->shouldBe('lowerThan');
        $this->assertions->all()[0]->args->shouldBe([255]);

        $this->assertions->hasFlag('optional')->shouldBe(false);
    }

    function it_parses_optional_keyword()
    {
        $this->beConstructedWith('optional & lowerThan(255)');

        $this->assertions->shouldHaveCount(1);
        $this->assertions->all()[0]->name->shouldBe('lowerThan');
        $this->assertions->all()[0]->args->shouldBe([255]);

        $this->assertions->hasFlag('optional')->shouldBe(true);
    }
}
