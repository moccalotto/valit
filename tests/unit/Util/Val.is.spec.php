<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Val;

describe('Valit\Util\Val', function () {

    it('existst', function () {
        expect(class_exists(Val::class))->toBe(true);
    });

    describe('::is() with string arguments', function () {

        it('tests booleans', function () {
            expect(Val::is(true, 'bool'))->toBe(true);
            expect(Val::is(false, 'bool'))->toBe(true);
            expect(Val::is(true, 'boolean'))->toBe(true);
            expect(Val::is(false, 'boolean'))->toBe(true);

            expect(Val::is(1, 'bool'))->toBe(false);
            expect(Val::is(0, 'bool'))->toBe(false);
            expect(Val::is(1.0, 'boolean'))->toBe(false);
            expect(Val::is(0.0, 'boolean'))->toBe(false);

            expect(Val::is('true', 'boolean'))->toBe(false);
            expect(Val::is('', 'boolean'))->toBe(false);
            expect(Val::is('0', 'boolean'))->toBe(false);

            expect(Val::is(null, 'boolean'))->toBe(false);
            expect(Val::is([], 'boolean'))->toBe(false);
            expect(Val::is((object) [], 'boolean'))->toBe(false);
            expect(Val::is(curl_init(), 'boolean'))->toBe(false);
        });

        it('tests integers', function () {
            expect(Val::is(1, 'int'))->toBe(true);
            expect(Val::is(0, 'int'))->toBe(true);
            expect(Val::is(1, 'integer'))->toBe(true);
            expect(Val::is(1, 'integer'))->toBe(true);

            expect(Val::is(true, 'int'))->toBe(false);
            expect(Val::is(false, 'int'))->toBe(false);
            expect(Val::is(1.0, 'integer'))->toBe(false);
            expect(Val::is(0.0, 'integer'))->toBe(false);

            expect(Val::is('1', 'integer'))->toBe(false);
            expect(Val::is('', 'integer'))->toBe(false);
            expect(Val::is('0', 'integer'))->toBe(false);

            expect(Val::is(null, 'integer'))->toBe(false);
            expect(Val::is([], 'integer'))->toBe(false);
            expect(Val::is((object) [], 'integer'))->toBe(false);
            expect(Val::is(curl_init(), 'integer'))->toBe(false);
        });

        it('tests floats', function () {
            expect(Val::is(1.0, 'float'))->toBe(true);
            expect(Val::is(0.0, 'float'))->toBe(true);
            expect(Val::is(NAN, 'float'))->toBe(true);
            expect(Val::is(INF, 'float'))->toBe(true);
            expect(Val::is(1.0, 'double'))->toBe(true);
            expect(Val::is(1.0, 'double'))->toBe(true);

            expect(Val::is(true, 'float'))->toBe(false);
            expect(Val::is(false, 'float'))->toBe(false);
            expect(Val::is(1, 'double'))->toBe(false);
            expect(Val::is(0, 'double'))->toBe(false);

            expect(Val::is('1.0', 'double'))->toBe(false);
            expect(Val::is('0.0', 'double'))->toBe(false);
            expect(Val::is('', 'double'))->toBe(false);

            expect(Val::is(null, 'double'))->toBe(false);
            expect(Val::is([], 'double'))->toBe(false);
            expect(Val::is((object) [], 'double'))->toBe(false);
            expect(Val::is(curl_init(), 'double'))->toBe(false);
        });

        it('tests strings', function () {
            expect(Val::is('', 'string'))->toBe(true);
            expect(Val::is('foo', 'string'))->toBe(true);

            expect(Val::is(true, 'string'))->toBe(false);
            expect(Val::is(false, 'string'))->toBe(false);
            expect(Val::is(1, 'string'))->toBe(false);
            expect(Val::is(0, 'string'))->toBe(false);

            expect(Val::is(1.0, 'string'))->toBe(false);
            expect(Val::is(0.0, 'string'))->toBe(false);

            expect(Val::is(null, 'string'))->toBe(false);
            expect(Val::is([], 'string'))->toBe(false);
            expect(Val::is((object) [], 'string'))->toBe(false);
            expect(Val::is(curl_init(), 'string'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'string'))->toBe(false);
        });

        it('tests null', function () {
            expect(Val::is(null, 'null'))->toBe(true);

            expect(Val::is(true, 'null'))->toBe(false);
            expect(Val::is(false, 'null'))->toBe(false);
            expect(Val::is(1, 'null'))->toBe(false);
            expect(Val::is(0, 'null'))->toBe(false);
            expect(Val::is(1.0, 'null'))->toBe(false);
            expect(Val::is(0.0, 'null'))->toBe(false);
            expect(Val::is('', 'null'))->toBe(false);

            expect(Val::is([], 'null'))->toBe(false);
            expect(Val::is((object) [], 'null'))->toBe(false);
            expect(Val::is(curl_init(), 'null'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'null'))->toBe(false);
        });

        it('tests arrays', function () {
            expect(Val::is([], 'array'))->toBe(true);
            expect(Val::is(['goo'], 'array'))->toBe(true);
            expect(Val::is([1, 2, 3], 'array'))->toBe(true);
            expect(Val::is(['foo' => 'bar'], 'array'))->toBe(true);

            expect(Val::is(true, 'array'))->toBe(false);
            expect(Val::is(false, 'array'))->toBe(false);
            expect(Val::is(true, 'array'))->toBe(false);
            expect(Val::is(false, 'array'))->toBe(false);

            expect(Val::is(1, 'array'))->toBe(false);
            expect(Val::is(0, 'array'))->toBe(false);
            expect(Val::is(1.0, 'array'))->toBe(false);
            expect(Val::is(0.0, 'array'))->toBe(false);

            expect(Val::is('true', 'array'))->toBe(false);
            expect(Val::is('', 'array'))->toBe(false);
            expect(Val::is('0', 'array'))->toBe(false);

            expect(Val::is(null, 'array'))->toBe(false);
            expect(Val::is((object) [], 'array'))->toBe(false);
            expect(Val::is(curl_init(), 'array'))->toBe(false);
        });

        it('tests scalar', function () {
            expect(Val::is(true, 'scalar'))->toBe(true);
            expect(Val::is(false, 'scalar'))->toBe(true);
            expect(Val::is(1, 'scalar'))->toBe(true);
            expect(Val::is(0, 'scalar'))->toBe(true);
            expect(Val::is(1.0, 'scalar'))->toBe(true);
            expect(Val::is(0.0, 'scalar'))->toBe(true);
            expect(Val::is('', 'scalar'))->toBe(true);
            expect(Val::is('foo', 'scalar'))->toBe(true);

            expect(Val::is(null, 'scalar'))->toBe(false);
            expect(Val::is([], 'scalar'))->toBe(false);
            expect(Val::is((object) [], 'scalar'))->toBe(false);
            expect(Val::is(curl_init(), 'scalar'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'scalar'))->toBe(false);
        });

        it('tests callable', function () {
            $closure = function () {
                return null;
            };
            expect(Val::is('strval', 'callable'))->toBe(true);
            expect(Val::is('DateTime::createFromFormat', 'callable'))->toBe(true);
            expect(Val::is(['DateTime', 'createFromFormat'], 'callable'))->toBe(true);
            expect(Val::is([new \DateTime(), 'format'], 'callable'))->toBe(true);
            expect(Val::is($closure, 'callable'))->toBe(true);

            expect(Val::is(true, 'callable'))->toBe(false);
            expect(Val::is(false, 'callable'))->toBe(false);
            expect(Val::is(1, 'callable'))->toBe(false);
            expect(Val::is(0, 'callable'))->toBe(false);
            expect(Val::is(1.0, 'callable'))->toBe(false);
            expect(Val::is(0.0, 'callable'))->toBe(false);
            expect(Val::is('', 'callable'))->toBe(false);
            expect(Val::is('foo', 'callable'))->toBe(false);
            expect(Val::is(null, 'callable'))->toBe(false);
            expect(Val::is([], 'callable'))->toBe(false);
            expect(Val::is((object) [], 'callable'))->toBe(false);
            expect(Val::is(curl_init(), 'callable'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'callable'))->toBe(false);
        });

        it('tests throwable', function () {
            expect(Val::is(new \Exception('foo'), 'throwable'))->toBe(true);

            if (class_exists('Error')) {
                expect(Val::is(new \Error('foo'), 'throwable'))->toBe(true);
            }

            expect(Val::is(true, 'throwable'))->toBe(false);
            expect(Val::is(false, 'throwable'))->toBe(false);
            expect(Val::is(1, 'throwable'))->toBe(false);
            expect(Val::is(0, 'throwable'))->toBe(false);
            expect(Val::is(1.0, 'throwable'))->toBe(false);
            expect(Val::is(0.0, 'throwable'))->toBe(false);
            expect(Val::is('', 'throwable'))->toBe(false);
            expect(Val::is('foo', 'throwable'))->toBe(false);
            expect(Val::is(null, 'throwable'))->toBe(false);
            expect(Val::is([], 'throwable'))->toBe(false);
            expect(Val::is((object) [], 'throwable'))->toBe(false);
            expect(Val::is(curl_init(), 'throwable'))->toBe(false);
        });

        it('tests iterable', function () {
            expect(Val::is([], 'iterable'))->toBe(true);
            expect(Val::is(new \ArrayIterator([]), 'iterable'))->toBe(true);
            expect(Val::is(new \EmptyIterator(), 'iterable'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'iterable'))->toBe(true);

            expect(Val::is(true, 'iterable'))->toBe(false);
            expect(Val::is(false, 'iterable'))->toBe(false);
            expect(Val::is(1, 'iterable'))->toBe(false);
            expect(Val::is(0, 'iterable'))->toBe(false);
            expect(Val::is(1.0, 'iterable'))->toBe(false);
            expect(Val::is(0.0, 'iterable'))->toBe(false);
            expect(Val::is('', 'iterable'))->toBe(false);
            expect(Val::is('foo', 'iterable'))->toBe(false);
            expect(Val::is(null, 'iterable'))->toBe(false);
            expect(Val::is((object) [], 'iterable'))->toBe(false);
            expect(Val::is(curl_init(), 'iterable'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'iterable'))->toBe(false);
        });

        it('tests countable', function () {
            expect(Val::is([], 'countable'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'countable'))->toBe(true);

            expect(Val::is(new \EmptyIterator(), 'countable'))->toBe(false);
            expect(Val::is(true, 'countable'))->toBe(false);
            expect(Val::is(false, 'countable'))->toBe(false);
            expect(Val::is(1, 'countable'))->toBe(false);
            expect(Val::is(0, 'countable'))->toBe(false);
            expect(Val::is(1.0, 'countable'))->toBe(false);
            expect(Val::is(0.0, 'countable'))->toBe(false);
            expect(Val::is('', 'countable'))->toBe(false);
            expect(Val::is('foo', 'countable'))->toBe(false);
            expect(Val::is(null, 'countable'))->toBe(false);
            expect(Val::is((object) [], 'countable'))->toBe(false);
            expect(Val::is(curl_init(), 'countable'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'countable'))->toBe(false);
        });

        it('tests arrayable', function () {
            expect(Val::is([], 'arrayable'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'arrayable'))->toBe(true);

            expect(Val::is(new \EmptyIterator(), 'arrayable'))->toBe(false);
            expect(Val::is(true, 'arrayable'))->toBe(false);
            expect(Val::is(false, 'arrayable'))->toBe(false);
            expect(Val::is(1, 'arrayable'))->toBe(false);
            expect(Val::is(0, 'arrayable'))->toBe(false);
            expect(Val::is(1.0, 'arrayable'))->toBe(false);
            expect(Val::is(0.0, 'arrayable'))->toBe(false);
            expect(Val::is('', 'arrayable'))->toBe(false);
            expect(Val::is('foo', 'arrayable'))->toBe(false);
            expect(Val::is(null, 'arrayable'))->toBe(false);
            expect(Val::is((object) [], 'arrayable'))->toBe(false);
            expect(Val::is(curl_init(), 'arrayable'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'arrayable'))->toBe(false);
        });

        it('tests container', function () {
            expect(Val::is([], 'container'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'container'))->toBe(true);
            expect(Val::is(new \ArrayIterator([]), 'container'))->toBe(true);

            expect(Val::is(new \EmptyIterator(), 'false'))->toBe(false);
            expect(Val::is(true, 'false'))->toBe(false);
            expect(Val::is(false, 'false'))->toBe(false);
            expect(Val::is(1, 'false'))->toBe(false);
            expect(Val::is(0, 'false'))->toBe(false);
            expect(Val::is(1.0, 'false'))->toBe(false);
            expect(Val::is(0.0, 'false'))->toBe(false);
            expect(Val::is('', 'false'))->toBe(false);
            expect(Val::is('foo', 'false'))->toBe(false);
            expect(Val::is(null, 'false'))->toBe(false);
            expect(Val::is((object) [], 'false'))->toBe(false);
            expect(Val::is(curl_init(), 'false'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'false'))->toBe(false);
        });

        it('tests stringable', function () {
            expect(Val::is(1, 'stringable'))->toBe(true);
            expect(Val::is(0, 'stringable'))->toBe(true);
            expect(Val::is(1.0, 'stringable'))->toBe(true);
            expect(Val::is(0.0, 'stringable'))->toBe(true);
            expect(Val::is('', 'stringable'))->toBe(true);
            expect(Val::is('foo', 'stringable'))->toBe(true);
            expect(Val::is(new \Exception('foo'), 'stringable'))->toBe(true);

            expect(Val::is(true, 'stringable'))->toBe(false);
            expect(Val::is(false, 'stringable'))->toBe(false);
            expect(Val::is(null, 'stringable'))->toBe(false);
            expect(Val::is((object) [], 'stringable'))->toBe(false);
            expect(Val::is(curl_init(), 'stringable'))->toBe(false);
            expect(Val::is([], 'stringable'))->toBe(false);
            expect(Val::is(new \ArrayObject([]), 'stringable'))->toBe(false);
            expect(Val::is(new \EmptyIterator(), 'stringable'))->toBe(false);
        });

        it('tests numeric', function () {
            expect(Val::is(1, 'numeric'))->toBe(true);
            expect(Val::is(0, 'numeric'))->toBe(true);
            expect(Val::is(1.0, 'numeric'))->toBe(true);
            expect(Val::is(0.0, 'numeric'))->toBe(true);
            expect(Val::is('1', 'numeric'))->toBe(true);
            expect(Val::is('0', 'numeric'))->toBe(true);
            expect(Val::is('1.0', 'numeric'))->toBe(true);
            expect(Val::is('0.0', 'numeric'))->toBe(true);
            expect(Val::is('007', 'numeric'))->toBe(true);

            expect(Val::is('1.foo', 'numeric'))->toBe(false);
            expect(Val::is('0x42a', 'numeric'))->toBe(false);
            expect(Val::is('', 'numeric'))->toBe(false);
            expect(Val::is('foo', 'numeric'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'numeric'))->toBe(false);
            expect(Val::is(true, 'numeric'))->toBe(false);
            expect(Val::is(false, 'numeric'))->toBe(false);
            expect(Val::is(null, 'numeric'))->toBe(false);
            expect(Val::is((object) [], 'numeric'))->toBe(false);
            expect(Val::is(curl_init(), 'numeric'))->toBe(false);
            expect(Val::is([], 'numeric'))->toBe(false);
            expect(Val::is(new \ArrayObject([]), 'numeric'))->toBe(false);
            expect(Val::is(new \EmptyIterator(), 'numeric'))->toBe(false);
        });

        it('tests class inheritance', function () {
            expect(Val::is(new \ArrayObject([]), 'Traversable'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'Countable'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'Iterable'))->toBe(true);
            expect(Val::is(new \ArrayObject([]), 'ArrayAccess'))->toBe(true);
            expect(Val::is(new \EmptyIterator(), 'Traversable'))->toBe(true);

            expect(Val::is(1, 'Traversable'))->toBe(false);
            expect(Val::is(0, 'Traversable'))->toBe(false);
            expect(Val::is(1.0, 'Traversable'))->toBe(false);
            expect(Val::is(0.0, 'Traversable'))->toBe(false);
            expect(Val::is('1', 'Traversable'))->toBe(false);
            expect(Val::is('0', 'Traversable'))->toBe(false);
            expect(Val::is('1.0', 'Traversable'))->toBe(false);
            expect(Val::is('0.0', 'Traversable'))->toBe(false);
            expect(Val::is('007', 'Traversable'))->toBe(false);

            expect(Val::is('1.foo', 'Traversable'))->toBe(false);
            expect(Val::is('0x42a', 'Traversable'))->toBe(false);
            expect(Val::is('', 'Traversable'))->toBe(false);
            expect(Val::is('foo', 'Traversable'))->toBe(false);
            expect(Val::is(new \Exception('foo'), 'Traversable'))->toBe(false);
            expect(Val::is(true, 'Traversable'))->toBe(false);
            expect(Val::is(false, 'Traversable'))->toBe(false);
            expect(Val::is(null, 'Traversable'))->toBe(false);
            expect(Val::is((object) [], 'Traversable'))->toBe(false);
            expect(Val::is(curl_init(), 'Traversable'))->toBe(false);
            expect(Val::is([], 'Traversable'))->toBe(false);
        });

        it('tests for arrays of types', function () {
            expect(Val::is([], 'bool[]'))->toBe(true);
            expect(Val::is([true, false], 'bool[]'))->toBe(true);
            expect(Val::is([9, false], 'bool[]'))->toBe(false);

            expect(Val::is([], 'string[]'))->toBe(true);
            expect(Val::is(['foo', 'bar'], 'string[]'))->toBe(true);
            expect(Val::is([9, 'bar'], 'string[]'))->toBe(false);
            expect(Val::is([true, 'bar'], 'string[]'))->toBe(false);

            expect(Val::is([], 'float[]'))->toBe(true);
            expect(Val::is([0.0, 1.0], 'float[]'))->toBe(true);
            expect(Val::is([9, 1.0], 'float[]'))->toBe(false);
            expect(Val::is([true, 1.0], 'float[]'))->toBe(false);

            expect(Val::is([], 'int[]'))->toBe(true);
            expect(Val::is([0, 1], 'int[]'))->toBe(true);
            expect(Val::is([true, 1], 'int[]'))->toBe(false);
            expect(Val::is([true, 1], 'int[]'))->toBe(false);

            expect(Val::is([], 'numeric[]'))->toBe(true);
            expect(Val::is([0, 1], 'numeric[]'))->toBe(true);
            expect(Val::is([0, 1.0], 'numeric[]'))->toBe(true);
            expect(Val::is(['0.0', 1.0], 'numeric[]'))->toBe(true);
            expect(Val::is(['', 1], 'numeric[]'))->toBe(false);
            expect(Val::is([true, 1], 'numeric[]'))->toBe(false);

            expect(Val::is([[], []], 'arrayable[]'))->toBe(true);
            expect(Val::is([[], []], 'iterable[]'))->toBe(true);
            expect(Val::is([[], []], 'countable[]'))->toBe(true);

            expect(Val::is(['foo', []], 'arrayable[]'))->toBe(false);
            expect(Val::is(['foo', []], 'iterable[]'))->toBe(false);
            expect(Val::is(['foo', []], 'countable[]'))->toBe(false);

            expect(
                Val::is(
                    [
                        new \ArrayObject([]),
                        new \EmptyIterator(),
                        new \ArrayIterator([]),
                    ],
                    'Traversable[]'
                )
            )->toBe(true);
        });
    });
});
