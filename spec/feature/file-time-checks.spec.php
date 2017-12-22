<?php

namespace Kahlan\Spec\Suite;

use Valit\Result\AssertionResult;

describe('FileSystemCheckProvider', function () {
    $subject = new \Valit\Providers\FileSystemCheckProvider();

    // ================================================================================
    // CREATION TIME
    // ================================================================================
    describe('checkCreatedAfter', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileNewerThan');
            expect($subject->provides())->toContainKey('isFileNewerThan');
            expect($subject->provides())->toContainKey('fileCreatedAfter');
            expect($subject->provides())->toContainKey('isFileCreatedAfter');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkCreatedAfter('goodFile', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exceptions if $date is incorrect', function () use ($subject) {
            expect(function () use ($subject) {
                $subject->checkCreatedAfter('foo', curl_init());
            })->toThrow(new \InvalidArgumentException());
            expect(function () use ($subject) {
                $subject->checkCreatedAfter('foo', 'this is not a parseable date');
            })->toThrow(new \InvalidArgumentException());
        });

        it('checks if file is created after a given date', function () use ($subject) {
            allow('filectime')->toBeCalled()->with('goodFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('badFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('goodFile')->andReturn(true);

            expect($subject->checkCreatedAfter('goodFile', -1)->success())->toBe(true);
            expect($subject->checkCreatedAfter('goodFile', -PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkCreatedAfter('badFile', -PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkCreatedAfter('goodFile', 0)->success())->toBe(false);
            expect($subject->checkCreatedAfter('goodFile', 1)->success())->toBe(false);
            expect($subject->checkCreatedAfter('goodFile', PHP_INT_MAX)->success())->toBe(false);
        });
    });

    describe('checkCreatedBefore', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileOlderThan');
            expect($subject->provides())->toContainKey('isFileOlderThan');
            expect($subject->provides())->toContainKey('fileCreatedBefore');
            expect($subject->provides())->toContainKey('isFileCreatedBefore');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkCreatedBefore('goodFile', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exceptions if $date is incorrect', function () use ($subject) {
            expect(function () use ($subject) {
                $subject->checkCreatedBefore('foo', curl_init());
            })->toThrow(new \InvalidArgumentException());
            expect(function () use ($subject) {
                $subject->checkCreatedBefore('foo', 'this is not a parseable date');
            })->toThrow(new \InvalidArgumentException());
        });

        it('checks if file is created before a given date', function () use ($subject) {
            allow('filectime')->toBeCalled()->with('goodFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('badFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('goodFile')->andReturn(true);

            expect($subject->checkCreatedBefore('goodFile', 1)->success())->toBe(true);
            expect($subject->checkCreatedBefore('goodFile', PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkCreatedBefore('badFile', PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkCreatedBefore('goodFile', 0)->success())->toBe(false);
            expect($subject->checkCreatedBefore('goodFile', -1)->success())->toBe(false);
            expect($subject->checkCreatedBefore('goodFile', -PHP_INT_MAX)->success())->toBe(false);
        });
    });

    // ================================================================================
    // MODIFICATION TIME
    // ================================================================================
    describe('checkModifiedAfter', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileNewerThan');
            expect($subject->provides())->toContainKey('isFileNewerThan');
            expect($subject->provides())->toContainKey('fileModifiedAfter');
            expect($subject->provides())->toContainKey('isFileModifiedAfter');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkModifiedAfter('goodFile', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exceptions if $date is incorrect', function () use ($subject) {
            expect(function () use ($subject) {
                $subject->checkModifiedAfter('foo', curl_init());
            })->toThrow(new \InvalidArgumentException());
            expect(function () use ($subject) {
                $subject->checkModifiedAfter('foo', 'this is not a parseable date');
            })->toThrow(new \InvalidArgumentException());
        });

        it('checks if file is modified after a given date', function () use ($subject) {
            allow('filemtime')->toBeCalled()->with('goodFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('badFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('goodFile')->andReturn(true);

            expect($subject->checkModifiedAfter('goodFile', -1)->success())->toBe(true);
            expect($subject->checkModifiedAfter('goodFile', -PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkModifiedAfter('badFile', -PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkModifiedAfter('goodFile', 0)->success())->toBe(false);
            expect($subject->checkModifiedAfter('goodFile', 1)->success())->toBe(false);
            expect($subject->checkModifiedAfter('goodFile', PHP_INT_MAX)->success())->toBe(false);
        });
    });

    describe('checkModifiedBefore', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileOlderThan');
            expect($subject->provides())->toContainKey('isFileOlderThan');
            expect($subject->provides())->toContainKey('fileModifiedBefore');
            expect($subject->provides())->toContainKey('isFileModifiedBefore');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkModifiedBefore('goodFile', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exceptions if $date is incorrect', function () use ($subject) {
            expect(function () use ($subject) {
                $subject->checkModifiedBefore('foo', curl_init());
            })->toThrow(new \InvalidArgumentException());
            expect(function () use ($subject) {
                $subject->checkModifiedBefore('foo', 'this is not a parseable date');
            })->toThrow(new \InvalidArgumentException());
        });

        it('checks if file is modified before a given date', function () use ($subject) {
            allow('filemtime')->toBeCalled()->with('goodFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('badFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('goodFile')->andReturn(true);

            expect($subject->checkModifiedBefore('goodFile', 1)->success())->toBe(true);
            expect($subject->checkModifiedBefore('goodFile', PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkModifiedBefore('badFile', PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkModifiedBefore('goodFile', 0)->success())->toBe(false);
            expect($subject->checkModifiedBefore('goodFile', -1)->success())->toBe(false);
            expect($subject->checkModifiedBefore('goodFile', -PHP_INT_MAX)->success())->toBe(false);
        });
    });

    // ================================================================================
    // ACCESS TIME
    // ================================================================================
    describe('checkAccessedAfter', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileNewerThan');
            expect($subject->provides())->toContainKey('isFileNewerThan');
            expect($subject->provides())->toContainKey('fileAccessedAfter');
            expect($subject->provides())->toContainKey('isFileAccessedAfter');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkAccessedAfter('goodFile', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exceptions if $date is incorrect', function () use ($subject) {
            expect(function () use ($subject) {
                $subject->checkAccessedAfter('foo', curl_init());
            })->toThrow(new \InvalidArgumentException());
            expect(function () use ($subject) {
                $subject->checkAccessedAfter('foo', 'this is not a parseable date');
            })->toThrow(new \InvalidArgumentException());
        });

        it('checks if file is accessed after a given date', function () use ($subject) {
            allow('fileatime')->toBeCalled()->with('goodFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('badFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('goodFile')->andReturn(true);

            expect($subject->checkAccessedAfter('goodFile', -1)->success())->toBe(true);
            expect($subject->checkAccessedAfter('goodFile', -PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkAccessedAfter('badFile', -PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkAccessedAfter('goodFile', 0)->success())->toBe(false);
            expect($subject->checkAccessedAfter('goodFile', 1)->success())->toBe(false);
            expect($subject->checkAccessedAfter('goodFile', PHP_INT_MAX)->success())->toBe(false);
        });
    });

    describe('checkAccessedBefore', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileOlderThan');
            expect($subject->provides())->toContainKey('isFileOlderThan');
            expect($subject->provides())->toContainKey('fileAccessedBefore');
            expect($subject->provides())->toContainKey('isFileAccessedBefore');
        });

        it('returns correct type', function () use ($subject) {
            $result = $subject->checkAccessedBefore('goodFile', 0);
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('throws exceptions if $date is incorrect', function () use ($subject) {
            expect(function () use ($subject) {
                $subject->checkAccessedBefore('foo', curl_init());
            })->toThrow(new \InvalidArgumentException());
            expect(function () use ($subject) {
                $subject->checkAccessedBefore('foo', 'this is not a parseable date');
            })->toThrow(new \InvalidArgumentException());
        });

        it('checks if file is accessed before a given date', function () use ($subject) {
            allow('fileatime')->toBeCalled()->with('goodFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('badFile')->andReturn(0);
            allow('file_exists')->toBeCalled()->with('goodFile')->andReturn(true);

            expect($subject->checkAccessedBefore('goodFile', 1)->success())->toBe(true);
            expect($subject->checkAccessedBefore('goodFile', PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkAccessedBefore('badFile', PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkAccessedBefore('goodFile', 0)->success())->toBe(false);
            expect($subject->checkAccessedBefore('goodFile', -1)->success())->toBe(false);
            expect($subject->checkAccessedBefore('goodFile', -PHP_INT_MAX)->success())->toBe(false);
        });
    });
});
