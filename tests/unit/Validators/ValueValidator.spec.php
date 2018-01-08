<?php

namespace Kahlan\Spec\Suite;

use Valit\Check;
use Valit\Logic;
use Valit\Manager;
use Valit\Validators\ValueValidator;

describe('ValueValidator', function () {
    $subject = new ValueValidator(
        Manager::instance(),
        1234,
        false
    );

    describe('varName', function () use ($subject) {
        it('has a sane default value', function () use ($subject) {
            expect($subject->varName)->toBe('value');
        });

        it('can be set via as()', function () use ($subject) {
            $result = $subject->as('foo');
            expect($result)->toBe($subject);
            expect($subject->varName)->toBe('foo');
        });

        it('can be set via alias()', function () use ($subject) {
            $result = $subject->alias('foo');
            expect($result)->toBe($subject);
            expect($subject->varName)->toBe('foo');
        });
    });

    describe('__call', function () use ($subject) {
        it('executes checks', function () use ($subject) {
            $result = $subject->isInt();
            expect($result)->toBe($subject);
            expect($subject->results)->toContainKey(0);
            expect($subject->results[0])->toBeAnInstanceOf('Valit\Result\AssertionResult');
        });
        it('throws exception if check does not exist', function () use ($subject) {
            $closure = function () use ($subject) {
                $subject->nameOfCheckThatDoesNotExist();
            };
            expect($closure)->toThrow(new \BadMethodCallException);
        });
    });

    describe('throwExceptionIfNotSuccessful', function () use ($subject) {
        it('returns $this if no exception', function () use ($subject) {
            $result = $subject->throwExceptionIfNotSuccessful();
            expect($result)->toBe($subject);
        });

        it('throws InvalidvalueException if value is invald', function () use ($subject) {
            $closure = function () use ($subject) {
                $subject->throwExceptionIfNotSuccessful();
            };
            $subject->isString(); // the value 1234 is not a string, therefore the value is invalid.
            expect($closure)->toThrow(
                new \Valit\Exceptions\InvalidvalueException(
                    $subject->varName,
                    $subject->value,
                    $subject->results
                )
            );
        });
    });
});
