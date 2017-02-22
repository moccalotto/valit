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

use Exception;
use PhpSpec\ObjectBehavior;

class StringCheckProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Moccalotto\Valit\Providers\StringCheckProvider');
    }

    public function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    public function it_checks_decimalString()
    {
        $this->checkDecimalString('0')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('decimalString');
        $this->provides()->shouldHaveKey('isDecimalString');

        $this->checkDecimalString('1')->success()->shouldBe(true);
        $this->checkDecimalString('12345678')->success()->shouldBe(true);

        $this->checkDecimalString(1)->success()->shouldBe(false);
        $this->checkDecimalString(-1)->success()->shouldBe(false);
        $this->checkDecimalString('-1')->success()->shouldBe(false);
        $this->checkDecimalString('-12345678')->success()->shouldBe(false);

        $this->checkDecimalString('1a')->success()->shouldBe(false);
        $this->checkDecimalString('0x1f')->success()->shouldBe(false);

        $this->checkDecimalString(NAN)->success()->shouldBe(false);
        $this->checkDecimalString(INF)->success()->shouldBe(false);
        $this->checkDecimalString(null)->success()->shouldBe(false);
        $this->checkDecimalString([])->success()->shouldBe(false);
        $this->checkDecimalString((object) [])->success()->shouldBe(false);
        $this->checkDecimalString(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_hexString()
    {
        $this->checkHexString(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isHex');
        $this->provides()->shouldHaveKey('hexString');
        $this->provides()->shouldHaveKey('isHexString');

        $this->checkHexString('0')->success()->shouldBe(true);
        $this->checkHexString('44f')->success()->shouldBe(true);

        $this->checkHexString(0)->success()->shouldBe(false);
        $this->checkHexString(0x4f)->success()->shouldBe(false);
        $this->checkHexString('0x44f')->success()->shouldBe(false);
        $this->checkHexString('-44f')->success()->shouldBe(false);
        $this->checkHexString('044h')->success()->shouldBe(false);

        $this->checkHexString(NAN)->success()->shouldBe(false);
        $this->checkHexString(INF)->success()->shouldBe(false);
        $this->checkHexString(null)->success()->shouldBe(false);
        $this->checkHexString([])->success()->shouldBe(false);
        $this->checkHexString((object) [])->success()->shouldBe(false);
        $this->checkHexString(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_emailAddress()
    {
        $this->checkEmail(0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('isEmail');
        $this->provides()->shouldHaveKey('emailAddress');
        $this->provides()->shouldHaveKey('isEmailAddress');

        $this->checkEmail('foo@bar.org')->success()->shouldBe(true);
        $this->checkEmail('foo.bar@baz.org')->success()->shouldBe(true);
        $this->checkEmail('foo.bar+ding@dong-baz.some.tld')->success()->shouldBe(true);
        $this->checkEmail('1+2=3@dong-baz.some.tld')->success()->shouldBe(true);
        $this->checkEmail('foo_bar@ding.com')->success()->shouldBe(true);

        $this->checkEmail('')->success()->shouldBe(false);
        $this->checkEmail('föö@bar.org')->success()->shouldBe(false);
        $this->checkEmail('1234foobar.org')->success()->shouldBe(false);
        $this->checkEmail('foo_bar@under_score.com')->success()->shouldBe(false);

        $this->checkEmail(NAN)->success()->shouldBe(false);
        $this->checkEmail(INF)->success()->shouldBe(false);
        $this->checkEmail(null)->success()->shouldBe(false);
        $this->checkEmail([])->success()->shouldBe(false);
        $this->checkEmail((object) [])->success()->shouldBe(false);
        $this->checkEmail(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_uppercase()
    {
        $this->checkUppercase('')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('uppercase');
        $this->provides()->shouldHaveKey('isUppercase');

        foreach (range('A', 'Z') as $char) {
            $this->checkUppercase($char)->success()->shouldBe(true);
        }
        $this->checkUppercase('ABCDEFGHIJKLMNOPQRSTUVWXYZ')->success()->shouldBe(true);

        foreach (range('a', 'z') as $char) {
            $this->checkUppercase($char)->success()->shouldBe(false);
        }
        $this->checkUppercase('Æ')->success()->shouldBe(false);
        $this->checkUppercase('Ø')->success()->shouldBe(false);
        $this->checkUppercase('Å')->success()->shouldBe(false);

        $this->checkUppercase(NAN)->success()->shouldBe(false);
        $this->checkUppercase(INF)->success()->shouldBe(false);
        $this->checkUppercase(null)->success()->shouldBe(false);
        $this->checkUppercase([])->success()->shouldBe(false);
        $this->checkUppercase((object) [])->success()->shouldBe(false);
        $this->checkUppercase(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_lowercase()
    {
        $this->checkLowercase('')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('lowercase');
        $this->provides()->shouldHaveKey('isLowercase');

        foreach (range('a', 'a') as $char) {
            $this->checkLowercase($char)->success()->shouldBe(true);
        }
        $this->checkLowercase('abcdefghijklmnopqrstuvwxyz')->success()->shouldBe(true);

        foreach (range('A', 'Z') as $char) {
            $this->checkLowercase($char)->success()->shouldBe(false);
        }
        $this->checkLowercase('æ')->success()->shouldBe(false);
        $this->checkLowercase('ø')->success()->shouldBe(false);
        $this->checkLowercase('å')->success()->shouldBe(false);

        $this->checkLowercase(NAN)->success()->shouldBe(false);
        $this->checkLowercase(INF)->success()->shouldBe(false);
        $this->checkLowercase(null)->success()->shouldBe(false);
        $this->checkLowercase([])->success()->shouldBe(false);
        $this->checkLowercase((object) [])->success()->shouldBe(false);
        $this->checkLowercase(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_matchesRegex()
    {
        $this->checkMatchesRegex('', '//')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('matches');
        $this->provides()->shouldHaveKey('matchesRegex');

        $this->checkMatchesRegex('', '//')->success()->shouldBe(true);
        $this->checkMatchesRegex('test', '/test/')->success()->shouldBe(true);
        $this->checkMatchesRegex('test', '/[te]{2}[st]{2}/A')->success()->shouldBe(true);
        $this->checkMatchesRegex('test', '//')->success()->shouldBe(true);

        $this->checkMatchesRegex('TEST', '/test/A')->success()->shouldBe(false);
        $this->checkMatchesRegex('TEST', '/A')->success()->shouldBe(false);

        $this->checkMatchesRegex(NAN, '//')->success()->shouldBe(false);
        $this->checkMatchesRegex(INF, '//')->success()->shouldBe(false);
        $this->checkMatchesRegex(null, '//')->success()->shouldBe(false);
        $this->checkMatchesRegex([], '//')->success()->shouldBe(false);
        $this->checkMatchesRegex((object) [], '//')->success()->shouldBe(false);
        $this->checkMatchesRegex(curl_init(), '//')->success()->shouldBe(false);
    }

    public function it_checks_stringable()
    {
        $this->checkStringable('')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('stringable');
        $this->provides()->shouldHaveKey('isStringable');
        $this->provides()->shouldHaveKey('stringCastable');
        $this->provides()->shouldHaveKey('isStringCastable');

        $this->checkStringable('string')->success()->shouldBe(true);
        $this->checkStringable(666)->success()->shouldBe(true);
        $this->checkStringable(123.456)->success()->shouldBe(true);
        $this->checkStringable(0)->success()->shouldBe(true);
        $this->checkStringable(0)->success()->shouldBe(true);
        $this->checkStringable(123.456)->success()->shouldBe(true);
        $this->checkStringable(NAN)->success()->shouldBe(true);
        $this->checkStringable(INF)->success()->shouldBe(true);
        $this->checkStringable(new Exception('this class has __toString'))->success()->shouldBe(true);

        $this->checkStringable(null)->success()->shouldBe(false);
        $this->checkStringable([])->success()->shouldBe(false);
        $this->checkStringable((object) [])->success()->shouldBe(false);
        $this->checkStringable(curl_init())->success()->shouldBe(false);
    }

    public function it_checks_startsWith()
    {
        $this->checkStartsWith('', '')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('startsWith');
        $this->provides()->shouldHaveKey('beginsWith');

        $this->checkStartsWith('', '')->success()->shouldBe(true);
        $this->checkStartsWith('test example', 'test')->success()->shouldBe(true);
        $this->checkStartsWith(NAN, 'NAN')->success()->shouldBe(true);
        $this->checkStartsWith(INF, 'INF')->success()->shouldBe(true);

        $this->checkStartsWith('', 'foo')->success()->shouldBe(false);
        $this->checkStartsWith('foo', 'bar')->success()->shouldBe(false);
        $this->checkStartsWith('start end', 'end')->success()->shouldBe(false);
        $this->checkStartsWith('ab', 'b')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkStartsWith', ['', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkStartsWith', ['', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkStartsWith', ['', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkStartsWith', ['', curl_init()]);

        $this->checkStartsWith(null, '')->success()->shouldBe(false);
        $this->checkStartsWith([], '')->success()->shouldBe(false);
        $this->checkStartsWith((object) [], '')->success()->shouldBe(false);
        $this->checkStartsWith(curl_init(), '')->success()->shouldBe(false);
    }

    public function it_checks_endsWith()
    {
        $this->checkEndsWith('', '')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('endsWith');

        $this->checkEndsWith('', '')->success()->shouldBe(true);
        $this->checkEndsWith('test example', 'example')->success()->shouldBe(true);
        $this->checkEndsWith('start end', 'end')->success()->shouldBe(true);
        $this->checkEndsWith(NAN, 'NAN')->success()->shouldBe(true);
        $this->checkEndsWith(INF, 'INF')->success()->shouldBe(true);

        $this->checkEndsWith('', 'foo')->success()->shouldBe(false);
        $this->checkEndsWith('foo', 'bar')->success()->shouldBe(false);
        $this->checkEndsWith('ab', 'a')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkEndsWith', ['', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkEndsWith', ['', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkEndsWith', ['', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkEndsWith', ['', curl_init()]);

        $this->checkEndsWith(null, '')->success()->shouldBe(false);
        $this->checkEndsWith([], '')->success()->shouldBe(false);
        $this->checkEndsWith((object) [], '')->success()->shouldBe(false);
        $this->checkEndsWith(curl_init(), '')->success()->shouldBe(false);
    }

    public function it_checks_containsString()
    {
        $this->checkContainsString('', '')->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('containsString');
        $this->provides()->shouldHaveKey('containsTheString');

        $this->checkContainsString('foobar', 'ooba')->success()->shouldBe(true);
        $this->checkContainsString('some long sentence', 'some')->success()->shouldBe(true);
        $this->checkContainsString('some long sentence', 'long')->success()->shouldBe(true);
        $this->checkContainsString('some long sentence', 'sentence')->success()->shouldBe(true);
        $this->checkContainsString('some long sentence', ' ')->success()->shouldBe(true);

        $this->checkContainsString('foobar', 'baz')->success()->shouldBe(false);
        $this->checkContainsString('foobar', 'föo')->success()->shouldBe(false);
        $this->checkContainsString('some long sentence', 'short')->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkContainsString', ['', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkContainsString', ['', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkContainsString', ['', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkContainsString', ['', curl_init()]);

        $this->checkContainsString(null, '')->success()->shouldBe(false);
        $this->checkContainsString([], '')->success()->shouldBe(false);
        $this->checkContainsString((object) [], '')->success()->shouldBe(false);
        $this->checkContainsString(curl_init(), '')->success()->shouldBe(false);
    }

    public function it_checks_shorterThan()
    {
        $this->checkShorterThan('', 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('shorterThan');
        $this->provides()->shouldHaveKey('isShorterThan');
        $this->provides()->shouldHaveKey('stringShorterThan');

        $this->checkShorterThan('kkk æøå', 10)->success()->shouldBe(true);
        $this->checkShorterThan('kkk æøå', 8)->success()->shouldBe(true);
        $this->checkShorterThan('kkk æøå', 7)->success()->shouldBe(false);
        $this->checkShorterThan('kkk æøå', 1)->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkShorterThan', ['', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkShorterThan', ['', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkShorterThan', ['', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkShorterThan', ['', curl_init()]);

        $this->checkShorterThan(null, 100)->success()->shouldBe(false);
        $this->checkShorterThan([], 100)->success()->shouldBe(false);
        $this->checkShorterThan((object) [], 100)->success()->shouldBe(false);
        $this->checkShorterThan(curl_init(), 100)->success()->shouldBe(false);
    }

    public function it_checks_longerThan()
    {
        $this->checkLongerThan('', 0)->shouldHaveType('Moccalotto\Valit\Result');

        $this->provides()->shouldHaveKey('longerThan');
        $this->provides()->shouldHaveKey('isLongerThan');
        $this->provides()->shouldHaveKey('stringLongerThan');

        $this->checkLongerThan('kkk æøå', 1)->success()->shouldBe(true);
        $this->checkLongerThan('kkk æøå', 6)->success()->shouldBe(true);
        $this->checkLongerThan('kkk æøå', 7)->success()->shouldBe(false);
        $this->checkLongerThan('kkk æøå', 10)->success()->shouldBe(false);

        $this->shouldThrow('InvalidArgumentException')->during('checkLongerThan', ['', null]);
        $this->shouldThrow('InvalidArgumentException')->during('checkLongerThan', ['', []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkLongerThan', ['', (object) []]);
        $this->shouldThrow('InvalidArgumentException')->during('checkLongerThan', ['', curl_init()]);

        $this->checkLongerThan(null, 100)->success()->shouldBe(false);
        $this->checkLongerThan([], 100)->success()->shouldBe(false);
        $this->checkLongerThan((object) [], 100)->success()->shouldBe(false);
        $this->checkLongerThan(curl_init(), 100)->success()->shouldBe(false);
    }
}
