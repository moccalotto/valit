<?php

namespace Kahlan\Spec\Suite;

use Valit\Manager;
use Valit\Util\FlatContainer;

describe('Manager', function () {
    describe('::__construct()', function () {
        it('is initializable with an empty container', function () {
            expect(function () {
                new FlatContainer([]);
            })->not->toThrow();

            expect(
                (new FlatContainer([]))->container
            )->toBe([ '' => [] ]);
        });

        it('is flattens various contianers', function () {
            expect(
                (new FlatContainer([1,2,3]))->container
            )->toBe([1,2,3]);

            expect(
                (new FlatContainer('foo'))->container
            )->toBe(['' => 'foo']);

            expect(
                (new FlatContainer((object) ['a' => 1, 'b' => 42]))->container
            )->toBe(['a' => 1, 'b' => 42]);

            expect(
                (new FlatContainer([
                    'a' => [
                        'b' => 42,
                    ],
                ]))->container
            )->toBe(['a' => ['b' => 42], 'a/b' => 42]);
        });
    });

    describe('::find()', function () {
        it('can glob for simple data', function () {
            $data = [
                'a' => [
                    'b' => [
                        'c' => 'd'
                    ]
                ],
                'x' => '2',
                'y' => 4,
            ];

            $subject = new FlatContainer($data);

            expect($subject->find('*'))->toContainKey([
                'a',
                'a/b',
                'a/b/c',
                'x',
                'y'
            ]);

            expect($subject->find('a/*'))->toContainKey([
                'a/b',
            ]);
            expect($subject->find('a/*/*'))->toContainKey([
                'a/b/c',
            ]);

            expect($subject->find('a'))->toBe([
                'a' => [
                    'b' => [
                        'c' => 'd'
                    ]
                ],
            ]);
            expect($subject->find('a/b'))->toBe([
                'a/b' => [
                    'c' => 'd'
                ]
            ]);
            expect($subject->find('a/b/c'))->toBe(['a/b/c' => 'd']);
            expect($subject->find('x'))->toBe(['x' => '2']);
            expect($subject->find('y'))->toBe(['y' => 4]);
        });
    });
    it('expands objects', function () {
        $o = new \Valit\Util\ContainerTestClass();

        $subject = new FlatContainer(['o' => $o]);

        expect($subject->container)->toContainKey('o');
        expect($subject->container['o'])->toBeAnInstanceOf('Valit\Util\ContainerTestClass');

        expect($subject->find('o'))->toBe([
            'o' => $o,
        ]);

        expect($subject->find('o/public'))->toBe([
            'o/public' => 'propertyAlreadyExists',
        ]);

        expect($subject->find('o/foo'))->toBe([
            'o/foo' => 'validationData',
        ]);
        expect($subject->find('o/bar'))->toBe([
            'o/bar' => 'validationData',
        ]);
        expect($subject->find('o/baz'))->toBe([
            'o/baz' => [
                'thing1' => 'validationData',
                'thing2' => 'validationData',
            ]
        ]);


        expect($subject->find('o/validationData/foo'))->toBe([
            'o/validationData/foo' => 'validationData',
        ]);
        expect($subject->find('o/validationData/bar'))->toBe([
            'o/validationData/bar' => 'validationData',
        ]);
        expect($subject->find('o/validationData/baz'))->toBe([
            'o/validationData/baz' => [
                'thing1' => 'validationData',
                'thing2' => 'validationData',
            ]
        ]);
        expect($subject->find('o/validationData/public'))->toBe([
            'o/validationData/public' => 'validationData',
        ]);
        expect($subject->find('o/validationData/protected'))->toBe([
            'o/validationData/protected' => 'validationData',
        ]);
        expect($subject->find('o/debugData/foo'))->toBe([
            'o/debugData/foo' => '__debugInfo',
        ]);
        expect($subject->find('o/debugData/bar'))->toBe([
            'o/debugData/bar' => '__debugInfo',
        ]);
        expect($subject->find('o/debugData/baz'))->toBe([
            'o/debugData/baz' => [
                'thing1' => '__debugInfo',
                'thing2' => '__debugInfo',
            ]
        ]);
        expect($subject->find('o/debugData/public'))->toBe([
            'o/debugData/public' => '__debugInfo',
        ]);
        expect($subject->find('o/debugData/protected'))->toBe([
            'o/debugData/protected' => '__debugInfo',
        ]);
        expect($subject->find('o/jsonData/foo'))->toBe([
            'o/jsonData/foo' => 'jsonSerialize',
        ]);
        expect($subject->find('o/jsonData/bar'))->toBe([
            'o/jsonData/bar' => 'jsonSerialize',
        ]);
        expect($subject->find('o/jsonData/baz'))->toBe([
            'o/jsonData/baz' => [
                'thing1' => 'jsonSerialize',
                'thing2' => 'jsonSerialize',
            ]
        ]);
        expect($subject->find('o/jsonData/public'))->toBe([
            'o/jsonData/public' => 'jsonSerialize',
        ]);
        expect($subject->find('o/jsonData/protected'))->toBe([
            'o/jsonData/protected' => 'jsonSerialize',
        ]);
        expect($subject->find('o/iteratorData/foo'))->toBe([
            'o/iteratorData/foo' => 'iterator',
        ]);
        expect($subject->find('o/iteratorData/bar'))->toBe([
            'o/iteratorData/bar' => 'iterator',
        ]);
        expect($subject->find('o/iteratorData/baz'))->toBe([
            'o/iteratorData/baz' => [
                'thing1' => 'iterator',
                'thing2' => 'iterator',
            ]
        ]);
        expect($subject->find('o/iteratorData/public'))->toBe([
            'o/iteratorData/public' => 'iterator',
        ]);
        expect($subject->find('o/iteratorData/protected'))->toBe([
            'o/iteratorData/protected' => 'iterator',
        ]);
    });
});
