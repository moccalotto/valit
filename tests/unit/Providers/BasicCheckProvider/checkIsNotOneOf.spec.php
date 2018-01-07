<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;
use Valit\Providers\BasicCheckProvider;

describe('BasicCheckProvider', function () {
    $subject = new BasicCheckProvider();

    describe('checkIsNotOneOf', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('isNotOneOf');
            expect($subject->provides())->toContainKey('notOneOf');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkIsNotOneOf(1, [1]);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('matches arrays that do not loosely contain $value', function () use ($subject) {
            expect($subject->checkIsNotOneOf(1, [2, 3])->success())->toBe(false);
            expect($subject->checkIsNotOneOf(1, 2, 3)->success())->toBe(false);
            expect($subject->checkIsNotOneOf(1, [2])->success())->toBe(false);

            expect($subject->checkIsNotOneOf(null, [true, 2])->success())->toBe(false);
            expect($subject->checkIsNotOneOf(null, true, 2)->success())->toBe(false);
            expect($subject->checkIsNotOneOf(curl_init(), [curl_init()])->success())->toBe(false);
            expect($subject->checkIsNotOneOf(curl_init(), curl_init())->success())->toBe(false);
            expect($subject->checkIsNotOneOf(new \LogicException(), new \Exception())->success())->toBe(false);
            expect($subject->checkIsNotOneOf([1, 3, 2], [1, 2, 3])->success())->toBe(false);
        });

        it('invalidates arrays that loosely contain $value', function () use ($subject) {
            $obj1 = $obj2 = new \stdClass();
            $obj3 = &$obj1;
            $obj4 = &$obj2;
            $clone = clone $obj1;

            expect($subject->checkIsNotOneOf(1, 1)->success())->toBe(true);
            expect($subject->checkIsNotOneOf(1, [1])->success())->toBe(true);
            expect($subject->checkIsNotOneOf(1, 1, 'foo')->success())->toBe(true);
            expect($subject->checkIsNotOneOf(1, [1, 'foo'])->success())->toBe(true);
            expect($subject->checkIsNotOneOf(1, ['foo', 1])->success())->toBe(true);
            expect($subject->checkIsNotOneOf(1, 'foo', 1)->success())->toBe(true);
            expect($subject->checkIsNotOneOf($obj1, [$obj1, 'foo'])->success())->toBe(true);
            expect($subject->checkIsNotOneOf($obj1, ['foo', $obj2])->success())->toBe(true);
            expect($subject->checkIsNotOneOf($obj3, $obj4, 'foo')->success())->toBe(true);
            expect($subject->checkIsNotOneOf($obj1, $clone, 'foo')->success())->toBe(true);
            expect($subject->checkIsNotOneOf($obj1, [$clone, 'foo'])->success())->toBe(true);
            expect($subject->checkIsNotOneOf(true, [true])->success())->toBe(true);
            expect($subject->checkIsNotOneOf(null, [null])->success())->toBe(true);
            expect($subject->checkIsNotOneOf(new \Exception(), [new \Exception()])->success())->toBe(true);
            expect($subject->checkIsNotOneOf([], [[], 'foo'])->success())->toBe(true);
            expect($subject->checkIsNotOneOf([1, 2, 3], [[1, 2, 3]])->success())->toBe(true);
        });
    });
});
