<?php

namespace spec\Valit\Result;

use PhpSpec\ObjectBehavior;

class SingleAssertionResultSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->shouldHaveType('Valit\Result\SingleAssertionResult');
    }

    public function it_has_a_success_status()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->success()->shouldBe(true);
    }

    public function it_has_a_message()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->message()->shouldBe('Foo');
    }

    public function it_has_a_context()
    {
        $this->beConstructedWith(true, 'Foo', ['foo' => 'bar']);
        $this->context()->shouldBe(['foo' => 'bar']);
    }

    public function it_can_render_message()
    {
        $this->beConstructedWith(
            true,
            'The variable »{name}« should be ≤ {number}, but it had the value {value}',
            [
                'number' => 42,
            ]
        );

        $this->renderMessage('foo', 1987)->shouldBe(
            'The variable »foo« should be ≤ 42, but it had the value 1987'
        );
    }

    public function it_can_format_messages()
    {
        $this->beConstructedWith(
            true,
            '{name} => {value}, {value:raw}, {value:type}, {value:float}, {value:hex}',
            []
        );

        $this->renderMessage('object', new \StdClass())->shouldBe(
            'object => Object (stdClass), Object (stdClass), object, [not numeric], [not integer]'
        );

        $this->renderMessage('array', [1, 2, 3, 4])->shouldBe(
            'array => Array (4 entries), Array (4 entries), array, [not numeric], [not integer]'
        );

        $this->renderMessage('int', 42)->shouldBe(
            'int => 42, 42, integer, 42, 2a'
        );

        $this->renderMessage('float', 19.88)->shouldBe(
            'float => 19.88, 19.88, double, 19.88, [not integer]'
        );

        $this->renderMessage('string', 'FOOBAR')->shouldBe(
            'string => "FOOBAR", FOOBAR, string, [not numeric], [not integer]'
        );
    }
}
