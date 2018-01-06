<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Date;

describe('Valit\Util\Date', function () {

    describe('::fromUnixTimestamp()', function () {
        it('handles integers', function () {
            $result = Date::fromUnixTimestamp(strtotime('2018-01-01 00:00:00'));

            expect($result)->toBeAnInstanceOf('DateTimeInterface');

            expect($result->format('Y-m-d H:i:s'))->toBe('2018-01-01 00:00:00');
        });

        it('handles negative integers', function () {
            $result = Date::fromUnixTimestamp(-1000);

            expect($result->format('U'))->toBe('-1000');
        });

        it('handles dates beyond 2038', function () {
            $timestamp = bcpow(2, 33);
            $result = Date::fromUnixTimestamp($timestamp);

            expect($result->format('U'))->toBe($timestamp);
            expect($result->format('U.u'))->toBe("$timestamp.000000");
        });

        it('handles floats', function () {
            $timestamp = pow(2.0, 33.0);

            $result = Date::fromUnixTimestamp($timestamp);

            expect($result->format('U'))->toBe((string) $timestamp);
            expect($result->format('U.u'))->toBe("$timestamp.000000");
        });

        it('handles floats with microsecond resolution', function () {
            $timestamp = pow(2.0, 33.0) + 0.123456;

            $result = Date::fromUnixTimestamp($timestamp);

            expect($result->format('U.u'))->toBe(bcadd($timestamp, 0, 6));
        });
    });
});
