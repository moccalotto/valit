<?php

namespace Kahlan\Spec\Suite;

use Valit\Manager;
use Valit\Assertion\AssertionBag;
use Valit\Validators\ValueValidator;
use Valit\Validators\ContainerValidator;

describe('Manager', function () {
    describe('::instance()', function () {
        it('creates a new instance on the first call', function () {
            Manager::$instance = null;
            expect(Manager::instance())->toBeAnInstanceOf(Manager::class);
        });
        it('contains a singleton', function () {
            expect(Manager::instance())->toBe(Manager::instance());
        });
    });

    describe('::create()', function () {
        it('creates a new instance', function () {
            expect(Manager::create())->toBeAnInstanceOf(Manager::class);
        });

        it('takes an array of additional providers', function () {
            expect(function () {
                Manager::create([]);
            })->not->toThrow();

            expect(function () {
                Manager::create(['Valit\Providers\BasicCheckProvider']);
            })->not->toThrow();

            expect(function () {
                Manager::create(['class does not exist']);
            })->toThrow();
        });
    });

    describe('::__construct()', function () {
        it('takes an array of providers', function () {
            expect(function () {
                return new Manager([]);
            })->not->toThrow();

            expect(function () {
                return new Manager(['Valit\Providers\BasicCheckProvider']);
            })->not->toThrow();
        });

        it('ensures that providers exist', function () {
            expect(function () {
                return new Manager(['some class that does not exist']);
            })->toThrow();
        });

        it('ensures that providers implement the CheckProvider contract', function () {
            expect(function () {
                return new Manager(['SimpleXMLElement']);
            })->toThrow();
        });
    });

    describe('::addProvider()', function () {
        it('takes an array of providers', function () {
            expect(function () {
                Manager::instance()->addProvider(
                    new \Valit\Providers\BasicCheckProvider()
                );
            })->not->toThrow();
        });
    });

    describe('::setGlobal()', function () {
        it('overwrites the global singleton', function () {
            $newInstance = Manager::create();

            expect($newInstance)->not->toBe(Manager::instance());

            $newInstance->setGlobal();

            expect($newInstance)->toBe(Manager::instance());
        });

        it('returns $this', function () {
            expect(
                Manager::instance()->setGlobal()
            )
            ->toBe(Manager::instance());
        });
    });

    describe('::checks()', function () {
        it('returns an array', function () {
            expect(Manager::instance()->checks())->toBeAn('array');
        });

        it('returns set of CheckInfo objects', function () {
            $checks = Manager::instance()->checks();
            expect(
                \Valit\Util\Val::is($checks, 'Valit\Util\CheckInfo[]')
            )->toBe(true);
        });
    });

    describe('::hasCheck()', function () {
        it('returns bool', function () {
            expect(Manager::instance()->hasCheck('foo'))->toBeA('bool');
        });

        it('returns true if a given check exists', function () {
            $checks = Manager::instance()->checks();

            $checkName = $checks[0]->aliases[0];

            expect(Manager::instance()->hasCheck($checkName))->toBe(true);
        });

        it('returns false if a given check does not exist', function () {
            $checkName = '-- check does not exist --';

            expect(Manager::instance()->hasCheck($checkName))->toBe(false);
        });
    });

    describe('::__debugInfo()', function () {
        it('returns an array', function () {
            expect(Manager::instance()->__debugInfo())->toBeAn('array');
        });
        it('contains checkCount', function () {
            expect(Manager::instance()->__debugInfo())->toContainKey('checkCount');
        });
    });

    describe('::executeCheck()', function () {
        it('returns an AssertionResult', function () {
            expect(
                Manager::instance()->executeCheck('isInt', 42, [])
            )
            ->toBeAnInstanceOf('Valit\Result\AssertionResult');
        });

        it('throws an exception if the given check does not exist', function () {
            $closure = function () {
                $checkName = '-- check does not exist --';
                Manager::instance()->executeCheck($checkName, 'foo', ['args']);
            };

            expect($closure)->toThrow(new \UnexpectedValueException());
        });
    });
});
