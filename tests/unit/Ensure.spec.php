<?php

namespace Kahlan\Spec\Suite;

use Valit\Check;
use Valit\Logic;
use Valit\Ensure;
use Valit\Manager;
use Valit\Validators\ValueValidator;
use Valit\Validators\ContainerValidator;
use Valit\Exceptions\InvalidValueException;

describe('Ensure', function () {
    describe('::that()', function () {
        it('creates ValueValidator', function () {
            $validator = Ensure::that(42);
            expect($validator)->toBeAnInstanceOf(ValueValidator::class);
            expect($validator->value())->toBe(42);
            expect($validator->throwOnFailure)->toBe(true);
        });
    });

    describe('::oneOf()', function () {
        it('creates a ValueValidator', function () {
            expect(
                function () {
                    expect(
                        Ensure::oneOf([
                            Check::that('foo')->isString(),
                            Check::that('bar')->isInt(),
                        ])
                    )->toBeAnInstanceOf(ValueValidator::class);
                }
            )->not->toThrow();
        });

        it('throws an execption if a value is required', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat', 'isString'];
                Ensure::oneOf($scenarios);
            })->toThrow();
        });

        it('accepts a value as a second parameter', function () {
            $scenarios = ['isInt', 'isFloat', 'isString'];
            expect(Ensure::oneOf($scenarios, 42))->toBeAnInstanceOf(ValueValidator::class);
        });

        it('throws an exception if no scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat'];
                Ensure::oneOf($scenarios, 'not numeric');
            })->toThrow();
        });
        it('throws an exception if more than 1 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::oneOf($scenarios, 42);
            })->toThrow();
        });
        it('does not throw if exactly one scenario passes', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::oneOf($scenarios, 1987);
            })->not->toThrow();
        });
    });

    describe('::allOf()', function () {
        it('creates a ValueValidator', function () {
            expect(
                function () {
                    expect(
                        Ensure::allOf([
                            Check::that('foo')->isString(),
                            Check::that('bar')->isString(),
                        ])
                    )->toBeAnInstanceOf(ValueValidator::class);
                }
            )->not->toThrow();
        });

        it('throws an execption if a value is required', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat', 'isString'];
                Ensure::allOf($scenarios);
            })->toThrow();
        });

        it('accepts a value as a second parameter', function () {
            $scenarios = ['isInt'];
            expect(Ensure::allOf($scenarios, 42))->toBeAnInstanceOf(ValueValidator::class);
        });
        it('throws an exception if 0/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat'];
                Ensure::allOf($scenarios, 'not numeric');
            })->toThrow();
        });
        it('throws an exception of 1/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::allOf($scenarios, 1987);
            })->toThrow();
        });
        it('does not throw if 2/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::allOf($scenarios, 42);
            })->not->toThrow();
        });
        it('does not throw if 0/0 scenarios pass', function () {
            expect(function () {
                $scenarios = [];
                Ensure::allOf($scenarios);
            })->not->toThrow();
        });
    });

    describe('::anyOf()', function () {
        it('creates a ValueValidator', function () {
            expect(
                function () {
                    expect(
                        Ensure::anyOf([
                            Check::that('foo')->isString(),
                            Check::that('bar')->isString(),
                        ])
                    )->toBeAnInstanceOf(ValueValidator::class);
                }
            )->not->toThrow();
        });

        it('throws an execption if a value is required', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat', 'isString'];
                Ensure::anyOf($scenarios);
            })->toThrow();
        });

        it('accepts a value as a second parameter', function () {
            $scenarios = ['isInt'];
            expect(Ensure::anyOf($scenarios, 42))->toBeAnInstanceOf(ValueValidator::class);
        });
        it('throws an exception if 0/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat'];
                Ensure::anyOf($scenarios, 'not numeric');
            })->toThrow();
        });
        it('does not throw if 1/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::anyOf($scenarios, 1987);
            })->not->toThrow();
        });
        it('does not throw if 2/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::anyOf($scenarios, 42);
            })->not->toThrow();
        });
        it('throws an exception if 0/0 scenarios pass', function () {
            expect(function () {
                $scenarios = [];
                Ensure::anyOf($scenarios);
            })->toThrow();
        });
    });

    describe('::noneOf()', function () {
        it('creates a ValueValidator', function () {
            expect(
                function () {
                    expect(
                        Ensure::noneOf([
                            Check::that('foo')->isInt(),
                            Check::that('bar')->isInt(),
                        ])
                    )->toBeAnInstanceOf(ValueValidator::class);
                }
            )->not->toThrow();
        });

        it('throws an execption if a value is required', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat', 'isString'];
                Ensure::noneOf($scenarios);
            })->toThrow();
        });

        it('accepts a value as a second parameter', function () {
            $scenarios = ['isString'];
            expect(Ensure::noneOf($scenarios, 42))->toBeAnInstanceOf(ValueValidator::class);
        });
        it('does not throw if 0/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isFloat'];
                Ensure::noneOf($scenarios, 'not numeric');
            })->not->toThrow();
        });
        it('throws an exception if 1/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::noneOf($scenarios, 1987);
            })->toThrow();
        });
        it('throws an exception if 2/2 scenarios pass', function () {
            expect(function () {
                $scenarios = ['isInt', 'isEven'];
                Ensure::noneOf($scenarios, 42);
            })->toThrow();
        });
        it('does not throw if 0/0 scenarios pass', function () {
            expect(function () {
                $scenarios = [];
                Ensure::noneOf($scenarios);
            })->not->toThrow();
        });
    });

    describe('::not()', function () {
        it('creates a ValueValidator', function () {
            expect(
                function () {
                    expect(Ensure::not(false))->toBeAnInstanceOf(ValueValidator::class);
                }
            )->not->toThrow();
        });

        it('throws an execption if a value is required', function () {
            expect(function () {
                Ensure::not('isInt');
            })->toThrow();
        });

        it('accepts a value as a second parameter', function () {
            expect(Ensure::not('isString', 42))->toBeAnInstanceOf(ValueValidator::class);
        });
        it('does not throw if scenario fails', function () {
            expect(function () {
                Ensure::not('isInt', 'not an integer');
            })->not->toThrow();
        });
        it('throws an exception if scenario passes', function () {
            expect(function () {
                Ensure::not('isInt', 1987);
            })->toThrow();
        });
    });

    describe('::ifThen()', function () {
        it('forwards calls to ifThenElse', function () {
            expect(Ensure::class)->toReceive('::ifThenElse')->with(true, true, true, 'foo-bar-baz');
            Ensure::ifThen(true, true, 'foo-bar-baz');
        });
        it('creates a ValueValidator', function () {
            expect(
                Ensure::ifThen(true, true, 'foo-bar-baz')
            )->toBeAnInstanceOf(ValueValidator::class);
        });
    });

    describe('::ifThenElse()', function () {
        it('creates a ValueValidator', function () {
            expect(
                Ensure::ifThenElse(true, true, true, 'foo-bar-baz')
            )->toBeAnInstanceOf(ValueValidator::class);
        });

        it('evaluates the $then scenario if $condition passes', function () {
            expect(function () {
                Ensure::ifThenElse(true, false, true);
            })->toThrow();
            expect(function () {
                Ensure::ifThenElse(true, false, false);
            })->toThrow();
            expect(function () {
                Ensure::ifThenElse(true, true, true);
            })->not->toThrow();
            expect(function () {
                Ensure::ifThenElse(true, true, false);
            })->not->toThrow();
        });
        it('evaluates the $else scenario if $condition does not pass', function () {
            expect(function () {
                Ensure::ifThenElse(false, false, false);
            })->toThrow();
            expect(function () {
                Ensure::ifThenElse(false, true, false);
            })->toThrow();
            expect(function () {
                Ensure::ifThenElse(false, false, true);
            })->not->toThrow();
            expect(function () {
                Ensure::ifThenElse(false, true, true);
            })->not->toThrow();
        });
    });
});
