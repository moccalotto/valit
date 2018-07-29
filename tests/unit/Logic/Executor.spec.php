<?php

namespace Kahlan\Spec\Suite;

use Valit\Value;
use Valit\Check;
use Valit\Manager;
use Valit\Util\Val;
use Valit\Logic\Executor;
use Valit\Result\ContainerResultBag;

describe('Executor', function () {

    it('exists', function () {
        expect(class_exists(Executor::class))->toBe(true);
    });

    describe('::__construct()', function () {

        it('is initializable with zero scenarios', function () {
            expect(
                new Executor(Manager::instance(), [])
            )->toBeAnInstanceOf(Executor::class);
        });

        it('is initializable with many scenarios', function () {
            expect(
                new Executor(Manager::instance(), [true, false, 'greaterThan(0)', 'lessThan(100)'])
            )->toBeAnInstanceOf(Executor::class);
        });
    });

    describe('::execute()', function () {
        it('can execute zero scenarios', function () {
            $subject = new Executor(Manager::instance(), []);

            $result = $subject->execute();

            expect($result)->toBeAn('array');
            expect($result)->toHaveLength(0);

            expect($result)->toBe($subject->results());
        });

        it('can execute many scenarios', function () {
            $subject = new Executor(Manager::instance(), [
                true,
                false,
                'greaterThan(2)',
                'greaterThan(100)',
                Check::that(true)->isTrue(),
                Value::isInt(),
                Check::ifThen(true, true),
            ]);

            $result = $subject->execute(true, 5);

            expect($result)->toBeAn('array');
            expect($result)->toHaveLength(7);
            expect(Val::isArrayOf($result, ContainerResultBag::class))->toBe(true);

            expect($result)->toBe($subject->results());

            expect($result[0]->success())->toBe(true);  // true is success
            expect($result[1]->success())->toBe(false); // false is a failure
            expect($result[2]->success())->toBe(true);  // 5 is bigger than 5
            expect($result[3]->success())->toBe(false); // 5 is not greater than than 100
            expect($result[4]->success())->toBe(true);  // 5 is an integer
            expect($result[5]->success())->toBe(true);  // the ifThen statement always evaluates to true.
        });
    });

    describe('__debugInfo()', function () {
        it('has a simple debugInfo without relations to the Manager', function () {
            $subject = new Executor(Manager::instance(), [
                true,
                false,
                'greaterThan(2)',
                'greaterThan(100)',
                Check::that(true)->isTrue(),
                Value::isInt(),
                Check::ifThen(true, true),
            ]);

            $subject->execute(true, 5);

            $debug = $subject->__debugInfo();

            expect($debug)->toContainKey('requires');
            expect($debug)->toContainKey('hasValue');
            expect($debug)->toContainKey('value');
            expect($debug)->toContainKey('scenarios');
            expect($debug)->toContainKey('results');

            expect($debug['requires'])->toBe(Executor::REQUIRES_VALUE);
            expect($debug['hasValue'])->toBe(true);
            expect($debug['value'])->toBe(5);
            expect($debug['scenarios'])->toBeAn('array');
            expect($debug['results'])->toBeAn('array');
        });
    });
});
