<?php

namespace Kahlan\Spec\Suite;

use DateTime;
use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;

describe('FileSystemCheckProvider', function () {
    $subject = new \Valit\Providers\FileSystemCheckProvider();

    // override existence of fileWithDate and missingFile
    File::override(FileInfo::custom('fileWithDate', [
        'createdAt' => DateTime::createFromFormat('U', 0),
        'modifiedAt' => DateTime::createFromFormat('U', 0),
        'accessedAt' => DateTime::createFromFormat('U', 0),
    ]));
    File::override(FileInfo::custom('missingFile', [
        'exists' => false,
    ]));

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
            $result = $subject->checkCreatedAfter('fileWithDate', 0);
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
            expect($subject->checkCreatedAfter('fileWithDate', -1)->success())->toBe(true);
            expect($subject->checkCreatedAfter('fileWithDate', -PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkCreatedAfter('missingFile', -PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkCreatedAfter('fileWithDate', 0)->success())->toBe(false);
            expect($subject->checkCreatedAfter('fileWithDate', 1)->success())->toBe(false);
            expect($subject->checkCreatedAfter('fileWithDate', PHP_INT_MAX)->success())->toBe(false);
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
            $result = $subject->checkCreatedBefore('fileWithDate', 0);
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
            expect($subject->checkCreatedBefore('fileWithDate', 1)->success())->toBe(true);
            expect($subject->checkCreatedBefore('fileWithDate', PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkCreatedBefore('missingFile', PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkCreatedBefore('fileWithDate', 0)->success())->toBe(false);
            expect($subject->checkCreatedBefore('fileWithDate', -1)->success())->toBe(false);
            expect($subject->checkCreatedBefore('fileWithDate', -PHP_INT_MAX)->success())->toBe(false);
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
            $result = $subject->checkModifiedAfter('fileWithDate', 0);
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
            expect($subject->checkModifiedAfter('fileWithDate', -1)->success())->toBe(true);
            expect($subject->checkModifiedAfter('fileWithDate', -PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkModifiedAfter('missingFile', -PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkModifiedAfter('fileWithDate', 0)->success())->toBe(false);
            expect($subject->checkModifiedAfter('fileWithDate', 1)->success())->toBe(false);
            expect($subject->checkModifiedAfter('fileWithDate', PHP_INT_MAX)->success())->toBe(false);
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
            $result = $subject->checkModifiedBefore('fileWithDate', 0);
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
            expect($subject->checkModifiedBefore('fileWithDate', 1)->success())->toBe(true);
            expect($subject->checkModifiedBefore('fileWithDate', PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkModifiedBefore('missingFile', PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkModifiedBefore('fileWithDate', 0)->success())->toBe(false);
            expect($subject->checkModifiedBefore('fileWithDate', -1)->success())->toBe(false);
            expect($subject->checkModifiedBefore('fileWithDate', -PHP_INT_MAX)->success())->toBe(false);
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
            $result = $subject->checkAccessedAfter('fileWithDate', 0);
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
            expect($subject->checkAccessedAfter('fileWithDate', -1)->success())->toBe(true);
            expect($subject->checkAccessedAfter('fileWithDate', -PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkAccessedAfter('missingFile', -PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkAccessedAfter('fileWithDate', 0)->success())->toBe(false);
            expect($subject->checkAccessedAfter('fileWithDate', 1)->success())->toBe(false);
            expect($subject->checkAccessedAfter('fileWithDate', PHP_INT_MAX)->success())->toBe(false);
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
            $result = $subject->checkAccessedBefore('fileWithDate', 0);
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
            expect($subject->checkAccessedBefore('fileWithDate', 1)->success())->toBe(true);
            expect($subject->checkAccessedBefore('fileWithDate', PHP_INT_MAX)->success())->toBe(true);

            expect($subject->checkAccessedBefore('missingFile', PHP_INT_MAX)->success())->toBe(false);
            expect($subject->checkAccessedBefore('fileWithDate', 0)->success())->toBe(false);
            expect($subject->checkAccessedBefore('fileWithDate', -1)->success())->toBe(false);
            expect($subject->checkAccessedBefore('fileWithDate', -PHP_INT_MAX)->success())->toBe(false);
        });
    });
});
