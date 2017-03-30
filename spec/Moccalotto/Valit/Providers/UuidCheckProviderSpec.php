<?php

namespace spec\Moccalotto\Valit\Providers;

use PhpSpec\ObjectBehavior;

class UuidCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\UuidCheckProvider');
        $this->shouldHaveType('Moccalotto\Valit\Contracts\CheckProvider');
    }

    public function it_checks_uuid()
    {
        $this->provides()->shouldHaveKey('isUuid');
        $this->provides()->shouldHaveKey('uuid');

        $this->checkIsUuid('')->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkIsUuid('00000000-0000-1000-8000-000000000000')->success()->shouldBe(true);
        $this->checkIsUuid('00000000-0000-2000-9000-000000000000')->success()->shouldBe(true);
        $this->checkIsUuid('00000000-0000-3000-a000-000000000000')->success()->shouldBe(true);
        $this->checkIsUuid('00000000-0000-4000-b000-000000000000')->success()->shouldBe(true);
        $this->checkIsUuid('00000000-0000-5000-a000-000000000000')->success()->shouldBe(true);
        $this->checkIsUuid('581a8d5f-68b0-400f-a96f-b2c7534cff52')->success()->shouldBe(true);

        $this->checkisuuid('c56a4180-65aa-42ec-a945-5fd21dec')->success()->shouldbe(false);
        $this->checkIsUuid('11111111-1111-1111-1111-111111111111')->success()->shouldBe(false);
        $this->checkIsUuid('x56a4180-h5aa-42ec-a945-5fd21dec0538')->success()->shouldBe(false);
        $this->checkIsUuid('00000000-0000-1000-c000-000000000000')->success()->shouldBe(false);
        $this->checkIsUuid('00000000-0000-2000-d000-000000000000')->success()->shouldBe(false);
        $this->checkIsUuid('00000000-0000-3000-e000-000000000000')->success()->shouldBe(false);
        $this->checkIsUuid('00000000-0000-4000-f000-000000000000')->success()->shouldBe(false);
        $this->checkIsUuid('00000000-0000-5000-1000-000000000000')->success()->shouldBe(false);

        $this->checkIsUuid(1)->success()->shouldBe(false);
        $this->checkIsUuid('')->success()->shouldBe(false);
        $this->checkIsUuid([])->success()->shouldBe(false);
        $this->checkIsUuid(1.0)->success()->shouldBe(false);
        $this->checkIsUuid(null)->success()->shouldBe(false);
        $this->checkIsUuid('foo')->success()->shouldBe(false);
        $this->checkIsUuid((object) [])->success()->shouldBe(false);
        $this->checkIsUuid(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_uuidVersion()
    {
        $this->provides()->shouldHaveKey('isUuidVersion');
        $this->provides()->shouldHaveKey('uuidVersion');

        $this->checkUuidVersion('', 1)->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkUuidVersion('00000000-0000-1000-8000-000000000000', 1)->success()->shouldBe(true);
        $this->checkUuidVersion('00000000-0000-2000-8000-000000000000', 2)->success()->shouldBe(true);
        $this->checkUuidVersion('00000000-0000-3000-8000-000000000000', 3)->success()->shouldBe(true);
        $this->checkUuidVersion('00000000-0000-4000-8000-000000000000', 4)->success()->shouldBe(true);
        $this->checkUuidVersion('00000000-0000-5000-8000-000000000000', 5)->success()->shouldBe(true);

        $this->checkUuidVersion('00000000-0000-0000-0000-000000000000', 1)->success()->shouldBe(false);
        $this->checkUuidVersion('00000000-0000-0000-0000-000000000000', 2)->success()->shouldBe(false);
        $this->checkUuidVersion('00000000-0000-0000-0000-000000000000', 3)->success()->shouldBe(false);
        $this->checkUuidVersion('00000000-0000-0000-0000-000000000000', 4)->success()->shouldBe(false);
        $this->checkUuidVersion('00000000-0000-0000-0000-000000000000', 5)->success()->shouldBe(false);

        $this->checkIsUuid(1)->success()->shouldBe(false);
        $this->checkIsUuid('')->success()->shouldBe(false);
        $this->checkIsUuid([])->success()->shouldBe(false);
        $this->checkIsUuid(1.0)->success()->shouldBe(false);
        $this->checkIsUuid(null)->success()->shouldBe(false);
        $this->checkIsUuid('foo')->success()->shouldBe(false);
        $this->checkIsUuid((object) [])->success()->shouldBe(false);
        $this->checkIsUuid(curl_init())->success()->shouldBe(false);
    }
}
