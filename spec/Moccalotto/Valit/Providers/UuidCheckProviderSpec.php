<?php

namespace spec\Moccalotto\Valit\Providers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UuidCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\UuidCheckProvider');
        $this->shouldHaveType('Moccalotto\Valit\Contracts\CheckProvider');
    }

    function it_checks_uuid()
    {
        $this->provides()->shouldHaveKey('isUuid');
        $this->provides()->shouldHaveKey('uuid');

        $this->checkIsUuid('')->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkIsUuid('00000000-0000-0000-0000-000000000000')->success()->shouldBe(true);
        $this->checkIsUuid('581a8d5f-68b0-400f-a96f-b2c7534cff52')->success()->shouldBe(true);

        $this->checkIsUuid('11111111-1111-1111-1111-111111111111')->success()->shouldBe(false);
        $this->checkIsUuid('x56a4180-h5aa-42ec-a945-5fd21dec0538')->success()->shouldBe(false);
        $this->checkIsUuid('C56A4180-65AA-42EC-A945-5FD21DEC')->success()->shouldBe(false);
        $this->checkIsUuid(null)->success()->shouldBe(false);

        $this->checkIsUuid('')->success()->shouldBe(false);
    }
}
