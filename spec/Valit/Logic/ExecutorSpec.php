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

namespace spec\Valit\Logic;

use Valit\check;
use Valit\Manager;
use Valit\Logic\OneOf;
use PhpSpec\ObjectBehavior;
use Valit\Result\AssertionResult;
use Valit\Result\ContainerResultBag;
use Valit\Result\AssertionResultBag;
use Valit\Exceptions\ValueRequiredException;

class ExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->shouldHaveType('Valit\Logic\Executor');
    }

    function it_can_execute_zero_branches()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $result = $this->execute();

        $result->shouldBeArray();
        $result->shouldHaveCount(0);

        $this->results()->shouldBe($result->getWrappedObject());
    }

    function it_stores_its_results()
    {
        $this->beConstructedWith(Manager::instance(), ['greaterThan(1)', 'greaterThan(2)']);
        $results = $this->execute(true, 2);

        $results->shouldBeArrayOfContainerResultBags();
        $results->shouldHaveCount(2);

        $this->results()->shouldBe($results->getWrappedObject());
    }

    function it_can_execute_branches_with_assertion_results()
    {
        $branches = [
            new AssertionResult(true, 'branch 1', []),
            new AssertionResult(false, 'branch 2', []),
            new AssertionResult(false, 'branch 3', []),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);

        $results = $this->execute();

        $results->shouldBeArrayOfContainerResultBags();
        $results->shouldHaveCount(3);

        $results[0]->success()->shouldBe(true);
        $results[1]->success()->shouldBe(false);
        $results[2]->success()->shouldBe(false);
    }

    function it_can_execute_branches_with_Templates()
    {
        $branches = [
            Check::value()->containsString('foo')->containsString('bar'),
            Check::value()->containsString('wee')->containsString('bar'),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);

        $results = $this->execute(true, 'foobar');

        $results->shouldBeArrayOfContainerResultBags();
        $results->shouldHaveCount(2);

        $results[0]->success()->shouldBe(true);
        $results[1]->success()->shouldBe(false);
    }

    function it_can_execute_branches_with_AssertionResultBags()
    {
        $branches = [
            (new AssertionResultBag(null, 'branch 1', false))->addAssertionResult(new AssertionResult(true, 'branch 1')),
            (new AssertionResultBag(null, 'branch 2', false))->addAssertionResult(new AssertionResult(false, 'branch 2')),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);

        $results = $this->execute(false);

        $results->shouldBeArrayOfContainerResultBags();
        $results->shouldHaveCount(2);

        $results[0]->success()->shouldBe(true);
        $results[1]->success()->shouldBe(false);
    }

    function it_can_execute_branches_with_Logics()
    {
        $branches = [
            new OneOf(Manager::instance(), [
                'isString & longerThan(5)',
                'isInt & greaterThan(10)'
            ]),
            new OneOf(Manager::instance(), [
                'isFloat',
                'isString',
            ]),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);

        $resultsInt11 = $this->execute(true, 11);

        $resultsInt11->shouldBeArrayOfContainerResultBags();
        $resultsInt11->shouldHaveCount(2);

        $resultsInt11[0]->success()->shouldBe(true);
        $resultsInt11[1]->success()->shouldBe(false);

        $resultsStringFoo = $this->execute(true, 'foo');

        $resultsStringFoo->shouldBeArrayOfContainerResultBags();
        $resultsStringFoo->shouldHaveCount(2);

        $resultsStringFoo[0]->success()->shouldBe(false);
        $resultsStringFoo[1]->success()->shouldBe(true);
    }

    function it_can_execute_branches_on_containers()
    {
        $branches = [
            'name' => 'isString & isUppercase',
            'email' => 'isString & isEmail',
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $results = $this->execute(true, [
            'name' => 'KIM',
            'email' => 'this-is-not-an-email',
        ]);

        $results->shouldBeArrayOfContainerResultBags();
        $results->shouldHaveCount(2);

        $results[0]->success()->shouldBe(true);
        $results[1]->success()->shouldBe(false);
    }

    function it_throws_exception_if_required_value_is_missing()
    {
        $branches = [
            Check::value()->containsString('foo')->containsString('bar'),
            Check::value()->containsString('wee')->containsString('bar'),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $this->shouldThrow(ValueRequiredException::class)->during(
            'execute', [false, null]
        );
    }

    function getMatchers()
    {
        return [
            'beArrayOfContainerResultBags' => function ($subject) {
                if (!is_array($subject)) {
                    return false;
                }

                foreach ($subject as $result) {
                    if (!is_a($result, ContainerResultBag::class)) {
                        return false;
                    }
                }

                return true;
            }
        ];
    }
}
