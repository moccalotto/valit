<?php

namespace Kahlan\Spec\Suite;

use Exception;
use Valit\Check;
use Valit\Ensure;
use Valit\Result\AssertionResult;
use Valit\Validators\ValueValidator;
use Valit\Exceptions\InvalidValueException;

describe('Ensure::allOf', function () {
    describe('Executed on two ValueValidator objects', function () {

        // This scenario works if either price is zero or age is ≥ 18.
        $test = function ($a, $b) {
            return Ensure::allOf([
                Check::that($a)->isTrue(),
                Check::that($b)->isTrue(),
                true,   // this check is always successful
            ]);
        };

        it('0/2 successful scenarios result in InvalidValueException', function () use ($test) {
            try {
                $test(false, false);
            } catch (InvalidValueException $e) {
                //
            }
            expect($e)->toBeAnInstanceOf(InvalidValueException::class);
        });

        it('1/2 successful scenarios result in InvalidValueException', function () use ($test) {
            try {
                $test(true, false);
            } catch (InvalidValueException $e) {
                //
            }
            expect($e)->toBeAnInstanceOf(InvalidValueException::class);
        });

        it('2/2 successful scenarios does not create an exception', function () use ($test) {
            $result = $test(true, true);
            expect($result)->toBeAnInstanceOf(ValueValidator::class);
            expect($result->success())->toBe(true);
        });
    });

    it('Can be executed as logic instance (success)', function () {
        $closure = function () {
            $number = 42;
            Ensure::that($number)->as('number')->passesLogic(Check::allOf([
                'isInteger',
                'gte(0)',
                'lte(255)'
            ]));
        };

        expect($closure)->not->toThrow();
    });

    it('Can be executed as logic instance (failure)', function () {
        $closure = function () {
            $number = 42;
            Ensure::that($number)->as('number')->passesLogic(Check::allOf([
                'isHex',
                'length(2)',
            ]));
        };

        expect($closure)->toThrow();
    });
});
