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

class OneOfSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->shouldHaveType('Valit\Logic\OneOf');
    }

    function it_can_execute_zero_branches()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $result = $this->execute();

        $result->shouldHaveType(AssertionResult::class);
        $result->success()->shouldBe(false);
    }

    function it_can_execute_branches_with_assertion_results()
    {
        $branches = [
            new AssertionResult(true, 'branch 1', []),
            new AssertionResult(false, 'branch 2', []),
            new AssertionResult(false, 'branch 3', []),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $result = $this->execute();
        $result->shouldHaveType(AssertionResult::class);
        $result->success()->shouldBe(true);
    }

    function it_can_execute_branches_with_templates()
    {
        $branches = [
            Check::value()->containsString('foo')->containsString('bar'),
            Check::value()->containsString('wee')->containsString('bar'),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $result = $this->execute(true, 'foobar');
        $result->shouldHaveType(AssertionResult::class);
        $result->success()->shouldBe(true);
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

    function it_requires_one_and_only_one_branch_to_succeed()
    {
        $branches = [
            Check::value()->containsString('foo')->containsString('bar'),
            Check::value()->containsString('f')->containsString('b'),
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $result = $this->execute(true, 'foobar');
        $result->shouldHaveType(AssertionResult::class);
        $result->success()->shouldBe(false);
    }
}
