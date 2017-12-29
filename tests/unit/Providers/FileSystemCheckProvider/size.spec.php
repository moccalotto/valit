<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;

describe('FileSystemCheckProvider', function () {
    $subject = new \Valit\Providers\FileSystemCheckProvider();

    describe('checkLargerThan', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileLargerThan');
            expect($subject->provides())->toContainKey('isFileLargerThan');
            expect($subject->provides())->toContainKey('fileSizeGreaterThan');
        });

        it('return correct type', function () use ($subject) {
            $result = $subject->checkLargerThan('existingSizeFile', '1.00 kB');

            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        $tests = [
            ['1.00 kB',  1000, false],
            ['1.00 kB',  1001, true],
            ['1.00 KiB', 1024, false],
            ['1.00 KiB', 1025, true],

            ['2.00 kB',  2000, false],
            ['2.00 kB',  2001, true],
            ['2.00 KiB', 2048, false],
            ['2.00 KiB', 2049, true],

            ['1.00 MB',  1000000, false],
            ['1.00 MB',  1000001, true],
            ['1.00 MiB', 1024 * 1024, false],
            ['1.00 MiB', 1024 * 1024 + 1, true],

            ['2.00 MB',  2000000, false],
            ['2.00 MB',  2000001, true],
            ['2.00 MiB', 2 * 1024 * 1024, false],
            ['2.00 MiB', 2 * 1024 * 1024 + 1, true],

            ['1.00 GB',  1000000000, false],
            ['1.00 GB',  1000000001, true],
            ['1.00 GiB', 1024 * 1024 * 1024, false],
            ['1.00 GiB', 1024 * 1024 * 1024 + 1, true],

            ['2.00 GB',  2000000000, false],
            ['2.00 GB',  2000000001, true],
            ['2.00 GiB', 2 * 1024 * 1024 * 1024, false],
            ['2.00 GiB', 2 * 1024 * 1024 * 1024 + 1, true],
        ];

        foreach ($tests as list ($againstSize, $actualSize, $expectedResult)) {
            $status = $expectedResult ? 'succeeds' : 'fails';
            $message = "$status when file of $actualSize bytes is larger than $againstSize";
            $test = function () use ($subject, $againstSize, $actualSize, $expectedResult) {
                File::mock(FileInfo::custom('existingSizeFile', ['size' => $actualSize ]));
                File::mock(FileInfo::custom('missingFile', ['exists' => false]));

                $result = $subject->checkLargerThan('existingSizeFile', $againstSize);
                expect($result->success())->toBe($expectedResult);

                $result = $subject->checkLargerThan('missingFile', $againstSize);
                expect($result->success())->toBe(false);
            };

            it($message, $test);
        }
    });

    describe('checkSmallerThan', function () use ($subject) {
        it('provides the correct assertions', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
            expect($subject->provides())->toContainKey('fileSmallerThan');
            expect($subject->provides())->toContainKey('isFileSmallerThan');
            expect($subject->provides())->toContainKey('fileSizeLessThan');
        });

        it('return correct type', function () use ($subject) {
            $result = $subject->checkSmallerThan('existingSizeFile', '1.00 kB');

            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        $tests = [
            ['1.00 kB',  1000, false],
            ['1.00 kB',  999, true],
            ['1.00 KiB', 1024, false],
            ['1.00 KiB', 1023, true],

            ['2.00 kB',  2000, false],
            ['2.00 kB',  1999, true],
            ['2.00 KiB', 2048, false],
            ['2.00 KiB', 2047, true],

            ['1.00 MB',  1000000, false],
            ['1.00 MB',  999999, true],
            ['1.00 MiB', 1024 * 1024, false],
            ['1.00 MiB', 1024 * 1024 - 1, true],

            ['2.00 MB',  2000000, false],
            ['2.00 MB',  1999999, true],
            ['2.00 MiB', 2 * 1024 * 1024, false],
            ['2.00 MiB', 2 * 1024 * 1024 - 1, true],

            ['1.00 GB',  1000000000, false],
            ['1.00 GB',  999999999, true],
            ['1.00 GiB', 1024 * 1024 * 1024, false],
            ['1.00 GiB', 1024 * 1024 * 1024 - 1, true],

            ['2.00 GB',  2000000000, false],
            ['2.00 GB',  1999999999, true],
            ['2.00 GiB', 2 * 1024 * 1024 * 1024, false],
            ['2.00 GiB', 2 * 1024 * 1024 * 1024 - 1, true],
        ];

        foreach ($tests as list ($againstSize, $actualSize, $expectedResult)) {
            $status = $expectedResult ? 'succeeds' : 'fails';
            $message = "$status when file of $actualSize bytes is smaller than $againstSize";
            $test = function () use ($subject, $againstSize, $actualSize, $expectedResult) {
                File::mock(FileInfo::custom('existingSizeFile', ['size' => $actualSize ]));
                File::mock(FileInfo::custom('missingFile', ['exists' => false]));

                $result = $subject->checkSmallerThan('existingSizeFile', $againstSize);

                expect($result->success())->toBe($expectedResult);
            };

            it($message, $test);
        }
    });
});
