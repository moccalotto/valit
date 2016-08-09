<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace spec\Moccalotto\Valit\Providers;

use PhpSpec\ObjectBehavior;

class JsonCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\JsonCheckProvider');
    }

    public function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    public function it_checks_isJson()
    {
        $this->checkIsJson('')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isJson');
        $this->provides()->shouldHaveKey('validJson');
        $this->provides()->shouldHaveKey('isValidJson');

        $this->checkIsJson('{}')->success()->shouldBe(true);
        $this->checkIsJson('[]')->success()->shouldBe(true);
        $this->checkIsJson(json_encode([]))->success()->shouldBe(true);

        $this->checkIsJson('')->success()->shouldBe(false);
        $this->checkIsJson(null)->success()->shouldBe(false);
        $this->checkIsJson(0)->success()->shouldBe(false);
        $this->checkIsJson(curl_init())->success()->shouldBe(false);
        $this->checkIsJson([])->success()->shouldBe(false);
        $this->checkIsJson((object) [])->success()->shouldBe(false);
    }
}
