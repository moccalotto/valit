<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Date;

describe('Valit\Util\Date', function () {

    describe('::compare()', function () {

        it('compares two string dates', function () {
            $result = Date::compare(
                '2000-01-01 00:00:00',
                '2000-01-01 00:00:01'
            );

            expect($result)->toBeLessThan(0);

            $result = Date::compare(
                '2000-01-01 00:00:01',
                '2000-01-01 00:00:00'
            );

            expect($result)->toBeGreaterThan(0);

            $result = Date::compare(
                '2000-01-01 00:00:00',
                '2000-01-01 00:00:00'
            );

            expect($result)->toBe(0.0);
        });
    });
});
