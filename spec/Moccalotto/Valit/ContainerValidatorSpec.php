<?php

namespace spec\Moccalotto\Valit;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Moccalotto\Valit\Manager;

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
    ];

    function it_is_initializable(Manager $fakeManager)
    {
        $this->beConstructedWith($fakeManager, $this->testData, true);
        $this->shouldHaveType('Moccalotto\Valit\ContainerValidator');
    }

    function it_handles_empty_filters(Manager $fakeManager)
    {
        $this->beConstructedWith($fakeManager, $this->testData, true);
        $result = $this->against([]);

        $result->shouldHaveType('Moccalotto\Valit\ContainerValidationResult');
        $result->results()->shouldBe([]);
    }

    function it_handles_simple_string_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->against([
            'someString' => 'required & string',
            'someInt' => 'required & integer & greaterThan(40) & lowerThan(43)',
            'someFloat' => 'required & greaterThan(19) & lowerThan(20)',
        ]);

        $result->shouldHaveType('Moccalotto\Valit\ContainerValidationResult');
        $result->errors()->shouldBe([]);
    }

    function it_handles_simple_array_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->against([
            'someString'    => ['required', 'string'],
            'someInt'       => ['required', 'integer', 'greaterThan(40)', 'lowerThan(43)'],
            'someFloat'     => ['required', 'greaterThan(19)', 'lowerThan(20)'],
        ]);

        $result->shouldHaveType('Moccalotto\Valit\ContainerValidationResult');
        $result->errors()->shouldBe([]);
    }

    function it_handles_simple_assoc_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->against([
            'someString'    => ['required', 'string'],
            'someInt'       => ['required', 'integer', 'greaterThan' => 40, 'lowerThan' => 43],
            'someFloat'     => ['required', 'float', 'greaterThan' => [19], 'lowerThan' => [20]],
        ]);

        $result->shouldHaveType('Moccalotto\Valit\ContainerValidationResult');
        $result->errors()->shouldBe([]);
    }

    function it_handles_nested_filters()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, true);
        $result = $this->against([
            'someArray' => 'required & array & hasNumericIndex',
            'someArray/*/key' => 'required & string',
            'someArray/*/value' => 'required & int & divisibleBy(2)',
            'someArray/*/notPresent' => 'string',

            'someAssoc' => 'required & associativeArray',
            'someAssoc/*' => 'required & string',
        ]);

        $result->shouldHaveType('Moccalotto\Valit\ContainerValidationResult');
        $result->errors()->shouldBe([]);
        $result->results()->shouldHaveKey('someArray');
        $result->results()->shouldHaveKey('someAssoc');
    }

    function it_finds_errors()
    {
        $this->beConstructedWith(Manager::instance(), $this->testData, false);

        $result = $this->against([
            'notFound' => 'required',
            'some/*/complex/*/filter/glob' => 'required',
            'someAssoc/*' => 'int',
        ]);

        $result->errors()->shouldHaveCount(5);
        $result->errors()->shouldHaveKey('notFound');
        $result->errors()->shouldHaveKey('some/*/complex/*/filter/glob');
        $result->errors()->shouldHaveKey('someAssoc/thing1');
        $result->errors()->shouldHaveKey('someAssoc/thing2');
        $result->errors()->shouldHaveKey('someAssoc/thing3');
    }
}
