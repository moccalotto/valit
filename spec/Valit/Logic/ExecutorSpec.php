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
use PhpSpec\ObjectBehavior;
use Valit\Result\AssertionResult;
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
        $this->results()->shouldBe($result->getWrappedObject());
        $result->shouldHaveCount(0);
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

        foreach ($results as $result) {
            $result->shouldHaveType(AssertionResultBag::class);
        }
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
        foreach ($results as $result) {
            $result->shouldHaveType(AssertionResultBag::class);
        }

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
        foreach ($results as $result) {
            $result->shouldHaveType(AssertionResultBag::class);
        }

        $results[0]->success()->shouldBe(true);
        $results[1]->success()->shouldBe(false);
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
        foreach ($results as $result) {
            $result->shouldHaveType(AssertionResultBag::class);
        }

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

}
