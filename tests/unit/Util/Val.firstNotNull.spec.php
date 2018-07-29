<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Val;

describe('Valit\Util\Val', function () {

    describe('firstNotNull()', function () {

        it('returns null if no arguments given', function () {
            expect(Val::firstNotNull())->toBe(null);
        });

        it('returns null if all arguments are null', function () {
            expect(Val::firstNotNull(null, null, null))->toBe(null);
        });

        it('returns the first argument that is not null', function () {
            expect(Val::firstNotNull(null, null, 3, 'ignored'))->toBe(3);
            expect(Val::firstNotNull(null, 'foo', 3, 'ignored'))->toBe('foo');
            expect(Val::firstNotNull('bar', 'foo', 3, 'ignored'))->toBe('bar');
            expect(Val::firstNotNull([null, 'foo'], 'ignored'))->toBe([null, 'foo']);
        });
    });

    describe('firstElementNotNull()', function () {

        it('throws an exception if arg is not iterable', function () {
            expect(function () {
                Val::firstElementNotNull('foo');
            })->toThrow();

            expect(function () {
                Val::firstElementNotNull(1, 2, 3);
            })->toThrow();

            expect(function () {
                Val::firstElementNotNull(null);
            })->toThrow();
        });

        it('returns null if all elements are null', function () {
            expect(Val::firstElementNotNull([null, null, null]))->toBe(null);
        });

        it('returns null if array is empty', function () {
            expect(Val::firstElementNotNull([]))->toBe(null);
        });

        it('returns the first not-null element in an array', function () {
            expect(Val::firstElementNotNull([null, 'foo', 'ignored']))->toBe('foo');
            expect(Val::firstElementNotNull([null, null, 'bar', 'ignored']))->toBe('bar');
            expect(Val::firstElementNotNull([null, null, null, 'baz', 'ignored']))->toBe('baz');

            expect(
                Val::firstElementNotNull(
                    new \ArrayIterator([null, 'iterable'])
                )
            )->toBe('iterable');
        });
    });
});
