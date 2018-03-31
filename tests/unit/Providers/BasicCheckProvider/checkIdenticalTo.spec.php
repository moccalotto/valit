<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;
use Valit\Providers\BasicCheckProvider;

describe('BasicCheckProvider', function () {
    $subject = new BasicCheckProvider();

    describe('checkIdenticalTo', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('isIdenticalTo');
            expect($subject->provides())->toContainKey('identicalTo');
            expect($subject->provides())->toContainKey('isSameAs');
            expect($subject->provides())->toContainKey('sameAs');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkIdenticalTo(1, 1);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('matches identical entities', function () use ($subject) {
            $obj1 = $obj2 = new \stdClass();
            $obj3 = &$obj1;
            $obj4 = &$obj2;

            expect($subject->checkIdenticalTo(1, 1)->success())->toBe(true);
            expect($subject->checkIdenticalTo(1.0, 1.0)->success())->toBe(true);
            expect($subject->checkIdenticalTo($obj1, $obj1)->success())->toBe(true);
            expect($subject->checkIdenticalTo($obj1, $obj2)->success())->toBe(true);
            expect($subject->checkIdenticalTo($obj3, $obj4)->success())->toBe(true);
            expect($subject->checkIdenticalTo(true, true)->success())->toBe(true);
            expect($subject->checkIdenticalTo(null, null)->success())->toBe(true);
            expect($subject->checkIdenticalTo([], [])->success())->toBe(true);
            expect($subject->checkIdenticalTo([1, 2, 3], [1, 2, 3])->success())->toBe(true);
        });
        it('invalidates non-identical entities', function () use ($subject) {
            $obj1 = new \stdClass;
            $obj2 = clone $obj1;

            expect($subject->checkIdenticalTo(1, 1.0)->success())->toBe(false);
            expect($subject->checkIdenticalTo(1, 2)->success())->toBe(false);
            expect($subject->checkIdenticalTo(1, '1')->success())->toBe(false);
            expect($subject->checkIdenticalTo($obj1, $obj2)->success())->toBe(false);
            expect($subject->checkIdenticalTo(null, false)->success())->toBe(false);
            expect($subject->checkIdenticalTo(curl_init(), curl_init())->success())->toBe(false);
            expect($subject->checkIdenticalTo([1, 3, 2], [1, 2, 3])->success())->toBe(false);
        });
    });
});
