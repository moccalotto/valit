<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Val;

describe('Valit\Util\Val', function () {

    describe('::format()', function () {

        it('uses Val::escape() to filter values via the »normal« format', function () {
            expect(Val::format('foo', 'normal'))->toBe(Val::escape('foo'));
            expect(Val::format(1234, 'normal'))->toBe(Val::escape(1234));
            expect(Val::format([], 'normal'))->toBe(Val::escape([]));
        });

        it('has an "»imploded« formatter that implodes iterables', function () {
            expect(Val::format([1, 2, 3], 'imploded'))->toBe('1, 2, 3');
            expect(Val::format(
                new \ArrayIterator(['foo', 'bar', 'baz']),
                'imploded'
            ))->toBe('"foo", "bar", "baz"');

            expect(Val::format(
                new \ArrayObject(['a' => 'foo', 'b' => 'bar', 'c' => 'baz']),
                'imploded'
            ))->toBe('"foo", "bar", "baz"');
        });

        it('has a »raw« format that attempts to stringify values', function () {
            expect(Val::format('foo', 'raw'))->toBe('foo');
            expect(Val::format(9.2, 'raw'))->toBe('9.2');
            expect(Val::format(
                new \SimpleXMLElement('<r>5.5</r>'),
                'raw'
            ))->toBe('5.5');

            expect(Val::format([], 'raw'))->toBe('Array (0 entries)');
            expect(Val::format(new \stdClass, 'raw'))->toBe('Object (stdClass)');
        });

        it('has a »type« format that returns the PHP type of the value', function () {
            expect(Val::format([], 'type'))->toBe('array');
            expect(Val::format((object) [], 'type'))->toBe('object');
            expect(Val::format(1, 'type'))->toBe('integer');
            expect(Val::format(1.0, 'type'))->toBe('double');
            expect(Val::format('foo', 'type'))->toBe('string');
            expect(Val::format(curl_init(), 'type'))->toBe('resource');
        });

        it('has an »int« format that attempts to convert the value to an integer', function () {
            expect(Val::format([], 'int'))->toBe('[not numeric]');
            expect(Val::format((object) [], 'int'))->toBe('[not numeric]');
            expect(Val::format('42', 'int'))->toBe('42');
            expect(Val::format('42.0', 'int'))->toBe('42');
            expect(Val::format(42, 'int'))->toBe('42');
            expect(Val::format(42.0, 'int'))->toBe('42');
            expect(Val::format(
                new \SimpleXMLElement('<r>42.0</r>'),
                'int'
            ))->toBe('42');
        });

        it('has a »float« format that attempts to convert the value to a float', function () {
            expect(Val::format([], 'float'))->toBe('[not numeric]');
            expect(Val::format((object) [], 'float'))->toBe('[not numeric]');
            expect(Val::format('42', 'float'))->toBe('42');
            expect(Val::format('42.0', 'float'))->toBe('42');
            expect(Val::format(42, 'float'))->toBe('42');
            expect(Val::format(42.3, 'float'))->toBe('42.3');
            expect(Val::format(
                new \SimpleXMLElement('<r>42.666</r>'),
                'float'
            ))->toBe('42.666');
        });

        it('has a »hex« format that attempts to convert the value to a hex-encoded integer', function () {
            expect(Val::format([], 'hex'))->toBe('[not integer]');
            expect(Val::format((object) [], 'hex'))->toBe('[not integer]');
            expect(Val::format('42', 'hex'))->toBe('2a');
            expect(Val::format('42.0', 'hex'))->toBe('2a');
            expect(Val::format(42, 'hex'))->toBe('2a');
            expect(Val::format(42.3, 'hex'))->toBe('[not integer]');
            expect(Val::format(
                new \SimpleXMLElement('<r>42</r>'),
                'hex'
            ))->toBe('2a');
        });

        it('has a »count« format that attempt to return the number of elements in the value', function () {
            expect(Val::format([], 'count'))->toBe('0');
            expect(Val::format((object) [], 'count'))->toBe('[not countable]');
            expect(Val::format(new \ArrayObject([1,2,3]), 'count'))->toBe('3');
            expect(Val::format(new \ArrayIterator([1,2,3]), 'count'))->toBe('3');

            expect(Val::format((object) [], 'count'))->toBe('[not countable]');
            expect(Val::format(1, 'count'))->toBe('[not countable]');
            expect(Val::format('foo', 'count'))->toBe('[not countable]');
        });

        it('throws up if the format is unknown', function () {
            expect(function () {
                Val::format('', 'foo');
            })->toThrow(new \LogicException);

            expect(function () {
                Val::format('', 'unknown format');
            })->toThrow(new \LogicException);
        });
    });
});
