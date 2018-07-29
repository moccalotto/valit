<?php

namespace Kahlan\Spec\Suite;

use Valit\Manager;
use Valit\Logic\AnyOf;
use Valit\Result\AssertionResult;
use Valit\Providers\LogicCheckProvider;

describe('LogicCheckProvider', function () {
    $subject = new LogicCheckProvider();

    describe('passesLogic', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('isSuccessfulLogic');
            expect($subject->provides())->toContainKey('passesLogic');
            expect($subject->provides())->toContainKey('logic');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkLogic(
                'foo',
                new AnyOf(Manager::instance(), [true])
            );
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('executes the logic', function () use ($subject) {
            $result = $subject->checkLogic(
                'foo',
                new AnyOf(Manager::instance(), [true])
            );
            expect($result->success())->toBe(true);

            $result = $subject->checkLogic(
                'foo',
                new AnyOf(Manager::instance(), [])
            );
            expect($result->success())->toBe(false);

            $result = $subject->checkLogic(
                'foo',
                new AnyOf(Manager::instance(), ['isInt', 'isNumeric'])
            );
            expect($result->success())->toBe(false);

            $result = $subject->checkLogic(
                'foo',
                new AnyOf(Manager::instance(), ['isInt', 'isString'])
            );
            expect($result->success())->toBe(true);
        });
    });
});
