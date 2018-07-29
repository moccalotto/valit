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

    describe('::comparison()', function () {

        it('less than', function () {

            expect(
                Date::comparison('<', '2000-01-01 00:00:00', '2000-01-01 00:00:01')
            )->toBe(true);

            expect(
                Date::comparison('<', '2000-01-01 00:00:00', '2000-01-01 00:00:00')
            )->toBe(false);

            expect(
                Date::comparison('<', '2000-01-01 00:00:01', '2000-01-01 00:00:00')
            )->toBe(false);
        });

        it('less than or equal', function () {

            expect(
                Date::comparison('<=', '2000-01-01 00:00:00', '2000-01-01 00:00:01')
            )->toBe(true);

            expect(
                Date::comparison('<=', '2000-01-01 00:00:00', '2000-01-01 00:00:00')
            )->toBe(true);

            expect(
                Date::comparison('<=', '2000-01-01 00:00:01', '2000-01-01 00:00:00')
            )->toBe(false);
        });

        it('greater than', function () {

            expect(
                Date::comparison('>', '2000-01-01 00:00:01', '2000-01-01 00:00:00')
            )->toBe(true);

            expect(
                Date::comparison('>', '2000-01-01 00:00:00', '2000-01-01 00:00:00')
            )->toBe(false);

            expect(
                Date::comparison('>', '2000-01-01 00:00:00', '2000-01-01 00:00:01')
            )->toBe(false);
        });

        it('greater than or equal', function () {

            expect(
                Date::comparison('>=', '2000-01-01 00:00:01', '2000-01-01 00:00:00')
            )->toBe(true);

            expect(
                Date::comparison('>=', '2000-01-01 00:00:00', '2000-01-01 00:00:00')
            )->toBe(true);

            expect(
                Date::comparison('>=', '2000-01-01 00:00:00', '2000-01-01 00:00:01')
            )->toBe(false);
        });

        it('equal', function () {

            expect(
                Date::comparison('=', '2000-01-01 00:00:00', '2000-01-01 00:00:00')
            )->toBe(true);

            expect(
                Date::comparison('=', '2000-01-01 00:00:01', '2000-01-01 00:00:00')
            )->toBe(false);

            expect(
                Date::comparison('=', '2000-01-01 00:00:00', '2000-01-01 00:00:01')
            )->toBe(false);
        });

        it('throws if given an invalid operator', function () {

            expect(function () {
                Date::comparison('===', '00:00:00', '00:00:00');
            })->toThrow(new \InvalidArgumentException());
        });

        it('does not throw if given a valid operator and valid dates', function () {

            expect(function () {
                Date::comparison('=', '00:00:00', '00:00:00');
                Date::comparison('>', '00:00:00', '00:00:00');
                Date::comparison('<', '00:00:00', '00:00:00');
                Date::comparison('>=', '00:00:00', '00:00:00');
                Date::comparison('<=', '00:00:00', '00:00:00');
                Date::comparison('≥', '00:00:00', '00:00:00');
                Date::comparison('≤', '00:00:00', '00:00:00');
            })->not->toThrow();
        });
    });
});
