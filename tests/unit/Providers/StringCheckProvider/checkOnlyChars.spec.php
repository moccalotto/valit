<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;

describe('StringCheckProvider', function () {
    $subject = new \Valit\Providers\StringCheckProvider();

    describe('checkOnlyChars', function () use ($subject) {

        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toContainKey('validChars');
            expect($subject->provides())->toContainKey('validCharacters');
            expect($subject->provides())->toContainKey('onlyChars');
            expect($subject->provides())->toContainKey('onlyCharacters');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkOnlyChars('foo', 'fo');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exception if $allowedChars is not stringable', function () use ($subject) {
            $closure1 = function () use ($subject) {
                $result = $subject->checkOnlyChars('foo', []);
            };
            $closure2 = function () use ($subject) {
                $result = $subject->checkOnlyChars('foo', new \StdClass());
            };

            expect($closure1)->toThrow(new \InvalidArgumentException());
            expect($closure2)->toThrow(new \InvalidArgumentException());
        });

        it('validates strings that contain valid characters', function () use ($subject) {
            expect(
                $subject->checkOnlyChars('foo', 'fo')->success()
            )
            ->toBe(true);
        });

        it('validates strings that contain valid characters from a large set', function () use ($subject) {
            expect(
                $subject->checkOnlyChars('foo', implode('', range('a', 'z')))->success()
            )
            ->toBe(true);
        });

        it('invalidates strings that contain illegal characters', function () use ($subject) {
            expect(
                $subject->checkOnlyChars('foobar', 'fo')->success()
            )->toBe(false);
        });

        it('allows duplicates in $allowedChars', function () use ($subject) {
            expect(
                $subject->checkOnlyChars('foo', 'fooooooooofffffofofofo')->success()
            )
            ->toBe(true);
        });
    });
});
