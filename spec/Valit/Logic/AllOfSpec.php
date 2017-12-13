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

class AllOfSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), ['isInt']);
        $this->shouldHaveType('Valit\Logic\AllOf');
    }

    function it_implements_Logic_interface()
    {
        $this->beConstructedWith(Manager::instance(), ['isInt']);
        $this->shouldHaveType('Valit\Contracts\Logic');
    }

    function it_returns_AssertionResult()
    {
        $this->beConstructedWith(Manager::instance(), ['isInt']);
        $this->execute(true, 'foobar')->shouldHaveType(AssertionResult::class);
    }

    function it_will_accept_empty_branch_set()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->execute(false)->success()->shouldBe(true);
    }

    function it_will_only_accept_if_all_branches_fail()
    {
        $this->beConstructedWith(Manager::instance(), [
            'containsString("a")',
            'containsString("b")',
            'containsString("c")',
        ]);
        $this->execute(true, 'XYZ')->success()->shouldBe(false);
        $this->execute(true, 'a')->success()->shouldBe(false);
        $this->execute(true, 'b')->success()->shouldBe(false);
        $this->execute(true, 'c')->success()->shouldBe(false);
        $this->execute(true, 'ab')->success()->shouldBe(false);
        $this->execute(true, 'ac')->success()->shouldBe(false);
        $this->execute(true, 'bc')->success()->shouldBe(false);
        $this->execute(true, 'abc')->success()->shouldBe(true);
        $this->execute(true, null)->success()->shouldBe(false);
        $this->execute(true, curl_init())->success()->shouldBe(false);
    }
}
