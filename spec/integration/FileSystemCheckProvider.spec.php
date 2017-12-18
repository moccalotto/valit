<?php

namespace Kahlan\Spec\Suite;

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
            $result = $subject->checkLargerThan('fooFile', '1.00 kB');

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

        foreach ($tests as list ($wantedSize, $actualSize, $expectedResult)) {
            $status = $expectedResult ? 'succeeds' : 'fails';
            $message = "$status when file of $actualSize bytes is validated against $wantedSize";
            $test = function () use ($subject, $wantedSize, $actualSize, $expectedResult) {
                allow('is_file')->toBeCalled()->with('fooFile')->andReturn(true);
                allow('filesize')->toBeCalled()->with('fooFile')->andReturn($actualSize);

                $result = $subject->checkLargerThan('fooFile', $wantedSize);

                expect($result->success())->toBe($expectedResult);
            };

            it($message, $test);
        }
    });
});
