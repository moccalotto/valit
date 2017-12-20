<?php

namespace Kahlan\Spec\Suite;

use Exception;
use Valit\Check;
use Valit\Ensure;
use Valit\Result\AssertionResult;
use Valit\Validators\ValueValidator;
use Valit\Exceptions\InvalidValueException;

describe('Ensure::oneOf', function () {
    describe('Executed on two ValueValidator objects', function () {

        // This scenario works if either price is zero or age is ≥ 18.
        $test = function ($age, $productType) {
            return Ensure::oneOf([
                Check::that($age)->as('age')->greaterThanOrEqual(18),
                Check::that($productType)->as('product type')->isString()->is('non-alcoholic'),
            ]);
        };

        it('1/2 successful scenarios does not result in exceptions', function () use ($test) {
            $result = $test(18, 'beer');
            expect($result)->toBeAnInstanceOf(ValueValidator::class);
            expect($result->success())->toBeTruthy();
        });

        it('0/2 successful scenarios result in InvalidValueException', function () use ($test) {
            try {
                $test(5, 'beer');
            } catch (InvalidValueException $e) {
                expect($e)->toBeAnInstanceOf(InvalidValueException::class);
                return;
            }
            throw new Exception('This should not happen!');
        });

        it('2/2 successful scenarios result in InvalidValueException', function () use ($test) {
            try {
                $test(18, 'non-alcoholic');
            } catch (Exception $e) {
                expect($e)->toBeAnInstanceOf(InvalidValueException::class);
                return;
            }
            throw new Exception('This should not happen!');
        });
    });

    it('Executed on successful logic instance', function () {
        $closure = function () {
            $number = 'ff';
            Ensure::that($number)->as('number')->passesLogic(Check::oneOf([
                'isInteger & gte(0) & lte(255)',
                'isHex & hasLength(2)',
            ]));
        };

        expect($closure)->not->toThrow();
    });

    it('Executed on failed logic instance', function () {
        $closure = function () {
            $number = 'bleh';
            Ensure::that($number)->as('number')->passesLogic(Check::oneOf([
                'isInteger & gte(0) & lte(255)',
                'isHex & hasLength(2)',
            ]));
        };

        expect($closure)->toThrow();
    });
});
