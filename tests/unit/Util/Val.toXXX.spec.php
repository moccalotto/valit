<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\Val;

describe('Valit\Util\Val', function () {

    describe('::toInt()', function () {

        it('converts intables to ints', function () {
            expect(Val::toInt(-42))->toBe(-42);
            expect(Val::toInt(PHP_INT_MAX))->toBe(PHP_INT_MAX);
            expect(Val::toInt(1.0))->toBe(1);
            expect(Val::toInt('-600.00000'))->toBe(-600);
            expect(Val::toInt(new \SimpleXmlElement('<r>-555.00</r>')))->toBe(-555);
        });

        it('throws up when trying to convert non-intables', function () {
            expect(function () {
                Val::toInt(42.1);
            })->toThrow(new \InvalidArgumentException('The given double could not be converted to an integer'));
            expect(function () {
                Val::toInt('foo');
            })->toThrow(new \InvalidArgumentException('The given string could not be converted to an integer'));
            expect(function () {
                Val::toInt(curl_init());
            })->toThrow(new \InvalidArgumentException('The given resource could not be converted to an integer'));
            expect(function () {
                Val::toInt([]);
            })->toThrow(new \InvalidArgumentException('The given array could not be converted to an integer'));
        });

        it('allows you to specify an exception message', function () {
            expect(function () {
                Val::toInt('foo', 'my custom message');
            })->toThrow(new \InvalidArgumentException('my custom message'));
        });

        it('allows you to specify the exception to throw', function () {
            $myException = new \Exception('Foo');
            expect(function () use ($myException) {
                Val::toInt('foo', $myException);
            })->toThrow($myException);
        });
    });

    describe('::toFloat()', function () {

        it('converts intables to floats', function () {
            expect(Val::toFloat(-42))->toBe(-42.0);
            expect(Val::toFloat(PHP_INT_MAX))->toBe((float) PHP_INT_MAX);
            expect(Val::toFloat(1.0))->toBe(1.0);
            expect(Val::toFloat('-600.123'))->toBe(-600.123);
            expect(Val::toFloat(new \SimpleXmlElement('<r>-555.55</r>')))->toBe(-555.55);
        });

        it('throws up when trying to convert non-intables', function () {
            expect(function () {
                Val::toFloat('foo');
            })->toThrow(new \InvalidArgumentException('The given string could not be converted to a float'));
            expect(function () {
                Val::toFloat(curl_init());
            })->toThrow(new \InvalidArgumentException('The given resource could not be converted to a float'));
            expect(function () {
                Val::toFloat([]);
            })->toThrow(new \InvalidArgumentException('The given array could not be converted to a float'));
        });

        it('allows you to specify an exception message', function () {
            expect(function () {
                Val::toFloat('foo', 'my custom message');
            })->toThrow(new \InvalidArgumentException('my custom message'));
        });

        it('allows you to specify the exception to throw', function () {
            $myException = new \Exception('Foo');
            expect(function () use ($myException) {
                Val::toFloat('foo', $myException);
            })->toThrow($myException);
        });
    });
});
