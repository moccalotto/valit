<?php

namespace Kahlan\Spec\Suite;

use DateTime;
use Valit\Util\Date;

describe('Valit\Util\Date', function () {

    it('existst', function () {
        expect(class_exists(Date::class))->toBe(true);
    });

    describe('::mockedCurrentTime()', function () {

        it('can mock the current time', function () {
            Date::mockCurrentTime(new DateTime('1987-01-01 00:00:00'));
            expect(
                Date::now()->format('Y-m-d H:i:s')
            )->toBe('1987-01-01 00:00:00');

            Date::mockCurrentTime(null);
        });
    });
});
