<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
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

class AnyOfSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), ['isInt']);
        $this->shouldHaveType('Valit\Logic\AnyOf');
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

    function it_will_not_accept_empty_branch_set()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->execute(false)->success()->shouldBe(false);
    }

    function it_will_only_accept_if_all_branches_fail()
    {
        $this->beConstructedWith(Manager::instance(), [
            'containsString("a")',
            'containsString("b")',
            'containsString("c")',
        ]);
        $this->execute(true, 'XYZ')->success()->shouldBe(false);
        $this->execute(true, 'a')->success()->shouldBe(true);
        $this->execute(true, 'b')->success()->shouldBe(true);
        $this->execute(true, 'c')->success()->shouldBe(true);
        $this->execute(true, 'ab')->success()->shouldBe(true);
        $this->execute(true, 'ac')->success()->shouldBe(true);
        $this->execute(true, 'bc')->success()->shouldBe(true);
        $this->execute(true, 'abc')->success()->shouldBe(true);
        $this->execute(true, null)->success()->shouldBe(false);
        $this->execute(true, curl_init())->success()->shouldBe(false);
    }
}
