<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Val;

describe('Valit\Util\Val', function () {

    describe('::escape()', function () {

        it('escapes scalars', function () {
            expect(Val::escape('foo'))->toBe('"foo"');
            expect(Val::escape(42))->toBe('42');
            expect(Val::escape(9.2))->toBe('9.2');
            expect(Val::escape(true))->toBe('true');
        });

        it('escapes null', function () {
            expect(Val::escape(null))->toBe('null');
        });

        it('escapes resources', function () {
            // for instance Resource id #183 (curl)
            expect(Val::escape(curl_init()))->toMatch('/^Resource id #\d+ \(curl\)$/');
            expect(Val::escape(fopen('php://memory', 'r')))->toMatch('/^Resource id #\d+ \(stream\)$/');
        });

        it('escapes callables', function () {
            $closure = function () {
                return 'foo';
            };
            eval('class Val__escape__Invokable { function __invoke() {} }');
            expect(Val::escape($closure))->toBe('Callable ({closure})');
            expect(Val::escape([Val::class, 'toInt']))->toBe('Callable (Valit\Util\Val::toInt)');
            expect(Val::escape(new \Val__escape__Invokable()))->toBe('Callable (Val__escape__Invokable::__invoke)');
        });

        it('escapes DateTime objects', function () {
            eval('class Val__escape__DateTime extends DateTimeImmutable {}');

            expect(Val::escape(new \DateTime('2000-01-01 00:00:00')))
                ->toBe('DateTime (2000-01-01T00:00:00+00:00)');

            expect(Val::escape(new \DateTimeImmutable('2000-01-01 00:00:00')))
                ->toBe('DateTimeImmutable (2000-01-01T00:00:00+00:00)');

            expect(Val::escape(new \Val__escape__DateTime('2000-01-01 00:00:00')))
                ->toBe('Val__escape__DateTime (2000-01-01T00:00:00+00:00)');
        });

        it('escapes objects', function () {
            expect(Val::escape(new \stdClass()))->toBe('Object (stdClass)');
            expect(Val::escape((object) []))->toBe('Object (stdClass)');
            expect(Val::escape((json_decode('{}'))))->toBe('Object (stdClass)');
            expect(Val::escape(new \SimpleXMLElement('<r/>')))->toBe('Object (SimpleXMLElement)');
            expect(Val::escape(new \Exception()))->toBe('Object (Exception)');
        });

        it('escapes arrays', function () {
            expect(Val::escape([]))->toBe('Array (0 entries)');
            expect(Val::escape([1, 2, 3]))->toBe('Array (3 entries)');
            expect(Val::escape(new \ArrayIterator([1, 2, 3])))->not->toBe('Array (3 entries)');
        });
    });
});
