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

namespace spec\Moccalotto\Valit\Providers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UriCheckProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\UriCheckProvider');
    }

    function it_checks_hostname()
    {
        $this->provides()->shouldHaveKey('hostname');
        $this->provides()->shouldHaveKey('isHostname');

        $this->checkHostname('foo.bar')->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkHostname('foo.bar')->success()->shouldBe(true);
        $this->checkHostname('example.com')->success()->shouldBe(true);

        $this->checkHostname('æ')->success()->shouldBe(false);

        $this->checkHostname(1)->success()->shouldBe(false);
        $this->checkHostname('')->success()->shouldBe(false);
        $this->checkHostname([])->success()->shouldBe(false);
        $this->checkHostname(1.0)->success()->shouldBe(false);
        $this->checkHostname(null)->success()->shouldBe(false);
        $this->checkHostname((object) [])->success()->shouldBe(false);
        $this->checkHostname(curl_init())->success()->shouldBe(false);
    }

    function it_checks_ip_address()
    {
        $this->provides()->shouldHaveKey('ipAddress');
        $this->provides()->shouldHaveKey('isIpAdrress');

        $this->checkIpAddress('127.0.0.1')->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkIpAddress('127.0.0.1')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5:6:7::')->success()->shouldBe(true);
        $this->checkIpAddress('1::8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5:6::8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5:6::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5::7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4::6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3::5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2::4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::8')->success()->shouldBe(true);
        $this->checkIpAddress('::2:3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('::2:3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('::8')->success()->shouldBe(true);
        $this->checkIpAddress('::')->success()->shouldBe(true);
        $this->checkIpAddress('fe80::7:8%eth0')->success()->shouldBe(true);
        $this->checkIpAddress('fe80::7:8%1')->success()->shouldBe(true);
        $this->checkIpAddress('::255.255.255.255')->success()->shouldBe(true);
        $this->checkIpAddress('::ffff:255.255.255.255')->success()->shouldBe(true);
        $this->checkIpAddress('::ffff:0:255.255.255.255')->success()->shouldBe(true);
        $this->checkIpAddress('2001:db8:3:4::192.0.2.33')->success()->shouldBe(true);
        $this->checkIpAddress('64:ff9b::192.0.2.33')->success()->shouldBe(true);

        $this->checkIpAddress('256.256.256.256')->success()->shouldBe(false);
        $this->checkIpAddress('::x')->success()->shouldBe(false);

        $this->checkIpAddress(1)->success()->shouldBe(false);
        $this->checkIpAddress('')->success()->shouldBe(false);
        $this->checkIpAddress([])->success()->shouldBe(false);
        $this->checkIpAddress(1.0)->success()->shouldBe(false);
        $this->checkIpAddress(null)->success()->shouldBe(false);
        $this->checkIpAddress('foo')->success()->shouldBe(false);
        $this->checkIpAddress((object) [])->success()->shouldBe(false);
        $this->checkIpAddress(curl_init())->success()->shouldBe(false);
    }

    function it_checks_web_url()
    {
        $this->provides()->shouldHaveKey('url');
        $this->provides()->shouldHaveKey('isUrl');

        $this->checkUrl('https://foo.bar')->shouldHaveType('Moccalotto\Valit\Result');

        $this->checkUrl('http://foo.bar')->success()->shouldBe(true);
        $this->checkUrl('https://foo.bar')->success()->shouldBe(true);
        $this->checkUrl('https://127.0.0.1')->success()->shouldBe(true);
        $this->checkUrl('https://::1')->success()->shouldBe(true);
        $this->checkUrl('http://foo.bar:80')->success()->shouldBe(true);
        $this->checkUrl('http://foo.bar:65535')->success()->shouldBe(true);
        $this->checkUrl('https://::1:65535')->success()->shouldBe(true);

        $this->checkUrl('ftp://foo.bar')->success()->shouldBe(false);
        $this->checkUrl('http://foo.bar:65536')->success()->shouldBe(false);
        $this->checkUrl('http://foo.bar:xxxxx')->success()->shouldBe(false);

        $this->checkUrl(1)->success()->shouldBe(false);
        $this->checkUrl('')->success()->shouldBe(false);
        $this->checkUrl([])->success()->shouldBe(false);
        $this->checkUrl(1.0)->success()->shouldBe(false);
        $this->checkUrl(null)->success()->shouldBe(false);
        $this->checkUrl('foo')->success()->shouldBe(false);
        $this->checkUrl((object) [])->success()->shouldBe(false);
        $this->checkUrl(curl_init())->success()->shouldBe(false);
    }
}
