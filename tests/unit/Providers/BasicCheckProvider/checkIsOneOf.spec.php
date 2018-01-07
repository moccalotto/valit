<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;
use Valit\Providers\BasicCheckProvider;

describe('BasicCheckProvider', function () {
    $subject = new BasicCheckProvider();

    describe('checkIsOneOf', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('isOneOf');
            expect($subject->provides())->toContainKey('oneOf');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkIsOneOf(1, [1]);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('matches arrays that loosely contain $value', function () use ($subject) {
            $obj1 = $obj2 = new \stdClass();
            $obj3 = &$obj1;
            $obj4 = &$obj2;
            $clone = clone $obj1;

            expect($subject->checkIsOneOf(1, 1)->success())->toBe(true);
            expect($subject->checkIsOneOf(1, [1])->success())->toBe(true);
            expect($subject->checkIsOneOf(1, 1, 'foo')->success())->toBe(true);
            expect($subject->checkIsOneOf(1, [1, 'foo'])->success())->toBe(true);
            expect($subject->checkIsOneOf(1, ['foo', 1])->success())->toBe(true);
            expect($subject->checkIsOneOf(1, 'foo', 1)->success())->toBe(true);
            expect($subject->checkIsOneOf($obj1, [$obj1, 'foo'])->success())->toBe(true);
            expect($subject->checkIsOneOf($obj1, ['foo', $obj2])->success())->toBe(true);
            expect($subject->checkIsOneOf($obj3, $obj4, 'foo')->success())->toBe(true);
            expect($subject->checkIsOneOf($obj1, $clone, 'foo')->success())->toBe(true);
            expect($subject->checkIsOneOf($obj1, [$clone, 'foo'])->success())->toBe(true);
            expect($subject->checkIsOneOf(true, [true])->success())->toBe(true);
            expect($subject->checkIsOneOf(null, [null])->success())->toBe(true);
            expect($subject->checkIsOneOf(new \Exception(), [new \Exception()])->success())->toBe(true);
            expect($subject->checkIsOneOf([], [[], 'foo'])->success())->toBe(true);
            expect($subject->checkIsOneOf([1, 2, 3], [[1, 2, 3]])->success())->toBe(true);
        });

        it('invalidates arrays that do not loosely contain $value', function () use ($subject) {
            expect($subject->checkIsOneOf(1, [2])->success())->toBe(false);
            expect($subject->checkIsOneOf(null, [true])->success())->toBe(false);
            expect($subject->checkIsOneOf(null, true)->success())->toBe(false);
            expect($subject->checkIsOneOf(curl_init(), [curl_init()])->success())->toBe(false);
            expect($subject->checkIsOneOf(curl_init(), curl_init())->success())->toBe(false);
            expect($subject->checkIsOneOf(new \LogicException(), new \Exception())->success())->toBe(false);
            expect($subject->checkIsOneOf([1, 3, 2], [1, 2, 3])->success())->toBe(false);
        });
    });
});
