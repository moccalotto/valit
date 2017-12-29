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

namespace spec\Valit\Validators;

use Valit\Manager;
use Prophecy\Argument;
use Valit\Logic\OneOf;
use PhpSpec\ObjectBehavior;

class ContainerValidatorSpec extends ObjectBehavior
{
    protected $testData = [
        'someString' => 'foo',
        'someInt' => 42,
        'someFloat' => 19.87,
        'someArray' => [
            [
                'key' => 'thing1',
                'value' => 2,
            ],
            [
                'key' => 'thing2',
                'value' => 4,
            ],
        ],
        'someAssoc' => [
            'thing1' => 'foo',
            'thing2' => 'bar',
            'thing3' => 'baz',
        ],
        'some' => [
            'deeply' => [
                'nested' => [
                    'entry' => 'hola',
                ],
            ],
        ],
    ];

    function it_is_initializable(Manager $fakeManager)
    {
        $this->beConstructedWith($fakeManager, $this->testData, true);
        $this->shouldHaveType('Valit\Validators\ContainerValidator');
    }

    function it_handles_empty_filters(Manager $fakeManager)
    {
        $this->beConstructedWith($fakeManager, $this->testData, true);
        $result = $this->passes([]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');
        $result->results()->shouldBe([]);
    }

    function it_handles_simple_string_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->passes([
            'someString' => 'required & string',
            'someInt' => 'integer & greaterThan(40) & lowerThan(43)',
            'someFloat' => 'greaterThan(19) & lowerThan(20)',
        ]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');
        $result->errors()->shouldBe([]);
    }

    function it_handles_simple_array_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->passes([
            'someString'    => ['required', 'string'],
            'someInt'       => ['integer', 'greaterThan(40)', 'lowerThan(43)'],
            'someFloat'     => new OneOf(Manager::instance(), [
                'isFloat & greaterThan(10) & lowerThan(20)', // true
                'isFloat & greaterThan(20)',                 // false
                'isInt',                                     // false
            ])
        ]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');
        $result->errors()->shouldBe([]);
    }

    function it_handles_simple_assoc_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->passes([
            'someString'    => ['required', 'string'],
            'someInt'       => ['integer', 'greaterThan' => 40, 'lowerThan' => 43],
            'someFloat'     => ['greaterThan' => [19], 'lowerThan' => [20]],
        ]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');
        $result->errors()->shouldBe([]);
    }

    function it_handles_nested_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->passes([
            'someArray' => 'array & hasNumericIndex',
            'someArray/*/key' => 'string',
            'someArray/*/value' => 'int & divisibleBy(2)',
            'someArray/*/notPresent' => 'optional & string',

            'someAssoc' => 'associativeArray',
            'someAssoc/*' => 'string',
        ]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');
        $result->errors()->shouldBe([]);
        $result->results('someArray')->shouldHaveCount(3);
        $result->results('someAssoc')->shouldHaveCount(2);
    }

    public function it_handles_object_containers()
    {
        $objectData = json_decode(json_encode($this->testData));

        $this->beConstructedWith(Manager::instance(), $objectData, false);
        $result = $this->passes([
            'someArray' => 'array & hasNumericIndex',
            'someArray/*' => 'object',
            'someArray/*/key' => 'string',
            'someArray/*/value' => 'int & divisibleBy(2)',
            'someArray/*/notPresent' => 'optional & string',

            'someAssoc' => 'object',
            'someAssoc/*' => 'string',
        ]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');
        $result->errors()->shouldBe([]);
        $result->results('someArray')->shouldHaveCount(3);
        $result->results('someArray/0')->shouldHaveCount(2);
        $result->results('someArray/1')->shouldHaveCount(2);
        $result->results('someArray/2')->shouldHaveCount(0);
        $result->results('someAssoc')->shouldHaveCount(2);
    }

    function it_finds_errors()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, false);

        $result = $this->passes([
            'notFound' => 'required',
            'some/*/complex/*/filter/glob' => 'required',
            'someAssoc/*' => 'optional & isInt',
            'some/*/*/*' => 'string & equals("hola")',
            'some/*/nested/*' => 'required',
            'some/deeply/nested/*' => 'required & string',
            'some/deeply/nested/entry' => 'string',
        ]);

        $result->errors()->shouldHaveCount(5);
        $result->errors('notFound')->shouldHaveCount(1);
        $result->errors('some/*/complex/*/filter/glob')->shouldHaveCount(1); // required, but value not found
        $result->errors('someAssoc/thing1')->shouldHaveCount(1);
        $result->errors('someAssoc/thing2')->shouldHaveCount(1);
        $result->errors('someAssoc/thing3')->shouldHaveCount(1);
        $result->results('notFound')->shouldHaveCount(1);
    }

    function it_finds_errors_in_arrays()
    {
        $testData = [
            'a' => 1234,
            'b' => [
                'c' => 'g',
                'd' => 'h',
            ],
        ];

        $this->beConstructedWith(Manager::instance(), $testData, false);

        $this->as('foo')->shouldBe($this->getWrappedObject());

        $result = $this->passes([
            'a' => 'required & isString & longerThan(100)',
            'b' => 'required & isArray',
            'b/c' => 'required & isInt & greaterThan(10)',
            'b/d' => 'required & isString',
            'b/e' => 'required',
            'c' => 'required & isString & longerThan(100)',
        ]);

        $result->shouldHaveType('Valit\Result\ContainerResultBag');

        $result->shouldThrow('Valit\Exceptions\InvalidContainerException')
            ->during('orThrowException');
    }
}
