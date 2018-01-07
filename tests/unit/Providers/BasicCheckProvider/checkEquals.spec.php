<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;
use Valit\Providers\BasicCheckProvider;

describe('BasicCheckProvider', function () {
    $subject = new BasicCheckProvider();

    describe('checkEquals', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('is');
            expect($subject->provides())->toContainKey('equals');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkEquals(1, [1]);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('matches values that loose equal each other', function () use ($subject) {
            expect($subject->checkEquals(1, 1)->success())->toBe(true);
            expect($subject->checkEquals(1, 1.0)->success())->toBe(true);
            expect($subject->checkEquals(1, '1')->success())->toBe(true);
            expect($subject->checkEquals(1, true)->success())->toBe(true);
            expect($subject->checkEquals(new \Exception(), new \Exception())->success())->toBe(true);

            expect($subject->checkEquals(0, 0)->success())->toBe(true);
            expect($subject->checkEquals(0, 0.0)->success())->toBe(true);
            expect($subject->checkEquals(0, '0')->success())->toBe(true);
            expect($subject->checkEquals(0, false)->success())->toBe(true);

            expect($subject->checkEquals(true, true)->success())->toBe(true);
            expect($subject->checkEquals(true, 42)->success())->toBe(true);
            expect($subject->checkEquals(true, 42.0)->success())->toBe(true);
            expect($subject->checkEquals(true, 'foo')->success())->toBe(true);
            expect($subject->checkEquals(true, new \Exception())->success())->toBe(true);
            expect($subject->checkEquals(true, ['foo'])->success())->toBe(true);

            expect($subject->checkEquals(false, false)->success())->toBe(true);
            expect($subject->checkEquals(false, 0)->success())->toBe(true);
            expect($subject->checkEquals(false, '')->success())->toBe(true);
            expect($subject->checkEquals(false, null)->success())->toBe(true);
            expect($subject->checkEquals(false, [])->success())->toBe(true);
        });

        it('invalidates values that do not loosely equal $equals', function () use ($subject) {
            expect($subject->checkEquals(1, 0)->success())->toBe(false);
            expect($subject->checkEquals(1, 0.0)->success())->toBe(false);
            expect($subject->checkEquals(1, 'foo')->success())->toBe(false);
            expect($subject->checkEquals(1, 'true')->success())->toBe(false);
            expect($subject->checkEquals(1, false)->success())->toBe(false);
            expect($subject->checkEquals(new \Exception(), new \LogicException())->success())->toBe(false);

            expect($subject->checkEquals(0, 1)->success())->toBe(false);
            expect($subject->checkEquals(0, 1.0)->success())->toBe(false);
            expect($subject->checkEquals(0, '1')->success())->toBe(false);
            expect($subject->checkEquals(0, true)->success())->toBe(false);
            expect($subject->checkEquals(curl_init(), curl_init())->success())->toBe(false);
        });
    });
});
