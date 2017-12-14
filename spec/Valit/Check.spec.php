<?php

namespace Kahlan\Spec\Suite;

use Valit\Check;
use Valit\Logic;
use Valit\Manager;
use Valit\Template;
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
        });
    });

    describe('::not()', function () {
        it('creates a Not instance', function () {
            $scenario = 'isInt & isFloat & isString';
            expect(Check::not($scenario))->toBeAnInstanceOf(Logic\Not::class);
        });
    });

    // TODO: check __callStatic

    // TODO:  port these
    // function it_is_initializable()
    // {
    //     $this->shouldHaveType('Valit\Check');
    // }

    // function it_creates_single_value_validator()
    // {
    //     $this->that(42)->shouldHaveType('Valit\Validators\ValueValidator');
    // }

    // function it_creates_container_validator()
    // {
    //     $this->container([1,2,3])->shouldHaveType('Valit\Validators\ContainerValidator');
    // }
});
