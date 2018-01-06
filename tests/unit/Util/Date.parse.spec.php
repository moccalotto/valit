<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Date;

describe('Valit\Util\Date', function () {

    describe('::parse()', function () {

        it('it parses integers as unix timestamps', function () {
            $result = Date::parse(strtotime('2018-01-01 00:00:00'));

            expect($result)->toBeAnInstanceOf('DateTimeInterface');

            expect($result->format('Y-m-d H:i:s'))->toBe('2018-01-01 00:00:00');

            expect(Date::parse(0)->format('Y-m-d H:i:s'))->toBe('1970-01-01 00:00:00');
        });

        it('it parses floats as unix timestamps', function () {
            expect(Date::parse(0.0))->toBeAnInstanceOf('DateTimeInterface');
            expect(Date::parse(0.0)->format('Y-m-d H:i:s.u'))->toBe('1970-01-01 00:00:00.000000');
            expect(Date::parse(0.123)->format('Y-m-d H:i:s.u'))->toBe('1970-01-01 00:00:00.123000');
            expect(Date::parse(0.123456)->format('Y-m-d H:i:s.u'))->toBe('1970-01-01 00:00:00.123456');
            expect(Date::parse(0.123456)->format('U.u'))->toBe('0.123456');
        });

        it('it parses strings as dates', function () {
            $result = Date::parse('2018-01-01 00:00:00.123000');
            expect($result)->toBeAnInstanceOf('DateTimeInterface');
            expect($result->format('Y-m-d H:i:s.u'))->toBe('2018-01-01 00:00:00.123000');
        });

        it('it parses strings as dates with a given format', function () {
            $result = Date::parse('01/01/18', 'd/m/y');
            expect($result)->toBeAnInstanceOf('DateTimeInterface');
            expect($result->format('Y-m-d'))->toBe('2018-01-01');
        });
    });
});
