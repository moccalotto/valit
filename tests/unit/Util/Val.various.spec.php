<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Val;

describe('Valit\Util\Val', function () {

    describe('::stringable()', function () {
        it('works on scalars', function () {
            expect(Val::stringable('foo'))->toBe(true);
            expect(Val::stringable(1234))->toBe(true);
            expect(Val::stringable(123.456))->toBe(true);
            expect(Val::stringable(true))->toBe(false);
        });
        it('works on objects', function () {
            expect(Val::stringable(new \Exception('Exceptions are stringable')))->toBe(true);
            expect(Val::stringable(new \stdClass()))->toBe(false);
            expect(Val::stringable(curl_init()))->toBe(false);
            expect(Val::stringable([]))->toBe(false);
        });
    });

    describe('::numeric()', function () {
        it('returns true if and only if the argument can be converted to a floating point number', function () {
            expect(Val::numeric(1234))->toBe(true);
            expect(Val::numeric(1234.0))->toBe(true);
            expect(Val::numeric('1234'))->toBe(true);
            expect(Val::numeric('1234.0'))->toBe(true);
            expect(Val::numeric(123.456))->toBe(true);
            expect(Val::numeric(new \SimpleXmlElement('<r>1234</r>')))->toBe(true);
            expect(Val::numeric(new \SimpleXmlElement('<r>1234.0</r>')))->toBe(true);
            expect(Val::numeric(new \SimpleXmlElement('<r>1234.567</r>')))->toBe(true);


            expect(Val::numeric(true))->toBe(false);
            expect(Val::numeric(new \SimpleXmlElement('<r>foo</r>')))->toBe(false);
            expect(Val::numeric(curl_init()))->toBe(false);
        });
    });

    describe('::intable()', function () {
        it('returns true if and only if arg can be converted to integer without data loss', function () {
            expect(Val::intable(1234))->toBe(true);
            expect(Val::intable(1234.0))->toBe(true);
            expect(Val::intable('1234'))->toBe(true);
            expect(Val::intable('1234.0'))->toBe(true);
            expect(Val::intable(new \SimpleXmlElement('<r>1234</r>')))->toBe(true);
            expect(Val::intable(new \SimpleXmlElement('<r>1234.0</r>')))->toBe(true);


            expect(Val::intable(123.456))->toBe(false);
            expect(Val::intable(true))->toBe(false);
            expect(Val::intable(new \SimpleXmlElement('<r>foo</r>')))->toBe(false);
            expect(Val::intable(new \SimpleXmlElement('<r>1234.567</r>')))->toBe(false);
            expect(Val::intable(curl_init()))->toBe(false);
        });
    });

    describe('::countable()', function () {
        it('returns true if and only if the argument is an array or an object that implements Countable', function () {
            expect(Val::countable([]))->toBe(true);
            expect(Val::countable(
                new \ArrayIterator([])
            ))->toBe(true);
            expect(Val::countable(
                new \InfiniteIterator(
                    new \ArrayIterator([])
                )
            ))->toBe(false);

            expect(Val::countable(new \EmptyIterator()))->toBe(false);
            expect(Val::countable('foo'))->toBe(false);
            expect(Val::countable(curl_init()))->toBe(false);
            expect(Val::countable(new \Exception))->toBe(false);
            expect(Val::countable(42))->toBe(false);
            expect(Val::countable(null))->toBe(false);
        });
    });

    describe('::throwable()', function () {
        it('returns true if and only if the argument is an exception or other throwable object', function () {
            expect(Val::throwable(new \Exception))->toBe(true);
            expect(Val::throwable(new \RuntimeException))->toBe(true);
            if (class_exists('Error')) {
                expect(Val::throwable(new \Error))->toBe(true);
            }

            expect(Val::throwable([]))->toBe(false);
            expect(Val::throwable(new \ArrayIterator([])))->toBe(false);
            expect(Val::throwable('foo'))->toBe(false);
            expect(Val::throwable(curl_init()))->toBe(false);
            expect(Val::throwable(42))->toBe(false);
            expect(Val::throwable(null))->toBe(false);
        });
    });
});
