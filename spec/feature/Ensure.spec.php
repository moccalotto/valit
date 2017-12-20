<?php

namespace Kahlan\Spec\Suite;

use Valit\Ensure;
use Valit\Logic;
use Valit\Manager;
use Valit\Assertion\Template;
use Valit\Validators\ValueValidator;
use Valit\Validators\ContainerValidator;
use Valit\Exceptions\InvalidValueException;

describe('Valit\Ensure', function () {
    describe('::that()', function () {
        it('creates ValueValidator', function () {
            $validator = Ensure::that(42);
            expect($validator)->toBeAnInstanceOf(ValueValidator::class);
            expect($validator->value())->toBe(42);
            expect($validator->throwOnFailure)->toBe(true);
        });
    });

    describe('::container()', function () {
        it('creates ContainerValidator', function () {
            $container = ['foo' => 'bar'];
            $validator = Ensure::container($container);

            expect($validator)->toBeAnInstanceOf(ContainerValidator::class);
            expect($validator->container)->toBe($container);
            expect($validator->throwOnFailure)->toBe(true);
            expect($validator->manager)->toBe(Manager::instance());
        });
    });

    describe('::oneOf', function () {
        it('creates and executes a oneOf logic', function () {

            $data = [
                'a' => 42,
                'b' => 'foo',
                'c' => '0x',
            ];

            $scenarios = [
                'a' => 'isNumeric',             // success because 42 is numeric
                'b' => 'containsString("0x")',  // fail because "foo" does not contain "0x"
            ];

            $logic = Ensure::oneOf($scenarios, $data);

            expect($logic)->toBeAnInstanceOf(ValueValidator::class);
            expect($logic->success())->toBe(true);
            expect(count($logic->results()))->toBe(1);
        });

        it('created logic will throw exception if not succesfull', function () {
            $tryCatch = function () {
                $data = [
                    'a' => 'not numeric',
                    'b' => 'foo',
                    'c' => '0x',
                ];

                $scenarios = [
                    'a' => 'isNumeric',             // success because 42 is numeric
                    'b' => 'containsString("0x")',  // fail because "foo" does not contain "0x"
                ];
                $logic = Ensure::oneOf($scenarios, $data);
            };


            expect($tryCatch)->toThrow();

            try {
                $tryCatch();
            } catch (\Exception $e) {
                expect($e)->toBeAnInstanceOf(InvalidValueException::class);
            }
        });
    });
});
