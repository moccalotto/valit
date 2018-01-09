<?php

namespace Kahlan\Spec\Suite;

use Valit\Providers\UriCheckProvider;

describe('UriCheckProvider', function () {
    describe('basics', function () {
        it('exists', function () {
            expect(class_exists(UriCheckProvider::class))->toBe(true);
        });

        it('provides an array of checks', function () use ($subject) {
            $subject = new UriCheckProvider();

            expect($subject->provides())->toBeAn('array');
        });
    });

    describe('checkHostname', function () {
        $subject = new UriCheckProvider();

        it('accepts non-empty stringables', function () use ($subject) {

            expect($subject->checkHostname('foo')->success())->toBe(true);
            expect($subject->checkHostname('foo.bar')->success())->toBe(true);
            expect($subject->checkHostname(new \SimpleXmlElement('<r>foo.bar</r>'))->success())->toBe(true);

            expect($subject->checkHostname('æ')->success())->toBe(false);
            expect($subject->checkHostname([])->success())->toBe(false);
            expect($subject->checkHostname(new \stdClass)->success())->toBe(false);
            expect($subject->checkHostname(1.0)->success())->toBe(false);
            expect($subject->checkHostname(curl_init())->success())->toBe(false);
        });
    });

    describe('checkIpAddress', function () {
        $subject = new UriCheckProvider();
        /*
        expect($subject->checkIpAddress('127.0.0.1')->success())->shouldBe(true);
        expect($subject->checkIpAddress('1:2:3:4:5:6:7:8')->success())->shouldBe(true);
        expect($subject->checkIpAddress('1:2:3:4:5:6:7::')->success())->shouldBe(true);
        expect($subject->checkIpAddress('1:2:3:4:5:6::8')->success())->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5::7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4:5::8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4::6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3:4::8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3::5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2:3::8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2::4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1:2::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::')->success()->shouldBe(true);
        $this->checkIpAddress('1::3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::7:8')->success()->shouldBe(true);
        $this->checkIpAddress('1::8')->success()->shouldBe(true);
        $this->checkIpAddress('1::8')->success()->shouldBe(true);
        $this->checkIpAddress('2001:db8:3:4::192.0.2.33')->success()->shouldBe(true);
        $this->checkIpAddress('64:ff9b::192.0.2.33')->success()->shouldBe(true);
        $this->checkIpAddress('::')->success()->shouldBe(true);
        $this->checkIpAddress('::255.255.255.255')->success()->shouldBe(true);
        $this->checkIpAddress('::2:3:4:5:6:7:8')->success()->shouldBe(true);
        $this->checkIpAddress('::8')->success()->shouldBe(true);
        $this->checkIpAddress('::ffff:0:255.255.255.255')->success()->shouldBe(true);
        $this->checkIpAddress('::ffff:255.255.255.255')->success()->shouldBe(true);

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

        // Checks that do not always work, depending on the OS
        // $this->checkIpAddress('fe80::7:8%eth0')->success()->shouldBe(true);
        // $this->checkIpAddress('fe80::7:8%1')->success()->shouldBe(true);
         */
    });

    describe('checkUrl', function () {
        $subject = new UriCheckProvider();
    });
});
