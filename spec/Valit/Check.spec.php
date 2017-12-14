<?php

namespace Kahlan\Spec\Suite;

use Valit\Check;
use Valit\Logic;
use Valit\Manager;
use Valit\Assertion\Template;
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

    describe('::container()', function () {
        it('creates ContainerValidator', function () {
            $container = ['foo' => 'bar'];
            $validator = Check::container($container);

            expect($validator)->toBeAnInstanceOf(ContainerValidator::class);
            expect($validator->container)->toBe($container);
            expect($validator->throwOnFailure)->toBe(false);
            expect($validator->manager)->toBe(Manager::instance());
        });
    });

    describe('::value()', function () {
        it('creates a Template', function () {
            expect(Check::value())->toBeAnInstanceOf(Template::class);
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

    describe('::__callStatic()', function () {
        it('has magic method for creating templates', function () {
            // proof of concept
            expect(Check::isInt())->toBeAnInstanceOf(Template::class);
            expect(Check::isInt()->assertions->count())->toBe(1);
            expect(Check::isInt()->assertions->assertions[0]->name)->toBe('isInt');

            // result when calling containsString as a method.
            expect(Check::containsString('foo'))->toBeAnInstanceOf(Template::class);
            expect(Check::containsString('foo')->assertions->count())->toBe(1);
            expect(Check::containsString('foo')->assertions->assertions[0]->name)->toBe('containsString');
            expect(Check::containsString('foo')->assertions->assertions[0]->args)->toBe(['foo']);

            // see same result when calling containsString via __callStatic
            expect(Check::__callStatic('containsString', ['foo']))->toBeAnInstanceOf(Template::class);
            expect(Check::__callStatic('containsString', ['foo'])->assertions->count())->toBe(1);
            expect(Check::__callStatic('containsString', ['foo'])->assertions->assertions[0]->name)->toBe('containsString');
            expect(Check::__callStatic('containsString', ['foo'])->assertions->assertions[0]->args)->toBe(['foo']);
        });
    });
});
