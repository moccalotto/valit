<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;

describe('StringCheckProvider', function () {
    $subject = new \Valit\Providers\StringCheckProvider();

    describe('checkLengthInRange', function () use ($subject) {

        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('lengthBetween');
            expect($subject->provides())->toContainKey('lengthInRange');
            expect($subject->provides())->toContainKey('lengthMinMax');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkLengthInRange('foo', 0, 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exception if $min is not int', function () use ($subject) {
            $closure = function () use ($subject) {
                $subject->checkLengthInRange('foo', 'not an int', 1337);
            };
        });

        it('throws exception if $max is not int', function () use ($subject) {
            $closure = function () use ($subject) {
                $subject->checkLengthInRange('foo', 19, 'not an int');
            };

            expect($closure)->toThrow(new \InvalidArgumentException());
        });

        it('throws exception if $min > $max', function () use ($subject) {
            $closure = function () use ($subject) {
                $subject->checkLengthInRange('foo', 19, 0);
            };

            expect($closure)->toThrow(new \InvalidArgumentException());
        });

        it('validates strings where length == $min', function () use ($subject) {
            expect(
                $subject->checkLengthInRange('foo', 3, 100)->success()
            )
            ->toBe(true);
        });

        it('validates strings where length == $max', function () use ($subject) {
            expect(
                $subject->checkLengthInRange('foo', 0, 3)->success()
            )
            ->toBe(true);
        });

        it('validates strings where length between $min and $max', function () use ($subject) {
            expect(
                $subject->checkLengthInRange('foo', 0, 100)->success()
            )
            ->toBe(true);
        });
    });
});
