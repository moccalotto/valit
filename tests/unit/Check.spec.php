<?php

namespace Kahlan\Spec\Suite;

use Valit\Check;
use Valit\Logic;
use Valit\Manager;
use Valit\Assertion\AssertionBag;
use Valit\Validators\ValueValidator;
use Valit\Validators\ContainerValidator;

describe('Valit\Check', function () {
    describe('::that()', function () {
        it('creates ValueValidator', function () {
            $validator = Check::that(42);
            expect($validator)->toBeAnInstanceOf(ValueValidator::class);
            expect($validator->value())->toBe(42);
            expect($validator->throwOnFailure)->toBe(false);
        });
    });

    describe('::value()', function () {
        it('creates an assertion bag', function () {
            expect(Check::value())->toBeAnInstanceOf(AssertionBag::class);
        });
    });

    describe('::oneOf()', function () {
        it('creates a OneOf instance', function () {
            $scenarios = ['isInt', 'isFloat', 'isString'];
            expect(Check::oneOf($scenarios))->toBeAnInstanceOf(Logic\OneOf::class);
        });
    });

    describe('::allOf()', function () {
        it('creates a AllOf instance', function () {
            $scenarios = ['isInt', 'isFloat', 'isString'];
            expect(Check::allOf($scenarios))->toBeAnInstanceOf(Logic\AllOf::class);
        });
    });

    describe('::anyOf()', function () {
        it('creates a AnyOf instance', function () {
            $scenarios = ['isInt', 'isFloat', 'isString'];
            expect(Check::anyOf($scenarios))->toBeAnInstanceOf(Logic\AnyOf::class);
        });
    });

    describe('::noneOf()', function () {
        it('creates a NoneOf instance', function () {
            $scenarios = ['isInt', 'isFloat', 'isString'];
            expect(Check::noneOf($scenarios))->toBeAnInstanceOf(Logic\NoneOf::class);
            expect(Check::notAnyOf($scenarios))->toBeAnInstanceOf(Logic\NoneOf::class);
        });
    });

    describe('::not()', function () {
        it('creates a Not instance', function () {
            $scenario = 'isInt & isFloat & isString';
            expect(Check::not($scenario))->toBeAnInstanceOf(Logic\Not::class);
        });
    });

    describe('::ifThen()', function () {
        it('creates a Conditional instance', function () {
            expect(Check::ifThen(true, true))->toBeAnInstanceOf(Logic\Conditional::class);
        });

        it('works with simple scenarios', function () {
            expect(Check::ifThen(true, true)->success())->toBe(true);
            expect(Check::ifThen(true, false)->success())->toBe(false);
            expect(Check::ifThen(false, false)->success())->toBe(true);
            expect(Check::ifThen(false, true)->success())->toBe(true);
        });

        it('works with more complicated scenarios', function () {
            $check = Check::ifThen('isNumeric', 'hasLength(2)');

            expect($check->withValue('foo')->success())->toBe(true);
            expect($check->withValue('12')->success())->toBe(true);
            expect($check->withValue('12')->success())->toBe(true);
            expect($check->withValue(12)->success())->toBe(true);
            expect($check->withValue('.5')->success())->toBe(true);

            expect($check->withValue('123')->success())->toBe(false);
            expect($check->withValue(123)->success())->toBe(false);
        });
    });

    describe('::__callStatic()', function () {
        it('has magic method for creating assertion bags', function () {
            // proof of concept
            expect(Check::isInt())->toBeAnInstanceOf(AssertionBag::class);
            expect(Check::isInt()->count())->toBe(1);
            expect(Check::isInt()->assertions[0]->name)->toBe('isInt');

            // result when calling containsString as a method.
            expect(Check::containsString('foo'))->toBeAnInstanceOf(AssertionBag::class);
            expect(Check::containsString('foo')->count())->toBe(1);
            expect(Check::containsString('foo')->assertions[0]->name)->toBe('containsString');
            expect(Check::containsString('foo')->assertions[0]->args)->toBe(['foo']);

            // see same result when calling containsString via __callStatic
            expect(Check::__callStatic('containsString', ['foo']))->toBeAnInstanceOf(AssertionBag::class);
            expect(Check::__callStatic('containsString', ['foo'])->count())->toBe(1);
            expect(Check::__callStatic('containsString', ['foo'])->assertions[0]->name)->toBe('containsString');
            expect(Check::__callStatic('containsString', ['foo'])->assertions[0]->args)->toBe(['foo']);
        });
    });
});
