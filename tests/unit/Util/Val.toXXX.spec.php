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

        it('converts numerics to floats', function () {
            expect(Val::toFloat(-42))->toBe(-42.0);
            expect(Val::toFloat(PHP_INT_MAX))->toBe((float) PHP_INT_MAX);
            expect(Val::toFloat(1.0))->toBe(1.0);
            expect(Val::toFloat('-600.123'))->toBe(-600.123);
            expect(Val::toFloat(new \SimpleXmlElement('<r>-555.55</r>')))->toBe(-555.55);
        });

        it('throws up when trying to convert non-numerics', function () {
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

    describe('::toArray()', function () {

        it('converts iterables to arrays', function () {
            expect(Val::toArray([]))->toBe([]);
            expect(Val::toArray([-42]))->toBe([-42]);
            expect(Val::toArray(new \ArrayIterator([-42])))->toBe([-42]);
            expect(Val::toArray(new \ArrayObject([-42])))->toBe([-42]);
            expect(Val::toArray(new \EmptyIterator()))->toBe([]);
        });

        it('throws up when trying to convert non-iterables', function () {
            expect(function () {
                Val::toArray('foo');
            })->toThrow(new \InvalidArgumentException('The given value must be a iterable'));
            expect(function () {
                Val::toArray(curl_init());
            })->toThrow(new \InvalidArgumentException('The given value must be a iterable'));
            expect(function () {
                Val::toArray((object) []);
            })->toThrow(new \InvalidArgumentException('The given value must be a iterable'));
        });

        it('allows you to specify an exception message', function () {
            expect(function () {
                Val::toArray('foo', 'my custom message');
            })->toThrow(new \InvalidArgumentException('my custom message'));
        });

        it('allows you to specify the exception to throw', function () {
            $myException = new \Exception('Foo');
            expect(function () use ($myException) {
                Val::toArray('foo', $myException);
            })->toThrow($myException);
        });
    });

    describe('toString', function () {
        it('converts stringables to strings', function () {
            expect(Val::toString('foo'))->toBe('foo');
            expect(Val::toString(-42))->toBe('-42');
            expect(Val::toString(1.23))->toBe('1.23');
            expect(Val::toString(new \SimpleXmlElement('<r>FOO</r>')))->toBe('FOO');
        });

        it('throws up when trying to convert non-stringables', function () {
            expect(function () {
                Val::toString([]);
            })->toThrow(new \InvalidArgumentException('The given array could not be converted to a string'));
            expect(function () {
                Val::toString(curl_init());
            })->toThrow(new \InvalidArgumentException('The given resource could not be converted to a string'));
            expect(function () {
                Val::toString((object) []);
            })->toThrow(new \InvalidArgumentException('The given object could not be converted to a string'));
        });

        it('allows you to specify an exception message', function () {
            expect(function () {
                Val::toString(['foo'], 'my custom message');
            })->toThrow(new \InvalidArgumentException('my custom message'));
        });

        it('allows you to specify the exception to throw', function () {
            $myException = new \Exception('Foo');
            expect(function () use ($myException) {
                Val::toString(null, $myException);
            })->toThrow($myException);
        });
    });

    describe('toBool', function () {

        it('converts values to bools if possible', function () {
            expect(Val::toBool(1))->toBe(true);
            expect(Val::toBool(1.0))->toBe(true);
            expect(Val::toBool('1'))->toBe(true);
            expect(Val::toBool(true))->toBe(true);
            expect(Val::toBool('1.0'))->toBe(true);
            expect(Val::toBool('true'))->toBe(true);
            expect(Val::toBool(new \SimpleXmlElement('<r>true</r>')))->toBe(true);

            expect(Val::toBool(0))->toBe(false);
            expect(Val::toBool(0.0))->toBe(false);
            expect(Val::toBool('0'))->toBe(false);
            expect(Val::toBool('0.0'))->toBe(false);
            expect(Val::toBool(false))->toBe(false);
            expect(Val::toBool('false'))->toBe(false);
            expect(Val::toBool(new \SimpleXmlElement('<r>false</r>')))->toBe(false);
        });

        it('throws up when trying to convert non-boolables', function () {
            expect(function () {
                Val::toBool('not true');
            })->toThrow(new \InvalidArgumentException('The given string could not be converted to a boolean'));
            expect(function () {
                Val::toBool(42);
            })->toThrow(new \InvalidArgumentException('The given integer could not be converted to a boolean'));
            expect(function () {
                Val::toBool((object) []);
            })->toThrow(new \InvalidArgumentException('The given object could not be converted to a boolean'));
        });

        it('allows you to specify an exception message', function () {
            expect(function () {
                Val::toBool('foo', 'my custom message');
            })->toThrow(new \InvalidArgumentException('my custom message'));
        });

        it('allows you to specify the exception to throw', function () {
            $myException = new \Exception('Foo');
            expect(function () use ($myException) {
                Val::toBool(null, $myException);
            })->toThrow($myException);
        });
    });

    describe('toClosure', function () {
        it('converts callables to closures', function () {
            // create an inv
            eval('class Invokable { function __invoke() { return "foo"; } }');

            expect(Val::toClosure('intval'))->toBeAnInstanceOf('Closure');
            expect(Val::toClosure([Val::class, 'toClosure']))->toBeAnInstanceOf('Closure');
            expect(Val::toClosure('Valit\Check::that'))->toBeAnInstanceOf('Closure');
            expect(Val::toClosure(function () {
                return 'foo';
            }))->toBeAnInstanceOf('Closure');
            expect(Val::toClosure(new \Invokable))->toBeAnInstanceOf('Closure');
        });

        it('throws up when trying to non-callables', function () {
            expect(function () {
                Val::toClosure('not true');
            })->toThrow(new \InvalidArgumentException('The given string could not be converted to a Closure'));
            expect(function () {
                Val::toClosure(42);
            })->toThrow(new \InvalidArgumentException('The given integer could not be converted to a Closure'));
            expect(function () {
                Val::toClosure((object) []);
            })->toThrow(new \InvalidArgumentException('The given object could not be converted to a Closure'));
        });

        it('allows you to specify an exception message', function () {
            expect(function () {
                Val::toClosure('foo', 'my custom message');
            })->toThrow(new \InvalidArgumentException('my custom message'));
        });

        it('allows you to specify the exception to throw', function () {
            $myException = new \Exception('Foo');
            expect(function () use ($myException) {
                Val::toClosure(null, $myException);
            })->toThrow($myException);
        });
    });
});
