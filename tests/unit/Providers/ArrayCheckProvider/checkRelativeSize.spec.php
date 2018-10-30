<?php

use Valit\Result\AssertionResult;

describe('ArrayCheckProvider', function () {
    $subject = new \Valit\Providers\ArrayCheckProvider();

    describe('checkRelativeCount()', function () use ($subject) {

        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('arrayWhereCount');
            expect($subject->provides())->toContainKey('isArrayWhereCount');
            expect($subject->provides())->toContainKey('whereCount');
            expect($subject->provides())->toContainKey('withCount');
            expect($subject->provides())->toContainKey('hasCount');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkRelativeCount([], '=', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exception if $operator is not stringable', function () use ($subject) {
            $closure1 = function () use ($subject) {
                $result = $subject->checkRelativeCount([], []);
            };
            $closure2 = function () use ($subject) {
                $result = $subject->checkRelativeCount([], new \StdClass());
            };

            expect($closure1)->toThrow(new \InvalidArgumentException());
            expect($closure2)->toThrow(new \InvalidArgumentException());
        });

        it('Validates empty array to have relative sizes', function () use ($subject) {
            expect(
                $subject->checkRelativeCount([], '=', 0)->success
            )
            ->toBe(true);

            expect(
                $subject->checkRelativeCount([], 0)->success
            )
            ->toBe(true);

            expect(
                $subject->checkRelativeCount([], '>=', 0)->success
            )
                ->toBe(true);

            expect(
                $subject->checkRelativeCount([], '≥', 0)->success
            )
            ->toBe(true);

            expect(
                $subject->checkRelativeCount([], '<', 0)->success
            )
            ->toBe(false);

            expect(
                $subject->checkRelativeCount([], '<=', 0)->success
            )
                ->toBe(true);

            expect(
                $subject->checkRelativeCount([], '≤', 0)->success
            )
            ->toBe(true);
        });

        it('Validates non-empty array to have relative sizes', function () use ($subject) {
            expect(
                $subject->checkRelativeCount(['a', 'b'], '=', 2)->success
            )
            ->toBe(true);

            expect(
                $subject->checkRelativeCount(['a', 'b'], '>', 1)->success
            )
            ->toBe(true);

            expect(
                $subject->checkRelativeCount(['a', 'b'], '<', 3)->success
            )
            ->toBe(true);
        });

        it('Validates countable to have relative sizes', function () use ($subject) {
            expect(
                $subject->checkRelativeCount(new \ArrayObject(['a', 'b']), '=', 2)->success
            )
            ->toBe(true);
        });
    });
});
