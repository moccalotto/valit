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

class OneOfSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->shouldHaveType('Valit\Logic\OneOf');
    }

    function it_implements_Logic_interface()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->shouldHaveType('Valit\Contracts\Logic');
    }

    function it_returns_AssertionResults()
    {
        $this->beConstructedWith(Manager::instance(), []);
        $this->execute(true, 'foobar')->shouldHaveType(AssertionResult::class);
    }

    function it_will_not_accept_two_successfull_branches()
    {
        $branches = [
            Check::value()->containsString('foo')->containsString('bar'),
            'containsString("oo") & containsString("ar")'
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $this->execute(true, 'foobar')->success()->shouldBe(false);
    }

    function it_will_accept_one_successful_branch()
    {
        $branches = [
            'name' => 'isString & isUppercase',
            'email' => 'isString & isEmail',
        ];

        $container = [
            'name' => 'KIM',
            'email' => 'this-is-not-an-email',
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $this->execute(true, $container)->success()->shouldBe(true);
    }

    function it_will_not_accept_zero_successful_branches()
    {
        $branches = [
            'name' => 'isString & isUppercase',
            'email' => 'isString & isEmail',
        ];

        $container = [
            'the keys »name« and »email« are missing from this container',
        ];

        $this->beConstructedWith(Manager::instance(), $branches);
        $this->execute(true, $container)->success()->shouldBe(false);
    }

    function it_will_only_accept_if_exactly_one_branch_succeeds()
    {
        $this->beConstructedWith(Manager::instance(), [
            'containsString("a")',
            'containsString("b")',
            'containsString("c")',
        ]);
        $this->execute(true, 'a')->success()->shouldBe(true);
        $this->execute(true, 'b')->success()->shouldBe(true);
        $this->execute(true, 'c')->success()->shouldBe(true);
        $this->execute(true, 'ab')->success()->shouldBe(false);
        $this->execute(true, 'ac')->success()->shouldBe(false);
        $this->execute(true, 'bc')->success()->shouldBe(false);
        $this->execute(true, 'abc')->success()->shouldBe(false);
        $this->execute(true, 'XYZ')->success()->shouldBe(false);
        $this->execute(true, null)->success()->shouldBe(false);
        $this->execute(true, curl_init())->success()->shouldBe(false);
    }
}
